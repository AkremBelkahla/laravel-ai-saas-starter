<?php

use App\Domain\Billing\Enums\Plan;
use App\Domain\Billing\Services\CreditService;
use App\Models\Team;
use App\Models\User;

test('can get credit balance', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id, 'plan' => Plan::PRO]);
    
    $creditService = app(CreditService::class);
    
    $creditService->credit($team, 'text', 1000, 'test');
    
    expect($creditService->getBalance($team, 'text'))->toBe(1000);
});

test('can debit credits', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id, 'plan' => Plan::PRO]);
    
    $creditService = app(CreditService::class);
    
    $creditService->credit($team, 'text', 1000, 'test');
    $creditService->debit($team, 'text', 500, 'usage');
    
    expect($creditService->getBalance($team, 'text'))->toBe(500);
});

test('cannot debit more than available', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id, 'plan' => Plan::FREE]);
    
    $creditService = app(CreditService::class);
    
    $creditService->credit($team, 'text', 100, 'test');
    
    $creditService->debit($team, 'text', 200, 'usage');
})->throws(Exception::class);

test('can reset monthly credits', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['owner_id' => $user->id, 'plan' => Plan::PRO]);
    
    $creditService = app(CreditService::class);
    
    $creditService->credit($team, 'text', 500, 'test');
    $creditService->resetMonthlyCredits($team);
    
    $expectedCredits = Plan::PRO->textCredits();
    expect($creditService->getBalance($team, 'text'))->toBe($expectedCredits);
});
