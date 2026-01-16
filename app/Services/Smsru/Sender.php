<?php

namespace App\Services\Smsru;

use Illuminate\Support\Facades\Log;
use stdClass;

class Sender
{
    private $smsru;
    private $api = '862879D8-A9E5-D7BF-60A7-469AA5A91BD0';
    public function __construct(){
      $this->smsru = new SMSRU($this->api);
    }

    public function sendSms($phone, $text){
      $data = new stdClass();
      $data->to = $phone;
      $data->text = $text;
      try{
        $sms = $this->smsru->send_one($data);
        if ($sms->status == "OK") { // Запрос выполнен успешно
//          echo "Сообщение отправлено успешно. ";
//          echo "ID сообщения: $sms->sms_id. ";
//          echo "Ваш новый баланс: $sms->balance";
        } else {
          Log::debug("Сообщение не отправлено. \nКод ошибки: $sms->status_code. \nТекст ошибки: $sms->status_text.");
        }
      }catch (\Exception $exception){
        Log::debug('SMSRU '.$exception->getMessage());
      }
    }
}
