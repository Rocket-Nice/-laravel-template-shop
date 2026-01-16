<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
class TelegramChannel
{
  public function send($notifiable, Notification $notification)
  {
    $message = $notification->toTelegram($notifiable);

    // Логика отправки сообщения в Telegram
    // Например, использование HTTP клиента для отправки запроса к Telegram API
  }
}
