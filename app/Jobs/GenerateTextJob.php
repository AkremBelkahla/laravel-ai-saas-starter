<?php

namespace App\Jobs;

use App\Domain\Ai\Contracts\AiDriverInterface;
use App\Domain\Billing\Services\CreditService;
use App\Models\AiJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        public AiJob $job
    ) {
    }

    public function handle(AiDriverInterface $driver, CreditService $creditService): void
    {
        $this->job->update(['status' => 'processing']);

        try {
            // Generate text
            $result = $driver->generateText($this->job->prompt, $this->job->options ?? []);

            // Calculate cost
            $costPer1k = config('ai.costs.text_per_1k_tokens');
            $actualCost = (int) ceil(($result['tokens'] / 1000) * $costPer1k);

            // Debit credits
            $creditService->debit(
                $this->job->team,
                'text',
                $actualCost,
                'text_generation',
                ['job_id' => $this->job->id]
            );

            // Update job
            $this->job->update([
                'status' => 'completed',
                'result' => $result['text'],
                'tokens_used' => $result['tokens'],
                'actual_cost' => $actualCost,
                'model' => $result['model'],
                'completed_at' => now(),
                'meta' => [
                    'finish_reason' => $result['finish_reason'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Text generation job failed', [
                'job_id' => $this->job->id,
                'error' => $e->getMessage(),
            ]);

            $this->job->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
