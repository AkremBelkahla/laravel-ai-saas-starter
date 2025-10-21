<?php

namespace App\Providers;

use App\Domain\Ai\Contracts\AiDriverInterface;
use App\Domain\Ai\Drivers\OpenAIDriver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind AI Driver
        $this->app->singleton(AiDriverInterface::class, function ($app) {
            $driver = config('ai.default_driver', 'openai');

            return match ($driver) {
                'openai' => new OpenAIDriver(),
                default => throw new \Exception("Unsupported AI driver: {$driver}"),
            };
        });
    }

    public function boot(): void
    {
        //
    }
}
