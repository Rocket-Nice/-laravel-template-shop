<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\MailSender;
use Illuminate\Console\Command;

class SendMailTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:mail-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to send mail template';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $admins = [
            'rytaya96@mail.ru',
            'bilyalova_viktoriya@mail.ru'
        ];

        $product = Product::select('id', 'name', 'quantity', 'status', 'slug')->first();

        foreach ($admins as $admin)
        {
            (new MailSender($admin))->productNotification($product);
        }
    }
}
