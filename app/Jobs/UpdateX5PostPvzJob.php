<?php

namespace App\Jobs;

use App\Http\Controllers\Shipping\X5PostController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateX5PostPvzJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $page;
    /**
     * Create a new job instance.
     */
    public function __construct($page = 0)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
      $client = (new X5PostController());
      $client->updatePvz($this->page);
    }
}
