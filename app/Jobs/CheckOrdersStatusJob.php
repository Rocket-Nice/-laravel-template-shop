<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckOrdersStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $page;
    private $shipping_codes;
    public function __construct($page = 1, $shipping_codes = ['cdek', 'cdek_courier', 'boxberry'])
    {
      $this->page = $page;
      $this->shipping_codes = $shipping_codes;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      (new OrderController())->checkStatusesSchedule($this->page, $this->shipping_codes);
    }
}
