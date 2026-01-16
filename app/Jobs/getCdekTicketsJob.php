<?php

namespace App\Jobs;

use App\Http\Controllers\Shipping\CdekController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class getCdekTicketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $order_ids = [];
    private $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($order_ids = [], $user_id)
    {
      $this->order_ids = $order_ids;
      $this->user_id = $user_id;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      (New CdekController())->getTickets($this->order_ids ?? null, $this->user_id);
      return true;
    }
}
