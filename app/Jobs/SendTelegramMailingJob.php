<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\TelegramMailingController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTelegramMailingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $tgMailing;
    private $type;
    private $users;
    /**
     * Create a new job instance.
     */
    public function __construct($tgMailing, $type, $users = [])
    {
        $this->tgMailing = $tgMailing;
        $this->type = $type;
        $this->users = $users;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      (new TelegramMailingController())->send($this->tgMailing, $this->type, $this->users);
    }
}
