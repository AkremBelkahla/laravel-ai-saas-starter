<?php

namespace App\Domain\Billing\Services;

use App\Models\CreditLedger;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class CreditService
{
    /**
     * Get credit balance for a team
     */
    public function getBalance(Team $team, string $type): int
    {
        return CreditLedger::where('team_id', $team->id)
            ->where('type', $type)
            ->sum('delta');
    }

    /**
     * Check if team has sufficient credits
     */
    public function hasCredits(Team $team, string $type, int $amount): bool
    {
        return $this->getBalance($team, $type) >= $amount;
    }

    /**
     * Debit credits from team
     */
    public function debit(Team $team, string $type, int $amount, string $reason, array $meta = []): CreditLedger
    {
        return DB::transaction(function () use ($team, $type, $amount, $reason, $meta) {
            // Check balance
            $balance = $this->getBalance($team, $type);

            if ($balance < $amount) {
                throw new \Exception("Insufficient {$type} credits. Required: {$amount}, Available: {$balance}");
            }

            // Create debit entry
            return CreditLedger::create([
                'team_id' => $team->id,
                'type' => $type,
                'delta' => -$amount,
                'reason' => $reason,
                'meta' => $meta,
            ]);
        });
    }

    /**
     * Credit credits to team
     */
    public function credit(Team $team, string $type, int $amount, string $reason, array $meta = []): CreditLedger
    {
        return CreditLedger::create([
            'team_id' => $team->id,
            'type' => $type,
            'delta' => $amount,
            'reason' => $reason,
            'meta' => $meta,
        ]);
    }

    /**
     * Reset monthly credits for a team based on their plan
     */
    public function resetMonthlyCredits(Team $team): void
    {
        $plan = $team->plan;

        if (! $plan) {
            return;
        }

        DB::transaction(function () use ($team, $plan) {
            // Get current balances
            $textBalance = $this->getBalance($team, 'text');
            $imageBalance = $this->getBalance($team, 'image');

            // Get plan allocations
            $textAllocation = $plan->textCredits();
            $imageAllocation = $plan->imageCredits();

            // Reset text credits
            if ($textBalance < $textAllocation) {
                $this->credit(
                    $team,
                    'text',
                    $textAllocation - $textBalance,
                    'monthly_reset',
                    ['month' => now()->format('Y-m')]
                );
            } elseif ($textBalance > $textAllocation) {
                // Remove excess credits
                $this->debit(
                    $team,
                    'text',
                    $textBalance - $textAllocation,
                    'monthly_reset_excess',
                    ['month' => now()->format('Y-m')]
                );
            }

            // Reset image credits
            if ($imageBalance < $imageAllocation) {
                $this->credit(
                    $team,
                    'image',
                    $imageAllocation - $imageBalance,
                    'monthly_reset',
                    ['month' => now()->format('Y-m')]
                );
            } elseif ($imageBalance > $imageAllocation) {
                $this->debit(
                    $team,
                    'image',
                    $imageBalance - $imageAllocation,
                    'monthly_reset_excess',
                    ['month' => now()->format('Y-m')]
                );
            }
        });
    }

    /**
     * Get credit usage statistics for a team
     */
    public function getUsageStats(Team $team, ?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        $query = CreditLedger::where('team_id', $team->id);

        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        if ($to) {
            $query->where('created_at', '<=', $to);
        }

        $ledgers = $query->get();

        return [
            'text' => [
                'used' => $ledgers->where('type', 'text')->where('delta', '<', 0)->sum('delta') * -1,
                'added' => $ledgers->where('type', 'text')->where('delta', '>', 0)->sum('delta'),
                'balance' => $this->getBalance($team, 'text'),
            ],
            'image' => [
                'used' => $ledgers->where('type', 'image')->where('delta', '<', 0)->sum('delta') * -1,
                'added' => $ledgers->where('type', 'image')->where('delta', '>', 0)->sum('delta'),
                'balance' => $this->getBalance($team, 'image'),
            ],
        ];
    }

    /**
     * Get credit history for a team
     */
    public function getHistory(Team $team, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return CreditLedger::where('team_id', $team->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
