<?php

namespace App\Jobs;

use App\Http\Controllers\Shipping\BoxberryController;
use App\Http\Controllers\Shipping\X5PostController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrdersToX5PostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $orders = [];
  private $user_id;
  /**
   * Create a new job instance.
   */
  public function __construct($orders, $user_id)
  {
    $this->orders = $orders;
    $this->user_id = $user_id;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    (new X5PostController())->prepareOrders($this->orders, $this->user_id);
  }
}
