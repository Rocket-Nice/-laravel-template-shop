<?php

namespace App\Jobs;

use App\Services\DashamailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveEmailFromDashamailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected string $email;

  public function __construct(string $email)
  {
    $this->email = $email;
  }

  public function handle(DashamailService $dashamail): void
  {
    $dashamail->deleteEmail($this->email);
  }
}
