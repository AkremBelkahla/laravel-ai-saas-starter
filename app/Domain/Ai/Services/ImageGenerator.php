<?php

namespace App\Domain\Ai\Services;

use App\Domain\Ai\Contracts\AiDriverInterface;
use App\Domain\Ai\Exceptions\InsufficientCreditsException;
use App\Domain\Billing\Services\CreditService;
use App\Models\AiJob;
use App\Models\Team;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageGenerator
{
    public function __construct(
        protected AiDriverInterface $driver,
        protected CreditService $creditService
    ) {
    }

    /**
     * Generate images for a team
     *
     * @throws InsufficientCreditsException
     */
    public function generate(Team $team, string $prompt, array $options = []): AiJob
    {
        $size = $options['size'] ?? config('ai.defaults.image.size');
        $count = $options['n'] ?? config('ai.defaults.image.n');

        // Calculate cost
        $costPerImage = $this->getCostForSize($size);
        $totalCost = $costPerImage * $count;

        // Check credits
        if (! $this->creditService->hasCredits($team, 'image', $totalCost)) {
            $available = $this->creditService->getBalance($team, 'image');
            throw new InsufficientCreditsException('image', $totalCost, $available);
        }

        // Create job record
        $job = AiJob::create([
            'team_id' => $team->id,
            'type' => 'image',
            'status' => 'processing',
            'prompt' => $prompt,
            'options' => $options,
            'estimated_cost' => $totalCost,
        ]);

        try {
            // Generate images
            $result = $this->driver->generateImage($prompt, $options);

            // Download and store images
            $storedImages = $this->storeImages($result['urls'], $team->id, $job->id);

            // Debit credits
            $this->creditService->debit(
                $team,
                'image',
                $totalCost,
                'image_generation',
                ['job_id' => $job->id]
            );

            // Update job
            $job->update([
                'status' => 'completed',
                'result' => $storedImages,
                'actual_cost' => $totalCost,
                'completed_at' => now(),
                'meta' => [
                    'revised_prompt' => $result['revised_prompt'],
                    'size' => $size,
                    'count' => $count,
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
     * Generate images asynchronously (via queue)
     */
    public function generateAsync(Team $team, string $prompt, array $options = []): AiJob
    {
        $size = $options['size'] ?? config('ai.defaults.image.size');
        $count = $options['n'] ?? config('ai.defaults.image.n');

        $costPerImage = $this->getCostForSize($size);
        $totalCost = $costPerImage * $count;

        if (! $this->creditService->hasCredits($team, 'image', $totalCost)) {
            $available = $this->creditService->getBalance($team, 'image');
            throw new InsufficientCreditsException('image', $totalCost, $available);
        }

        $job = AiJob::create([
            'team_id' => $team->id,
            'type' => 'image',
            'status' => 'pending',
            'prompt' => $prompt,
            'options' => $options,
            'estimated_cost' => $totalCost,
        ]);

        \App\Jobs\GenerateImageJob::dispatch($job);

        return $job;
    }

    protected function getCostForSize(string $size): int
    {
        return match ($size) {
            '256x256', '512x512' => config('ai.costs.image.small'),
            '1024x1024' => config('ai.costs.image.medium'),
            '1024x1792', '1792x1024' => config('ai.costs.image.large'),
            default => config('ai.costs.image.medium'),
        };
    }

    protected function storeImages(array $urls, int $teamId, int $jobId): array
    {
        $stored = [];

        foreach ($urls as $index => $url) {
            $filename = sprintf(
                'ai-images/%d/%d/%s-%d.png',
                $teamId,
                $jobId,
                Str::random(16),
                $index
            );

            // Download image
            $imageContent = file_get_contents($url);

            // Store to S3 or local
            Storage::disk(config('filesystems.default'))->put($filename, $imageContent);

            $stored[] = [
                'path' => $filename,
                'url' => Storage::disk(config('filesystems.default'))->url($filename),
            ];
        }

        return $stored;
    }
}
