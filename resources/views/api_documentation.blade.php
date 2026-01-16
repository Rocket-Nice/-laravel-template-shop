<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>API Documentation</title>
{{--  <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css">--}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.20.2/swagger-ui.css" integrity="sha512-KBb32o+GN4eR1g6fWk0qpYDRaRe1Gh3nCGUh63HpW0lQF1krZGYwlb1MKOsjLrFHIjcDKTGCok3rA6/5ru71vg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
{{--  <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js"></script>--}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/swagger-ui/5.20.2/swagger-ui-bundle.js" integrity="sha512-8ikdReA469tD99k5RDPZcMKjApNzSfCCG5Du+Zd39rBvTFNVE1JqLEzd1oSBFbixNFxX5PIgqCGl8ucgHYWdLA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
<div id="swagger-ui"></div>
<script>
  window.onload = function() {
    SwaggerUIBundle({
      url: @json(asset('/api-doc/openapi-1.json?1')), // Путь к вашему swagger.json файлу
      dom_id: '#swagger-ui',
      deepLinking: true,
      presets: [
        SwaggerUIBundle.presets.apis,
        SwaggerUIBundle.SwaggerUIStandalonePreset
      ],
      layout: "BaseLayout"
    });
  }
</script>
</body>
</html>
