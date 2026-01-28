<?php

namespace App\Console;

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\OrderController;
use App\Jobs\CalcExportFilesJob;
use App\Jobs\checkCdekCourierCitiesJob;
use App\Jobs\CheckOrdersStatusJob;
use App\Jobs\CheckRobokassaPaymentsJob;
use App\Models\ExportFile;
use App\Models\Prize;
use App\Models\Setting;
use App\Jobs\UpdateBoxberryCitiesJob;
use App\Jobs\UpdateBoxberryPvzsJob;
use App\Jobs\UpdateCdekCitiesJob;
use App\Jobs\UpdateCdekCourierCitiesJob;
use App\Jobs\UpdateCdekPvzJob;
use App\Jobs\UpdateCdekRegionsJob;
use App\Jobs\UpdateProductViewersJob;
use App\Jobs\UpdateX5PostPvzJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            if (ExportFile::query()->where('size', null)->exists()) {
                CalcExportFilesJob::dispatch()->onQueue('calc_export_files');
            }
        })->everyMinute();
        //Log::debug('hello');

        // $schedule->command('queue:work --queue=telegram_queue')->at('16:41');
        //      $schedule->call(function(){
        //       Log::debug(1222);
        //      })->everyMinute();

        //      $schedule->call(function(){
        //        $prize_id = 128;
        //        $prize = Prize::find($prize_id);
        //        $gave = $prize->giftCoupons()->where('created_at', '>', '2024-02-25 10:00:00')->count();
        //        if ($gave < 2 && $prize->count == 0) {
        //          $this->addPrize($prize_id, 1);
        //        }
        //      })->at('03:00');
        // iphone
        //       $schedule->call(function(){
        //        $prize_id = 167;
        //        $prize = Prize::find($prize_id);
        //        $gave = $prize->giftCoupons()->where('created_at', '>', '2024-06-01 07:00:00')->count();
        //        if ($gave < 3 && $prize->count == 0) {
        //          $this->addPrize($prize_id, 1);
        //        }
        //      })
        //          ->when(function(){
        //            $times17 = [
        //                '15:30',
        //                '16:30',
        //            ];
        //            $now = now()->toTimeString('minutes');
        //            return (in_array($now, $times17) && (now()->day == 1));
        //          })
        //          ->everyMinute();
        // Бокс с мини версиями
        //       $schedule->call(function(){
        //        $prize_id = 147;
        //        $prize = Prize::find($prize_id);
        //        $gave = $prize->giftCoupons()->where('created_at', '>', '2024-02-25 10:00:00')->count();
        //        if ($gave < 10 && $prize->count == 0) {
        //          $this->addPrize($prize_id, 1);
        //        }
        //      })
        //          ->when(function(){
        //            $times17 = [
        //                '00:10',
        //                '02:30',
        //                '04:50',
        //                '07:20',
        //                '09:30',
        //                '11:10',
        //                '13:40',
        //                '15:25',
        //                '17:55',
        //            ];
        //            $now = now()->toTimeString('minutes');
        //            return (in_array($now, $times17) && (now()->day == 26));
        //          })
        //          ->everyMinute();
        // фен
        //       $schedule->call(function(){
        //        $prize_id = 130;
        //        $prize = Prize::find($prize_id);
        //        $gave = $prize->giftCoupons()->where('created_at', '>', '2024-02-25 10:00:00')->count();
        //        if ($gave < 18 && $prize->count == 0) {
        //          $this->addPrize($prize_id, 1);
        //        }
        //      })
        //          ->when(function(){
        //            $times17 = [
        //                '10:10',
        //                '10:20',
        //                '10:30',
        //                '10:40',
        //                '10:50',
        //                '11:00',
        //                '11:10',
        //                '11:20',
        //                '11:30',
        //                '11:45',
        //                '12:00',
        //                '12:15',
        //                '12:30',
        //                '12:45',
        //                '13:00',
        //                '13:15',
        //                '13:30',
        //                '13:45',
        //                '14:00',
        //            ];
        //            $now = now()->toTimeString('minutes');
        //            return (in_array($now, $times17) && (now()->day == 25));
        //          })
        //          ->everyMinute();
        // рассылка
        //      $schedule->call(function(){
        //        $users = User::query()->whereHas('tgChats', function(Builder $builder){
        //          $builder->where('active', true);
        //        })
        //            ->whereDoesntHave('orders', function (Builder $builder){
        //              $builder->where('confirm', 1);
        //              $builder->where('created_at', '>', '2024-06-01 00:00:00');
        //            })
        ////            ->whereIn('id', [1,2])
        //            ->pluck('id')->toArray();
        //        $mailing = MailingList::find(14);
        //        $text = "*ХОЧЕШЬ В ДУБАЙ\\?*\n\n";
        //
        //        $text .= "Только *24ч*🔥\nНаш клиентский день в LE MOUSSE\n*1 \\+ 1 \\= 3🎁 1000 подарков *✈️\n\n";
        //
        //        $text .= "https://lemousse\\.shop\n\n";
        //
        //        $text .= "Самый дорогой продукт в корзине в подарок, а так же возможность выиграть *Путевку в Дубай*, Apple IPhone, SPA боксы и еще *1000 крутых призов*\\!\n\n";
        //
        //        $text .= "_Акция распространяется на товары из одной категории💫_";
        //        $tgChats = TgChat::query()->with('user')->whereIn('user_id', $users)->where('active', true)->chunk(1, function ($tgChats) use ($text, $mailing) {
        //          foreach($tgChats as $tgChat){
        //            $tgChat->user->mailing_list()->syncWithoutDetaching($mailing);
        //            $tgChat->notify(new TelegramNotification($text, 'text_message', 'MarkdownV2'));
        //          }
        //        });
        //      })->at('11:00');

        // Log::debug('try schedule');
        if (config('app.env') === 'production') {
            $schedule->call(function () {
                checkCdekCourierCitiesJob::dispatch(1)->onQueue('check_cities');
            })->weeklyOn(0, '04:00');
            $schedule->call(function () {
                UpdateCdekRegionsJob::dispatch(0)->onQueue('cdek_regions');
                UpdateBoxberryCitiesJob::dispatch(0)->onQueue('boxberry_city');
            })->at('00:00');
            $schedule->call(function () {
                UpdateCdekCitiesJob::dispatch(0)->onQueue('cdek_cities');
            })->at('00:10');
            $schedule->call(function () {
                UpdateBoxberryPvzsJob::dispatch(0)->onQueue('boxberry_pvz');
                UpdateCdekPvzJob::dispatch(0)->onQueue('cdek_pvz');
            })->at('01:30');
            $schedule->call(function () {
                UpdateCdekCourierCitiesJob::dispatch(0)->onQueue('cdek_courier_cities');
            })->at('02:00');
            $schedule->call(function () {
                (new UserController())->expireBonuses();
            })->at('00:01');
            $schedule->call(function () {
                (new UserController())->birthdayGifts();
            })->at('06:00');
            $schedule->call(function () {
                (new UserController())->telegramGifts();
                (new UserController())->surveyGifts();
            })->at('05:00');
            $schedule->call(function () {
                $x5post_job = DB::table('jobs')->whereIn('queue', ['x5post_pvzs'])->exists();
                if (!$x5post_job) {
                    UpdateX5PostPvzJob::dispatch()->onQueue('x5post_pvzs');
                }
            })->at('07:00');
            $schedule->call(function () {
                (new OrderController())->findNotPaidOrders();
            })->everyMinute();
            $schedule->call(function () {
                CheckRobokassaPaymentsJob::dispatch(1)->onQueue('robokassa_payments');
            })->everyTenMinutes();
            $schedule->call(function () {
                CheckOrdersStatusJob::dispatch(1, ['yandex'])->onQueue('check_order_statuses');
            })->cron('10 */3 * * *');
            $schedule->call(function () {
                CheckOrdersStatusJob::dispatch(1, ['cdek'])->onQueue('check_order_statuses');
            })->cron('20 */3 * * *');
            $schedule->call(function () {
                CheckOrdersStatusJob::dispatch(1, ['cdek_courier'])->onQueue('check_order_statuses');
            })->cron('30 */3 * * *');
            $schedule->call(function () {
                CheckOrdersStatusJob::dispatch(1, ['pochta'])->onQueue('check_order_statuses');
            })->cron('40 */3 * * *');
            $schedule->call(function () {
                CheckOrdersStatusJob::dispatch(1, ['x5post'])->onQueue('check_order_statuses');
            })->cron('50 */3 * * *');
        }


        $queues = [
            'mail_queue',
            'cdek_regions',
            'cdek_cities',
            'cdek_courier_cities',
            'cdek_pvz',
            'boxberry_pvz',
            'boxberry_city',
            'update_tickets',
        ];
        if (!config('happy-coupone.active')) {
            $queues[] = 'mail_delivery';
        }
        $schedule->command('queue:work --queue=' . implode(',', $queues) . ' --stop-when-empty --timeout=300')->everyMinute()->withoutOverlapping(1);
        $schedule->command('queue:work --queue=robokassa_payments --stop-when-empty --timeout=600')->everyMinute()->withoutOverlapping(1);
        $schedule->command('queue:work --queue=create_vouchers --stop-when-empty --timeout=1200')->everyMinute()->withoutOverlapping(1);
        //      $schedule->command('queue:work --queue=compressImages --stop-when-empty --timeout=600')->everyMinute();
        $schedule->command('queue:work --queue=check_cities --stop-when-empty --timeout=600')->everyMinute();
        //      $schedule->command('queue:work --queue=export_users --stop-when-empty --timeout=3600')->everyMinute();
        $schedule->command('queue:work --queue=send_to_sdek,send_to_boxberry,send_to_pochta,boxberry_tickets,cdek_tickets --stop-when-empty --timeout=600')->everyMinute()->withoutOverlapping(1);
        $schedule->command('queue:work --queue=check_order_statuses --stop-when-empty')->everyMinute();
        $schedule->command('queue:work --queue=telegram_queue --stop-when-empty')->everyMinute();
        $schedule->command('queue:work --queue=x5post_pvzs --timeout=600 --stop-when-empty')->everyMinute();
        $schedule->command('queue:work --queue=telegram_mailing1 --timeout=600 --stop-when-empty')->everyMinute();
        $schedule->command('queue:retry --queue=telegram_queue')->everyFiveMinutes();
        //$schedule->command('queue:retry --queue=mail_queue')->hourly()
        $schedule->command('queue:work --queue=send_to_x5post,x5post_tickets --timeout=600 --stop-when-empty')->everyMinute()->withoutOverlapping(1);;
        $schedule->command('queue:work --queue=update_viewers --stop-when-empty')->everyMinute()->withoutOverlapping(1);;
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping(1);

        $schedule->call(function () {
            UpdateProductViewersJob::dispatch()->onQueue('update_viewers');
        })->everyTenMinutes();
        $prizes = DB::table('jobs')->where('queue', 'like', 'set_prize_%')->pluck('queue')->toArray();
        $schedule->command('queue:work --queue=' . implode(',', $prizes) . ' --stop-when-empty')->everyMinute()->withoutOverlapping(1);
        $messages = DB::table('failed_jobs')->where('queue', 'like', 'tg_queue_%')->groupBy('queue')->pluck('queue')->toArray();
        $schedule->command('queue:work --queue=calc_export_files --timeout=600 --stop-when-empty')->everyMinute()->withoutOverlapping(1);
        foreach ($messages as $message) {
            $schedule->command('queue:retry --queue=' . $message)->everyMinute()->withoutOverlapping(1);
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    private function addPrize($id, $count = 1)
    {
        $prize = Prize::find($id);
        if ($prize->count == 0) {
            $prize->increment('count', $count);
            Log::debug('Расписание изменило количество подарков "' . $prize->name . '" на ' . $count);
        } else {
            Log::debug('Количество подарков "' . $prize->name . '" больше 0');
        }
        return true;
    }

    private function closeSite()
    {
        $setting = Setting::query()->where('key', 'maintenanceStatus')->first();
        $setting->update([
            'value' => false
        ]);
    }
}
