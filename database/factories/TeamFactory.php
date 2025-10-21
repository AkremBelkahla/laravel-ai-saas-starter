<?php

namespace Database\Factories;

use App\Domain\Billing\Enums\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'owner_id' => User::factory(),
            'plan' => Plan::FREE,
        ];
    }

    public function pro(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => Plan::PRO,
        ]);
    }

    public function team(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => Plan::TEAM,
        ]);
    }
}
