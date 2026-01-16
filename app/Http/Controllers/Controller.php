<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

//      public function __construct(Request $request){
//        $currentDomain = parse_url(url('/'), PHP_URL_HOST);
//        if(trim($currentDomain) == 'le-mousse.ru'){
//          $this->checkUserAgent($request);
//        }
//      }
//
//  public function checkUserAgent(Request $request)
//  {
//
//    $userAgent = $request->header('User-Agent');
//
//    if (preg_match('/bot|crawl|slurp|spider|mediapartners/i', $userAgent)) {
//      // Это бот
//      abort(403, 'Bot detected');
//    }
//  }
//      public function __construct(){
//      $user = 'admin';  // Укажите имя пользователя
//      $pass = 'admin1';  // Укажите пароль
//
//      if (!isset($_SERVER['PHP_AUTH_USER']) ||
//          !isset($_SERVER['PHP_AUTH_PW']) ||
//          $_SERVER['PHP_AUTH_USER'] != $user ||
//          $_SERVER['PHP_AUTH_PW'] != $pass) {
//        header('WWW-Authenticate: Basic realm="My Private Area"');
//        header('HTTP/1.0 401 Unauthorized');
//        echo 'Требуется авторизация';  // Текст, который будет показан, если пользователь нажмёт "Отмена"
//        exit;
//      }
//    }
  public function __construct(){
    if (
        config('app.env') === 'local' &&
        isset($_SERVER['REQUEST_URI']) &&
        strpos($_SERVER['REQUEST_URI'], 'robokassa/check') === false
        && strpos($_SERVER['REQUEST_URI'], 'api/') === false
        && strpos($_SERVER['REQUEST_URI'], 'api2/') === false
        && strpos($_SERVER['REQUEST_URI'], 'laravel-filemanager') === false
    ) {
      $user = 'admin';  // Укажите имя пользователя
      $pass = 'admin11';  // Укажите пароль

      if (!isset($_SERVER['PHP_AUTH_USER']) ||
          !isset($_SERVER['PHP_AUTH_PW']) ||
          $_SERVER['PHP_AUTH_USER'] != $user ||
          $_SERVER['PHP_AUTH_PW'] != $pass) {
        header('WWW-Authenticate: Basic realm="My Private Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Требуется авторизация';  // Текст, который будет показан, если пользователь нажмёт "Отмена"
        exit;
      }
    }

  }
}
