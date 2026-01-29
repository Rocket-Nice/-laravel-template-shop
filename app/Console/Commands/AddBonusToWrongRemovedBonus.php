<?php

namespace App\Console\Commands;

use App\Models\BonusTransaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddBonusToWrongRemovedBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:bonus-to-wrong-removed-bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add bonus to wrong removed bonus';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Start adding bonus to wrong removed bonuses...');

        $fileName = 'added_bonus_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $path = storage_path('app/' . $fileName);

        $csv = fopen($path, 'w');
        fputcsv($csv, [
            'user_id',
            'email',
            'before_balance',
            'after_balance'
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

            $addBonus = 250;
            $before = $user->getBonuses();

            $user->addBonuses(
                $addBonus,
                'telegram script'
            );

            $after = $user->getBonuses();

            fputcsv($csv, [
                $user->id,
                $user->email ?? '',
                $before,
                $after
            ]);

            $this->info("User #{$user->id}: -{$after}");
        }

        fclose($csv);

        $this->info("Done! CSV saved to: storage/app/{$fileName}");

        return Command::SUCCESS;
    }
}
