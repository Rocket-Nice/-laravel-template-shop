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
        // Remove bonus without expiration date
        $schedule->command('remove:bonus-without-expiration-date')->dailyAt('00:00')
            ->when(function () {
                return now()->between(
                    '2026-02-01 00:00:00',
                    '2026-02-01 00:02:00'
                );
            })
            ->withoutOverlapping()
            ->onOneServer();

        $schedule->call(function () {
            if (ExportFile::query()->where('size', null)->exists()) {
                CalcExportFilesJob::dispatch()->onQueue('calc_export_files');
            }
        })->everyMinute();

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
        $schedule->command('queue:work --queue=compressImages --stop-when-empty --timeout=1200')->everyMinute();
        $schedule->command('queue:work --queue=check_cities --stop-when-empty --timeout=600')->everyMinute();
        //      $schedule->command('queue:work --queue=export_users --stop-when-empty --timeout=3600')->everyMinute();
        $schedule->command('queue:work --queue=send_to_sdek,send_to_boxberry,send_to_pochta,boxberry_tickets,cdek_tickets --stop-when-empty --timeout=600')->everyMinute()->withoutOverlapping(1);
        $schedule->command('queue:work --queue=check_order_statuses --stop-when-empty')->everyMinute();
        $schedule->command('queue:work --queue=telegram_queue --stop-when-empty')->everyMinute();
        $schedule->command('queue:work --queue=x5post_pvzs --timeout=600 --stop-when-empty')->everyMinute();
        $schedule->command('queue:work --queue=telegram_mailing1 --timeout=600 --stop-when-empty')->everyMinute();
        $schedule->command('queue:retry --queue=telegram_queue')->everyFiveMinutes();
        //$schedule->command('queue:retry --queue=mail_queue')->hourly()
        $schedule->command('queue:work --queue=send_to_x5post,x5post_tickets --timeout=600 --stop-when-empty')->everyMinute()->withoutOverlapping(1);
        $schedule->command('queue:work --queue=update_viewers --stop-when-empty')->everyMinute()->withoutOverlapping(1);
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
