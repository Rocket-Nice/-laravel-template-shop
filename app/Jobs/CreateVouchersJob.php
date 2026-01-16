<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\Promo\VoucherController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateVouchersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $id;
  private $count;

  /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $count)
    {
        $this->id = $id;
        $this->count = $count;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      (new VoucherController())->create_voucher($this->id, $this->count);
    }
}
