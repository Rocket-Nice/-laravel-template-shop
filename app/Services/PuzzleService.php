<?php

namespace App\Services;

use App\Models\Setting;
use App\Services\Telegram\Exception\TgException;
use Psr\Log\LoggerAwareTrait;

class PuzzleService
{
  use LoggerAwareTrait;
  private $login = 'admin3';
  private $password  = '&IqAX6P4ewe1o{33+[}p';
  private $token;
  private $url;
  private $httpClient;

  public function __construct($url = 'https://puzzle.lemousse.shop/'){
    $this->url = $url;
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
          $this->logger->info("puzzle.lemousse.shop API {$type} request {$method}: " . http_build_query($params));
        }
        $headers = [];
        if($this->token){
          $headers['Authorization'] = 'Bearer '.$this->token;
        }
        $response = $this->httpClient->get($method, [
            'headers' => $headers,
            'query' => $params
        ]);
        break;
      case 'POST':
        if ($this->logger) {
          $this->logger->info("puzzle.lemousse.shop API {$type} request {$method}: " . json_encode($params));
        }
        $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
        if($this->token){
          $headers['Authorization'] = 'Bearer '.$this->token;
        }
        $response = $this->httpClient->post($method, [
            'headers' => $headers,
            'form_params' => $params
        ]);
        break;
      case 'FILE':
        if ($this->logger) {
          $this->logger->info("puzzle.lemousse.shop API {$type} request {$method}: " . json_encode($params));
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
      throw new \Exception('Неверный код ответа от сервера puzzle.lemousse.shop при вызове метода ' . $method . ': ' . $response->getStatusCode(), $response->getStatusCode(), $json, $request);

    $respBB = json_decode($json, true);

    if (empty($respBB))
      throw new \Exception('От сервера puzzle.lemousse.shop при вызове метода ' . $method . ' пришел пустой ответ', $response->getStatusCode(), $json, $request);

    if (!empty($respBB['err']))
      throw new \Exception('От сервера puzzle.lemousse.shop при вызове метода ' . $method . ' получена ошибка: ' . $respBB['err'], $response->getStatusCode(), $json, $request);


    if (!empty($respBB[0]['err']))
      throw new \Exception('От сервера puzzle.lemousse.shop при вызове метода ' . $method . ' получена ошибка: ' . $respBB[0]['err'], $response->getStatusCode(), $json, $request);

    return $respBB;
  }
  public function getToken()
  {
    try {
      $getToken = $this->callApi('POST', 'token', [
          'username' => $this->login,
          'password' => $this->password,
      ]);
      $this->token = $getToken['access_token'];
      return true;
    } catch (\Exception $exception){
      return false;
    }
  }

  public function getMe()
  {
    try {
      $getToken = $this->callApi('GET', 'users/me');
      return $getToken;
    } catch (\Exception $exception){
      return $exception;
    }
  }

  public function createPrize($params)
  {
    try {
      $request = $this->callApi('POST', 'prizes', $params);
      return $request;
    } catch (\Exception $exception){
      return false;
    }
  }
}
