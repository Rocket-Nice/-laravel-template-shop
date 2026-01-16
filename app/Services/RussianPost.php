<?php


namespace App\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RussianPost
{
  const URL = 'https://postprice.ru/engine';

  public function russia($params){
    return $this->request('russia', $params);
  }

  public function internation($params){
    return $this->request('international', $params);
  }

  private function request($url, $parameters = [], $timeout = 30){
    $headers = ['Content-Type: application/json'];
    $parameters['apikey'] = '39c6e912d709d97a2764fd552fceeea1';
    $ch = curl_init();

    $url .= '/api.php?' . http_build_query($parameters);
    curl_setopt($ch, CURLOPT_URL, self::URL . '/' . $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, false);

    if (count($headers)) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);

    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($errno) {
      Log::debug('RussianPost Запрос произвести не удалось: ' . $error . ' | ' .$errno);
    }

    return $response;
  }
}
