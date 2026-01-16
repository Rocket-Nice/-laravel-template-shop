<?php

namespace App\Jobs;

use App\Services\CompressModule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompressContentImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $content_id;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($content_id)
  {
    $this->content_id = $content_id;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    CompressModule::compressContentImages($this->content_id);
  }
}
