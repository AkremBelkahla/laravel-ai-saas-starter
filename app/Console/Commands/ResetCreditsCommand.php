<?php

namespace App\Console\Commands;

use App\Domain\Billing\Services\CreditService;
use App\Models\Team;
use Illuminate\Console\Command;

class ResetCreditsCommand extends Command
{
    protected $signature = 'app:reset-credits';

    protected $description = 'Reset monthly credits for all teams based on their plan';

    public function __construct(
        protected CreditService $creditService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Resetting monthly credits...');

        $teams = Team::all();
        $bar = $this->output->createProgressBar($teams->count());

        foreach ($teams as $team) {
            $this->creditService->resetMonthlyCredits($team);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Credits reset for {$teams->count()} teams.");

        return self::SUCCESS;
    }
}
