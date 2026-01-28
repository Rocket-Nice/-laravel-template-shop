<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\UserController;
use Illuminate\Console\Command;

class ExecuteTelegramGifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'execute:telegram-gifts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to execute telegram gifts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new UserController())->telegramGifts();
    }
}
