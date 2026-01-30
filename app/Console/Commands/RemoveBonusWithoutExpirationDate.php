<?php

namespace App\Console\Commands;

use App\Models\Bonus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveBonusWithoutExpirationDate extends Command
{
    protected $signature = 'remove:bonus-without-expiration-date';

    protected $description = 'Remove bonus without expiration date';

    public function handle()
    {
        $this->info('Start removing bonus without expiration date...');

        $removedFile = 'removed_bonus_without_expiration_' . now()->format('Y-m-d') . '.csv';
        $removedPath = storage_path('app/' . $removedFile);

        $removedCsv = fopen($removedPath, 'w');
        fputcsv($removedCsv, [
            'user_id',
            'email',
            'removed_bonus'
        ]);

        $remainingFile = 'remaining_bonus_after_2026-02-01_' . now()->format('Y-m-d_H') . '.csv';
        $remainingPath = storage_path('app/' . $remainingFile);

        $remainingCsv = fopen($remainingPath, 'w');
        fputcsv($remainingCsv, [
            'user_id',
            'email',
            'remaining_bonus'
        ]);

        $totalRemoved = 0;

        $bonuses = Bonus::query()
            ->whereNull('expired_at')
            ->get()
            ->groupBy('user_id');

        $this->info('Users found (without expiration): ' . $bonuses->count());

        foreach ($bonuses as $userId => $userBonuses) {

            $user = User::find($userId);
            if (!$user) {
                continue;
            }

            $amountToRemove = $userBonuses->sum('amount');

            if ($amountToRemove <= 0) {
                continue;
            }

            $currentBalance = $user->getBonuses();
            $subBonus = min($amountToRemove, $currentBalance);

            $user->subBonuses(
                $subBonus,
                'Сгорели в 01/02/2026 (script)'
            );

            $totalRemoved += $subBonus;

            fputcsv($removedCsv, [
                $user->id,
                $user->email,
                $subBonus,
            ]);

            $this->info("User {$user->id}: -{$subBonus}");
        }

        $remainingBonuses = Bonus::query()
            ->whereDate('expired_at', '>', Carbon::parse('2026-02-01'))
            ->get()
            ->groupBy('user_id');

        foreach ($remainingBonuses as $userId => $bonuses) {
            $user = User::find($userId);
            if (!$user) {
                continue;
            }

            $remainingAmount = $bonuses->sum('amount');

            if ($remainingAmount <= 0) {
                continue;
            }

            fputcsv($remainingCsv, [
                $user->id,
                $user->email,
                $remainingAmount,
            ]);
        }

        fclose($removedCsv);
        fclose($remainingCsv);

        $this->info('--------------------------------');
        $this->info('TOTAL REMOVED: ' . $totalRemoved);
        $this->info('Removed CSV: ' . $removedPath);
        $this->info('Remaining CSV: ' . $remainingPath);

        return Command::SUCCESS;
    }
}
