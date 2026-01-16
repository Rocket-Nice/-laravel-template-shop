<?php

namespace App\Providers;

use App\Channels\TelegramChannel;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
  public $singletons = [
      'telegram' => TelegramChannel::class,
  ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
//      Log::info('Метод boot в TelegramServiceProvider вызван.');
      Notification::resolved(function (ChannelManager $service) {
        $service->extend('telegram', function ($app) {
          return $app->make('telegram');
        });
      });
    }
}
