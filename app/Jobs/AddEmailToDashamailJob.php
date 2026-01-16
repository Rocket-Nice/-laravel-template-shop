<?php

namespace App\Jobs;

use App\Services\DashamailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddEmailToDashamailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected string $email;
  protected array $params;

  public function __construct(string $email, array $params = [])
  {
    $this->email = $email;
    $this->params = $params;
  }

  public function handle(DashamailService $dashamail): void
  {
    $dashamail->addEmail($this->email, $this->params);
  }
}
