<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\Goods\ProductController;
use App\Services\CompressModule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CompressImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $product_id;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($product_id)
  {
    $this->product_id = $product_id;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    CompressModule::compressProductImages($this->product_id);
  }
}
