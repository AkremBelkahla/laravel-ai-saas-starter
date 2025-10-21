<?php

namespace App\Domain\Ai\Drivers;

use App\Domain\Ai\Contracts\AiDriverInterface;
use App\Domain\Ai\Exceptions\AiGenerationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIDriver implements AiDriverInterface
{
    protected string $apiKey;

    protected ?string $organization;

    protected int $timeout;

    protected string $textModel;

    protected string $imageModel;

    public function __construct()
    {
        $this->apiKey = config('ai.drivers.openai.api_key');
        $this->organization = config('ai.drivers.openai.organization');
        $this->timeout = config('ai.drivers.openai.request_timeout', 30);
        $this->textModel = config('ai.drivers.openai.models.text');
        $this->imageModel = config('ai.drivers.openai.models.image');
    }

    public function generateText(string $prompt, array $options = []): array
    {
        $defaults = config('ai.defaults.text');

        $payload = [
            'model' => $options['model'] ?? $this->textModel,
            'messages' => [
                ['role' => 'system', 'content' => $options['system'] ?? 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $options['max_tokens'] ?? $defaults['max_tokens'],
            'temperature' => $options['temperature'] ?? $defaults['temperature'],
            'top_p' => $options['top_p'] ?? $defaults['top_p'],
            'frequency_penalty' => $options['frequency_penalty'] ?? $defaults['frequency_penalty'],
            'presence_penalty' => $options['presence_penalty'] ?? $defaults['presence_penalty'],
        ];

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout)
                ->retry(3, 1000)
                ->post('https://api.openai.com/v1/chat/completions', $payload);

            if ($response->failed()) {
                throw new AiGenerationException(
                    'OpenAI API request failed: '.$response->body(),
                    $response->status()
                );
            }

            $data = $response->json();

            return [
                'text' => $data['choices'][0]['message']['content'] ?? '',
                'tokens' => $data['usage']['total_tokens'] ?? 0,
                'model' => $data['model'] ?? $this->textModel,
                'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI text generation failed', [
                'error' => $e->getMessage(),
                'prompt_length' => strlen($prompt),
            ]);

            throw new AiGenerationException(
                'Failed to generate text: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function generateImage(string $prompt, array $options = []): array
    {
        $defaults = config('ai.defaults.image');

        $payload = [
            'model' => $options['model'] ?? $this->imageModel,
            'prompt' => $prompt,
            'n' => $options['n'] ?? $defaults['n'],
            'size' => $options['size'] ?? $defaults['size'],
            'quality' => $options['quality'] ?? $defaults['quality'],
            'style' => $options['style'] ?? $defaults['style'],
        ];

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout($this->timeout * 2) // Images take longer
                ->retry(2, 2000)
                ->post('https://api.openai.com/v1/images/generations', $payload);

            if ($response->failed()) {
                throw new AiGenerationException(
                    'OpenAI Image API request failed: '.$response->body(),
                    $response->status()
                );
            }

            $data = $response->json();

            return [
                'urls' => collect($data['data'] ?? [])->pluck('url')->toArray(),
                'revised_prompt' => $data['data'][0]['revised_prompt'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI image generation failed', [
                'error' => $e->getMessage(),
                'prompt' => $prompt,
            ]);

            throw new AiGenerationException(
                'Failed to generate image: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getName(): string
    {
        return 'openai';
    }

    protected function getHeaders(): array
    {
        $headers = [
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json',
        ];

        if ($this->organization) {
            $headers['OpenAI-Organization'] = $this->organization;
        }

        return $headers;
    }
}
