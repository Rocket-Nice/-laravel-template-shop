<?php


namespace App\Services\Telegram;


use App\Models\Setting;
use App\Services\Telegram\Entities\File;
use App\Services\Telegram\Entities\MediaGroup;
use App\Services\Telegram\Entities\Message;
use App\Services\Telegram\Entities\Photo;
use App\Services\Telegram\Entities\Video;
use App\Services\Telegram\Exception\PzlException;
use App\Services\WildberriesSDK\Entity\Incomes;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerAwareTrait;

class Client
{
  use LoggerAwareTrait;
  private $apiKey;
  private $url;
  private $httpClient;

  public function __construct($url = 'https://api.telegram.org/bot'){
    $this->apiKey = Setting::query()->where('key', 'tg_notifications_bot')->first()?->value;
    $this->url = $url.$this->apiKey.'/';
    $this->httpClient = new \GuzzleHttp\Client([
        'base_uri' => $this->url,
        'timeout' => 300,
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
        ]
    ]);
  }

  private function callApi($type, $method, $params = [])
  {
    switch ($type) {
      case 'GET':
        if ($this->logger) {
          $this->logger->info("Telegram API {$type} request {$method}: " . http_build_query($params));
        }
        $response = $this->httpClient->get($method, ['query' => $params]);
        break;
      case 'POST':
        if ($this->logger) {
          $this->logger->info("Telegram API {$type} request {$method}: " . json_encode($params));
        }
        $response = $this->httpClient->post($method, ['headers' => ['Content-Type' => 'application/json'], 'json' => $params]);
        break;
      case 'FILE':
        if ($this->logger) {
          $this->logger->info("Telegram API {$type} request {$method}: " . json_encode($params));
        }
        $response = $this->httpClient->post($method, ['multipart' => $params]);
        break;
    }

    $request = http_build_query($params);

    $json = $response->getBody()->getContents();

    if ($this->logger) {
      $headers = $response->getHeaders();
      $headers['http_status'] = $response->getStatusCode();
      $this->logger->info("Telegram API response {$method}: " . $json, $headers);
    }

    if ($response->getStatusCode() != 200)
      throw new PzlException('Неверный код ответа от сервера Telegram при вызове метода ' . $method . ': ' . $response->getStatusCode(), $response->getStatusCode(), $json, $request);

    $respBB = json_decode($json, true);

    if (empty($respBB))
      throw new PzlException('От сервера Telegram при вызове метода ' . $method . ' пришел пустой ответ', $response->getStatusCode(), $json, $request);

    if (!empty($respBB['err']))
      throw new PzlException('От сервера Telegram при вызове метода ' . $method . ' получена ошибка: ' . $respBB['err'], $response->getStatusCode(), $json, $request);


    if (!empty($respBB[0]['err']))
      throw new PzlException('От сервера Telegram при вызове метода ' . $method . ' получена ошибка: ' . $respBB[0]['err'], $response->getStatusCode(), $json, $request);

    return $respBB;
  }

  public function getMe(): array
  {
    try {
      $response = $this->callApi('GET', 'getMe');

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function getWebhookInfo(): array
  {
    try {
      $response = $this->callApi('GET', 'getWebhookInfo');

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function deleteWebhook(): array
  {
    try {
      $response = $this->callApi('GET', 'deleteWebhook');

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function setWebhook($url): array
  {
    try {
      $response = $this->callApi('GET', 'setWebhook', ['url' => $url]);

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function getUserProfilePhotos($user): array
  {
    try {
      $response = $this->callApi('GET', 'getUserProfilePhotos', ['user_id' => $user]);

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function getFile($file_id): array
  {
    try {
      $response = $this->callApi('GET', 'getFile', ['file_id' => $file_id]);

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function downloadFile($file_path, $save_path)
  {
    $save_path = storage_path('app/public/'.$save_path);
    $response = $this->httpClient->get($file_path);
    if ($response->getStatusCode() == 200) {
      if (!file_exists($save_path)) {
        mkdir($save_path, 0777, true);
      }

      $imageContent = $response->getBody()->getContents();

      $fileExtension = pathinfo($file_path, PATHINFO_EXTENSION);
      // Сохранение содержимого в файл
      file_put_contents($save_path.'/file'.time().'.'.$fileExtension, $imageContent);

      return $save_path.'/file'.time().'.'.$fileExtension;
    }
    return false;
  }
  public function sendMessage(Message $params): array
  {
    try {
      $response = $this->callApi('GET', 'sendMessage', $params->toArray());

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function sendPhoto(Photo $params): array
  {
    try {
      $response = $this->callApi('GET', 'sendPhoto', $params->toArray());

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function sendVideo(Video $params): array
  {
    try {
      if ($params->getAttach()){
        $response = $this->callApi('FILE', 'sendVideo', $params->toArray());
      }else{
        $response = $this->callApi('POST', 'sendVideo', $params->toArray());
      }
      

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function sendMediaGroup(MediaGroup $params): array
  {
    try {
      $response = $this->callApi('FILE', 'sendMediaGroup', $params->toArray());

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
  public function sendDocument(File $params): array
  {
    try {
      $response = $this->callApi('FILE', 'sendDocument', $params->toArray());

      return $response;
    } catch (RequestException $e) {
      // Обработка ошибок запроса
      if ($e->hasResponse()) {
        return json_decode($e->getResponse()->getBody(), true);
      }
      throw $e;
    }
  }
}
