<?php

namespace App\Domain\Ai\Services;

use App\Domain\Ai\Contracts\AiDriverInterface;
use App\Domain\Ai\Exceptions\InsufficientCreditsException;
use App\Domain\Billing\Services\CreditService;
use App\Models\AiJob;
use App\Models\Team;
use Illuminate\Support\Facades\DB;

class TextGenerator
{
    public function __construct(
        protected AiDriverInterface $driver,
        protected CreditService $creditService
    ) {
    }

    /**
     * Generate text for a team
     *
     * @throws InsufficientCreditsException
     */
    public function generate(Team $team, string $prompt, array $options = []): AiJob
    {
        // Estimate cost (we'll adjust after generation)
        $estimatedTokens = $options['max_tokens'] ?? config('ai.defaults.text.max_tokens');
        $estimatedCost = $this->estimateCost($estimatedTokens);

        // Check credits
        if (! $this->creditService->hasCredits($team, 'text', $estimatedCost)) {
            $available = $this->creditService->getBalance($team, 'text');
            throw new InsufficientCreditsException('text', $estimatedCost, $available);
        }

        // Create job record
        $job = AiJob::create([
            'team_id' => $team->id,
            'type' => 'text',
            'status' => 'processing',
            'prompt' => $prompt,
            'options' => $options,
            'estimated_cost' => $estimatedCost,
        ]);

        try {
            // Generate text
            $result = $this->driver->generateText($prompt, $options);

            // Calculate actual cost
            $actualCost = $this->calculateCost($result['tokens']);

            // Debit credits
            $this->creditService->debit(
                $team,
                'text',
                $actualCost,
                'text_generation',
                ['job_id' => $job->id]
            );

            // Update job
            $job->update([
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

            return $job->fresh();
        } catch (\Exception $e) {
            $job->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate text asynchronously (via queue)
     */
    public function generateAsync(Team $team, string $prompt, array $options = []): AiJob
    {
        $estimatedTokens = $options['max_tokens'] ?? config('ai.defaults.text.max_tokens');
        $estimatedCost = $this->estimateCost($estimatedTokens);

        if (! $this->creditService->hasCredits($team, 'text', $estimatedCost)) {
            $available = $this->creditService->getBalance($team, 'text');
            throw new InsufficientCreditsException('text', $estimatedCost, $available);
        }

        $job = AiJob::create([
            'team_id' => $team->id,
            'type' => 'text',
            'status' => 'pending',
            'prompt' => $prompt,
            'options' => $options,
            'estimated_cost' => $estimatedCost,
        ]);

        \App\Jobs\GenerateTextJob::dispatch($job);

        return $job;
    }

    protected function estimateCost(int $tokens): int
    {
        $costPer1k = config('ai.costs.text_per_1k_tokens');

        return (int) ceil(($tokens / 1000) * $costPer1k);
    }

    protected function calculateCost(int $tokens): int
    {
        return $this->estimateCost($tokens);
    }
}
