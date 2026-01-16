<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//      $setting_keys = [
//          'maintenanceStatus', 'maintenanceNotification', 'tg_support', 'promo_1+1=3', 'happyCoupon', 'puzzlesStatus', 'diamondPromo1', 'diamondPromo2'
//      ];
//      $settings = Setting::whereIn('key', $setting_keys)->get();
//      if($settings){
//        View::share('settings', $settings);
//      }
    }
}
