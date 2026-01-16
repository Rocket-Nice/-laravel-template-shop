<?php

namespace App\Jobs;

use App\Http\Controllers\Payment\RobokassaController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckRobokassaPaymentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $page;
  public function __construct($page = 1)
  {
    $this->page = $page;
  }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      (new RobokassaController())->checkPayments($this->page);
    }
}
