<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailNotification extends Notification implements ShouldQueue
{
  use Queueable;

  private $mailmessage;

  /**
   * Create a new notification instance.
   *
   * @return void
   */
  public function __construct($mailmessage)
  {
    $this->mailmessage = $mailmessage;
  }

  /**
   * Get the notification's delivery channels.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function via($notifiable)
  {
    return ['mail'];
  }

//    public function viaQueues()
//    {
//      return [
//          'mail' => 'mail_queue'
//      ];
//    }

  public function viaQueues()
  {
    $queue_id = rand(1, 10);
    if($queue_id == 1){
      $queue_id = '';
    }
    return [
        'mail' => 'mail_queue'.$queue_id
    ];
  }
  /**
   * Get the mail representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return \Illuminate\Notifications\Messages\MailMessage
   */
  public function toMail($notifiable)
  {
    return $this->mailmessage;
  }

  /**
   * Get the array representation of the notification.
   *
   * @param  mixed  $notifiable
   * @return array
   */
  public function toArray($notifiable)
  {
    return [
      //
    ];
  }
}
