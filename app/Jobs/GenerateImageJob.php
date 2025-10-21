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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 120;

    public function __construct(
        public AiJob $job
    ) {
    }

    public function handle(AiDriverInterface $driver, CreditService $creditService): void
    {
        $this->job->update(['status' => 'processing']);

        try {
            // Generate images
            $result = $driver->generateImage($this->job->prompt, $this->job->options ?? []);

            // Store images
            $storedImages = $this->storeImages($result['urls']);

            // Calculate cost
            $size = $this->job->options['size'] ?? config('ai.defaults.image.size');
            $count = $this->job->options['n'] ?? config('ai.defaults.image.n');
            $costPerImage = $this->getCostForSize($size);
            $totalCost = $costPerImage * $count;

            // Debit credits
            $creditService->debit(
                $this->job->team,
                'image',
                $totalCost,
                'image_generation',
                ['job_id' => $this->job->id]
            );

            // Update job
            $this->job->update([
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
        } catch (\Exception $e) {
            Log::error('Image generation job failed', [
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

    protected function storeImages(array $urls): array
    {
        $stored = [];

        foreach ($urls as $index => $url) {
            $filename = sprintf(
                'ai-images/%d/%d/%s-%d.png',
                $this->job->team_id,
                $this->job->id,
                Str::random(16),
                $index
            );

            $imageContent = file_get_contents($url);
            Storage::disk(config('filesystems.default'))->put($filename, $imageContent);

            $stored[] = [
                'path' => $filename,
                'url' => Storage::disk(config('filesystems.default'))->url($filename),
            ];
        }

        return $stored;
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
}
