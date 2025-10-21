<?php

namespace App\Domain\Billing\Enums;

enum Plan: string
{
    case FREE = 'free';
    case PRO = 'pro';
    case TEAM = 'team';

    public function label(): string
    {
        return match ($this) {
            self::FREE => 'Free',
            self::PRO => 'Pro',
            self::TEAM => 'Team',
        };
    }

    public function price(): int
    {
        return match ($this) {
            self::FREE => 0,
            self::PRO => 2900, // $29.00
            self::TEAM => 9900, // $99.00
        };
    }

    public function stripePriceId(): ?string
    {
        return match ($this) {
            self::FREE => config('services.stripe.prices.free'),
            self::PRO => config('services.stripe.prices.pro'),
            self::TEAM => config('services.stripe.prices.team'),
        };
    }

    public function textCredits(): int
    {
        return match ($this) {
            self::FREE => config('ai.credits.free.text'),
            self::PRO => config('ai.credits.pro.text'),
            self::TEAM => config('ai.credits.team.text'),
        };
    }

    public function imageCredits(): int
    {
        return match ($this) {
            self::FREE => config('ai.credits.free.image'),
            self::PRO => config('ai.credits.pro.image'),
            self::TEAM => config('ai.credits.team.image'),
        };
    }

    public function rateLimit(): int
    {
        return match ($this) {
            self::FREE => config('ai.rate_limits.free'),
            self::PRO => config('ai.rate_limits.pro'),
            self::TEAM => config('ai.rate_limits.team'),
        };
    }

    public function features(): array
    {
        return match ($this) {
            self::FREE => [
                'text_credits' => $this->textCredits(),
                'image_credits' => $this->imageCredits(),
                'api_access' => false,
                'priority_support' => false,
                'custom_templates' => false,
            ],
            self::PRO => [
                'text_credits' => $this->textCredits(),
                'image_credits' => $this->imageCredits(),
                'api_access' => true,
                'priority_support' => false,
                'custom_templates' => true,
            ],
            self::TEAM => [
                'text_credits' => $this->textCredits(),
                'image_credits' => $this->imageCredits(),
                'api_access' => true,
                'priority_support' => true,
                'custom_templates' => true,
                'team_members' => 'unlimited',
            ],
        };
    }

    public static function fromStripePriceId(string $priceId): ?self
    {
        foreach (self::cases() as $plan) {
            if ($plan->stripePriceId() === $priceId) {
                return $plan;
            }
        }

        return null;
    }
}
