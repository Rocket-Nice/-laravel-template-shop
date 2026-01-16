<?php

namespace App\Jobs;

use App\Http\Controllers\Shipping\CdekController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCdekRegionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
  public function __construct($page)
  {
    $this->page = $page;
  }


  protected $page;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      (New CdekController())->updateRegions($this->page);
      return true;
    }
}
