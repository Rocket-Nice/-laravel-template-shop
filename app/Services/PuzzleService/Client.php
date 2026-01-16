<?php

namespace App\Services\PuzzleService;

use App\Services\PuzzleService\Entities\File;
use App\Services\PuzzleService\Exception\PzlException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Psr\Log\LoggerAwareTrait;

class Client
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

        try {
          $response = $this->httpClient->get($method, [
              'headers' => $headers,
              'query' => $params
          ]);
        }catch (RequestException $e){
          return $e;
        }
        break;
      case 'POST':
        if ($this->logger) {
          $this->logger->info("puzzle.lemousse.shop API {$type} request {$method}: " . json_encode($params));
        }
        $ContentType = 'application/json';
        if($method == 'token'){
          $ContentType = 'application/x-www-form-urlencoded';
        }
        $headers = ['Content-Type' => $ContentType];
        if($this->token){
          $headers['Authorization'] = 'Bearer '.$this->token;
        }
//        if($method!='token'){
//          dd([
//              'headers' => $headers,
//              'form_params' => $params,
//              'debug' => true
//          ]);
//        }
        $responseParams = [
            'headers' => $headers
        ];
        if($ContentType == 'application/json'){
          $responseParams['json'] = $params;
        }else{
          $responseParams['form_params'] = $params;
        }
        try {
          $response = $this->httpClient->post($method, $responseParams);
        }catch (RequestException $e){
          return $e;
        }
        break;
      case 'PUT':
        if ($this->logger) {
          $this->logger->info("puzzle.lemousse.shop API {$type} request {$method}: " . json_encode($params));
        }
        $ContentType = 'application/json';
        if($method == 'token'){
          $ContentType = 'application/x-www-form-urlencoded';
        }
        $headers = ['Content-Type' => $ContentType];
        if($this->token){
          $headers['Authorization'] = 'Bearer '.$this->token;
        }
//        if($method!='token'){
//          dd([
//              'headers' => $headers,
//              'form_params' => $params,
//              'debug' => true
//          ]);
//        }
        $responseParams = [
            'headers' => $headers
        ];
        if($ContentType == 'application/json'){
          $responseParams['json'] = $params;
        }else{
          $responseParams['form_params'] = $params;
        }
        try {
          $response = $this->httpClient->put($method, $responseParams);
        }catch (RequestException $e){
          return $e;
        }
        break;
      case 'DELETE':
        if ($this->logger) {
          $this->logger->info("puzzle.lemousse.shop API {$type} request {$method}: " . json_encode($params));
        }
        $ContentType = 'application/json';
        if($method == 'token'){
          $ContentType = 'application/x-www-form-urlencoded';
        }
        $headers = ['Content-Type' => $ContentType];
        if($this->token){
          $headers['Authorization'] = 'Bearer '.$this->token;
        }
//        if($method!='token'){
//          dd([
//              'headers' => $headers,
//              'form_params' => $params,
//              'debug' => true
//          ]);
//        }
        $responseParams = [
            'headers' => $headers
        ];
        if($ContentType == 'application/json'){
          $responseParams['json'] = $params;
        }else{
          $responseParams['form_params'] = $params;
        }
        try {
          $response = $this->httpClient->delete($method, $responseParams);
        }catch (RequestException $e){
          return $e;
        }
        break;
      case 'FILE':
        if ($this->logger) {
          $this->logger->info("puzzle.lemousse.shop API {$type} request {$method}: " . json_encode($params));
        }
        try {
          $response = $this->httpClient->post($method, ['multipart' => $params]);
        }catch (RequestException $e){
          return $e;
        }
        break;
    }

    $request = http_build_query($params);

    $json = $response->getBody()->getContents();

    if ($this->logger) {
      $headers = $response->getHeaders();
      $headers['http_status'] = $response->getStatusCode();
      $this->logger->info("puzzle.lemousse.shop API response {$method}: " . $json, $headers);
    }

    if ($response->getStatusCode() != 200)
      throw new PzlException('Неверный код ответа от сервера puzzle.lemousse.shop при вызове метода ' . $method . ': ' . $response->getStatusCode(), $response->getStatusCode(), $json, $request);

    $respBB = json_decode($json, true);

    if (empty($respBB))
      throw new PzlException('От сервера puzzle.lemousse.shop при вызове метода ' . $method . ' пришел пустой ответ', $response->getStatusCode(), $json, $request);

    if (!empty($respBB['err']))
      throw new PzlException('От сервера puzzle.lemousse.shop при вызове метода ' . $method . ' получена ошибка: ' . $respBB['err'], $response->getStatusCode(), $json, $request);


    if (!empty($respBB[0]['err']))
      throw new PzlException('От сервера puzzle.lemousse.shop при вызове метода ' . $method . ' получена ошибка: ' . $respBB[0]['err'], $response->getStatusCode(), $json, $request);

    return $respBB;
  }
  public function getToken()
  {
    if ($this->isTokenValid()) {
      $this->token = Cache::get('api_token');
      return true;
    } else {
      try {
        $getToken = $this->callApi('POST', 'token', [
            'username' => $this->login,
            'password' => $this->password,
        ]);
        $this->token = $getToken['access_token'];
        $this->storeTokenInSession($getToken['access_token']);
        return true;
      } catch (PzlException $exception){
        return false;
      }
    }
  }

  public function isTokenValid()
  {
    if (Cache::has('api_token') && Cache::has('api_token_timestamp')) {

      $timestamp = Cache::get('api_token_timestamp');
      $currentTime = now();

      // Проверяем, прошло ли менее 30 минут с момента получения токена
      return $currentTime->diffInMinutes($timestamp) < 30;
    }
    return false;
  }

  public function storeTokenInSession($token)
  {
    // Сохраняем токен и текущую временную метку в сессии
    Cache::put('api_token', $token);
    Cache::put('api_token_timestamp', now());
  }

  public function getMe()
  {
    try {
      $response = $this->callApi('GET', 'users/me');
      return $response;
    } catch (PzlException $exception){
      return $exception;
    }
  }

  public function getPrizes($params = [])
  {
    try {
      $response = $this->callApi('GET', 'prizes/', $params);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function createPrize($params)
  {
    try {
      $response = $this->callApi('POST', 'prizes/', $params);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function updatePrize($id, $params)
  {
    try {
      $response = $this->callApi('PUT', 'prizes/'.$id, $params);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function getPrize($id)
  {
    try {
      $response = $this->callApi('GET', 'prizes/'.$id);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function deletePrize($id)
  {
    try {
      $response = $this->callApi('DELETE', 'prizes/'.$id);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function solvePuzzle(File $params)
  {
    try {
      $response = $this->callApi('FILE', 'solver/uploadimage', $params->toArray());
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function prizeByParticipant($id)
  {
    try {
      $response = $this->callApi('GET', 'prizes/by_member/'.$id);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function assignParticipant($id)
  {
    try {
      $response = $this->callApi('PUT', 'prizes/assign_first_prize/'.$id);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function resetPrizeParticipant($id)
  {
    try {
      $response = $this->callApi('PUT', 'prizes/reset_prize/'.$id);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
  public function prizeByParticipants($ids)
  {
    try {
      $response = $this->callApi('POST', 'prizes/by_members/', $ids);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }

  public function createParticipant($params)
  {
    try {
      $response = $this->callApi('POST', 'members/', $params);
      return $response;
    } catch (PzlException $exception){
      return false;
    }
  }
}
