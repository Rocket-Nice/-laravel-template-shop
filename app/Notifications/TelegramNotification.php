<?php

namespace App\Notifications;

use App\Channels\TelegramChannel;
use App\Http\Controllers\Admin\TelegramController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TelegramNotification extends Notification implements ShouldQueue
{
    use Queueable;


    public $message;
    public $type;
    public $image;
    public $parse_mode;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
  public function __construct($message, $type = 'text_message', $parse_mode = null, $image = null, $queue = null)
  {
    $this->message = $message;
    $this->type = $type;
    $this->image = $image;
    $this->parse_mode = $parse_mode;
    $this->onQueue($queue ?? 'telegram_queue');
  }

  // Определение каналов отправки
  public function via(object $notifiable): string
  {
    return TelegramChannel::class;
  }

  // Определение данных для канала 'telegram'
  public function toTelegram($notifiable)
  {
    if($this->type == 'text_message'){
      (new TelegramController())->userMessage($notifiable, $this->message, $this->parse_mode);
    }elseif($this->type == 'image_text_message'){
      (new TelegramController())->userPhotoMessage($notifiable, $this->image, $this->message, $this->parse_mode);
    }elseif($this->type == 'video_text_message'){
      (new TelegramController())->userVideoMessage($notifiable, $this->image, $this->message, $this->parse_mode);
    }elseif($this->type == 'files'){
      (new TelegramController())->userFile($notifiable, $this->message);
    }

  }
}
