<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    protected $token;

    protected $notifiable;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $notifiable)
    {

        $this->token = $token;

        $this->notifiable = $notifiable;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reset_password_notification', [
            'url' => url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $this->notifiable->email,
                ], false)),
            'name' => $this->notifiable->name,
        ]);

    }
}
