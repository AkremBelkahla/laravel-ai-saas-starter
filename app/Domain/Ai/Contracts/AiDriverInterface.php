<?php

namespace App\Domain\Ai\Contracts;

interface AiDriverInterface
{
    /**
     * Generate text from a prompt
     *
     * @param  string  $prompt
     * @param  array  $options
     * @return array{text: string, tokens: int, model: string}
     */
    public function generateText(string $prompt, array $options = []): array;

    /**
     * Generate images from a prompt
     *
     * @param  string  $prompt
     * @param  array  $options
     * @return array{urls: array, revised_prompt: ?string}
     */
    public function generateImage(string $prompt, array $options = []): array;

    /**
     * Get the driver name
     */
    public function getName(): string;
}
