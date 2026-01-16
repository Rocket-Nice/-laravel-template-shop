<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\HappyCoupon\PrizeController;
use App\Models\Prize;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SetPrizeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $prize_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($prize_id)
    {
      $this->prize_id = $prize_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      (new PrizeController())->set_prize($this->prize_id);
    }
}
