<?php
/**
 * Health check endpoint for the application
 * 
 * This endpoint provides a lightweight health check without loading the full framework
 */

// Set the response content type to JSON
header('Content-Type: application/json');

try {
    // Check if the application files exist
    $requiredFiles = [
        __DIR__ . '/../bootstrap/app.php',
        __DIR__ . '/../vendor/autoload.php'
    ];
    
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            throw new Exception("Required file missing: " . basename($file));
        }
    }
    
    // Check environment configuration
    $envFile = __DIR__ . '/../.env';
    $envExample = __DIR__ . '/../.env.example';
    
    if (!file_exists($envFile) && !file_exists($envExample)) {
        throw new Exception("Environment configuration missing");
    }
    
    // Parse environment variables
    $envPath = file_exists($envFile) ? $envFile : $envExample;
    $envContent = file_get_contents($envPath);
    $envVars = [];
    
    foreach (explode("\n", $envContent) as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
    // Database connection check
    $dbChecks = [];
    if (isset($envVars['DB_CONNECTION'])) {
        $dbChecks['driver'] = $envVars['DB_CONNECTION'];
        
        // Only perform actual DB connection check for critical health monitoring
        // This is a simplified check - in production you might want to actually connect
        $dbRequired = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME'];
        $dbConfigComplete = true;
        
        foreach ($dbRequired as $required) {
            if (!isset($envVars[$required]) || empty($envVars[$required])) {
                $dbConfigComplete = false;
                break;
            }
        }
        
        $dbChecks['configured'] = $dbConfigComplete;
    }
    
    // Basic response
    $response = [
        'status' => 'ok',
        'timestamp' => date('c'),
        'service' => 'lemousse-shop',
        'checks' => [
            'app_files' => 'ok',
            'environment' => file_exists($envFile) ? 'ok' : 'using_example',
            'database' => $dbChecks
        ]
    ];
    
    http_response_code(200);
    echo json_encode($response);
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'timestamp' => date('c'),
        'service' => 'lemousse-shop',
        'message' => $e->getMessage()
    ];
    
    http_response_code(503);
    echo json_encode($response);
}