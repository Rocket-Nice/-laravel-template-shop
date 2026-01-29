<?php

namespace App\Console\Commands;

use App\Models\BonusTransaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RemoveWrongBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:wrong-bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove wrong telegram bonuses and export report to CSV';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Start removing wrong bonuses...');

        $fileName = 'wrong_bonus_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $path = storage_path('app/' . $fileName);

        $csv = fopen($path, 'w');
        fputcsv($csv, [
            'user_id',
            'email',
            'telegram_bonus_count',
            'telegram_bonus_sum',
            'before_balance',
            'after_balance',
            'removed'
        ]);

        $users = BonusTransaction::query()
            ->select('user_id', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(amount) as sum'))
            ->where('comment', 'like', 'telegram%')
            ->where('amount', '>', 0)
            ->groupBy('user_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($users as $row) {
            $user = User::find($row->user_id);
            if (!$user) {
                continue;
            }

            $telegramSum = $row->sum;
            $before = $user->getBonuses();

            if ($telegramSum <= 0 || $before <= 0) {
                continue;
            }

            $subBonus = $telegramSum;
            if ($telegramSum > $before) {
                $subBonus = $before;
            }

            $user->subBonuses(
                $telegramSum,
                'Убираем дубликат бонусовы подписки на телеграм'
            );

            $after = $user->getBonuses();

            fputcsv($csv, [
                $user->id,
                $user->email ?? '',
                $row->cnt,
                $telegramSum,
                $before,
                $after,
                $before - $after,
            ]);

            $this->info("User #{$user->id}: -{$telegramSum}");
        }

        fclose($csv);

        $this->info("Done! CSV saved to: storage/app/{$fileName}");

        return Command::SUCCESS;
    }
}
