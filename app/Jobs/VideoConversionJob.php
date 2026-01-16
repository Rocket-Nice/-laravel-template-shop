<?php

namespace App\Jobs;

use App\Http\Controllers\Admin\Goods\ProductController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VideoConversionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $open; // путь к исходному видео
    private $save; // путь куда сохраняем новое

    private $product_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($open, $save, $product_id)
    {
        $this->open = $open;
        $this->save = $save;
        $this->product_id = $product_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      (new ProductController())->videoConversion($this->open, $this->save, $this->product_id);
    }
}
