<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CheckWrongBonusUse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:wrong-bonus-use';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check wrong bonus use';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $filePath = storage_path('app/wrong_bonus_orders.csv');

        $handle = fopen($filePath, 'w');

        fputcsv($handle, ['order_id', 'amount', 'cart_sum']);

        Order::orderBy('id')
            ->chunk(500, function ($orders) use ($handle) {

                foreach ($orders as $order) {
                    $cart = $order->data_cart;

                    if (!is_array($cart)) {
                        continue;
                    }

                    $cartSum = 0;

                    foreach ($cart as $item) {
                        $qty   = $item['qty']   ?? 0;
                        $price = $item['price'] ?? 0;

                        $cartSum += $qty * $price;
                    }

                    if ($cartSum <= 0) {
                        continue;
                    }

                    // Amount is paid sum
                    if ($order->amount < ($cartSum * 0.5)) {
                        fputcsv($handle, [
                            $order->id,
                            $order->amount,
                            $cartSum
                        ]);
                    }
                }
            });

        fclose($handle);

        $this->info('Done');
        $this->info('CSV saved to: ' . $filePath);

        return Command::SUCCESS;
    }
}
