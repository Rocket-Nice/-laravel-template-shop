<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ZipArchive;

class CreateZipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $dirPath;
    private $zipPath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dirPath, $zipPath)
    {
        $this->dirPath = $dirPath;
        $this->zipPath = $zipPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $dirPath = $this->dirPath;

      // Создание и открытие архива
      $zip = new ZipArchive();
      $zipPath = $this->zipPath;

      if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
        addToZip($zip, $dirPath);
        $zip->close();
        echo storageToAsset(storage_path('app/public/store_coupons.zip'));
      } else {
        Log::debug("Ошибка при создании архива.");
      }
    }
}
