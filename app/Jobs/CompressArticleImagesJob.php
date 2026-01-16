<?php

namespace App\Jobs;

use App\Services\CompressModule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CompressArticleImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  private $article_id;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($article_id)
  {
    $this->article_id = $article_id;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    CompressModule::compressArticleImages($this->article_id);
  }
}
