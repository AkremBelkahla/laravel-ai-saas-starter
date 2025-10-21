<?php

namespace App\Http\Controllers;

use App\Domain\Billing\Enums\Plan;
use App\Domain\Billing\Services\CreditService;
use App\Models\AuditLog;
use App\Models\Team;
use Illuminate\Http\Request;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class WebhookController extends CashierController
{
    public function __construct(
        protected CreditService $creditService
    ) {
    }

    /**
     * Handle subscription created
     */
    protected function handleCustomerSubscriptionCreated(array $payload): void
    {
        parent::handleCustomerSubscriptionCreated($payload);

        $team = $this->getTeamFromPayload($payload);

        if ($team) {
            $priceId = $payload['data']['object']['items']['data'][0]['price']['id'] ?? null;
            $plan = Plan::fromStripePriceId($priceId);

            if ($plan) {
                $team->update(['plan' => $plan]);

                // Allocate initial credits
                $this->creditService->credit(
                    $team,
                    'text',
                    $plan->textCredits(),
                    'subscription_created',
                    ['subscription_id' => $payload['data']['object']['id']]
                );

                $this->creditService->credit(
                    $team,
                    'image',
                    $plan->imageCredits(),
                    'subscription_created',
                    ['subscription_id' => $payload['data']['object']['id']]
                );

                AuditLog::log('subscription_created', null, $team);
            }
        }
    }

    /**
     * Handle subscription updated
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): void
    {
        parent::handleCustomerSubscriptionUpdated($payload);

        $team = $this->getTeamFromPayload($payload);

        if ($team) {
            $priceId = $payload['data']['object']['items']['data'][0]['price']['id'] ?? null;
            $plan = Plan::fromStripePriceId($priceId);

            if ($plan && $team->plan !== $plan) {
                $oldPlan = $team->plan;
                $team->update(['plan' => $plan]);

                // Reset credits to new plan allocation
                $this->creditService->resetMonthlyCredits($team);

                AuditLog::log('subscription_updated', null, $team, null, [
                    'old_plan' => $oldPlan->value,
                ], [
                    'new_plan' => $plan->value,
                ]);
            }
        }
    }

    /**
     * Handle subscription deleted
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): void
    {
        parent::handleCustomerSubscriptionDeleted($payload);

        $team = $this->getTeamFromPayload($payload);

        if ($team) {
            $team->update(['plan' => Plan::FREE]);

            // Reset to free plan credits
            $this->creditService->resetMonthlyCredits($team);

            AuditLog::log('subscription_deleted', null, $team);
        }
    }

    /**
     * Handle payment succeeded
     */
    protected function handleInvoicePaymentSucceeded(array $payload): void
    {
        $team = $this->getTeamFromPayload($payload);

        if ($team) {
            AuditLog::log('payment_succeeded', null, $team, null, [], [
                'amount' => $payload['data']['object']['amount_paid'] ?? 0,
                'invoice_id' => $payload['data']['object']['id'] ?? null,
            ]);
        }
    }

    /**
     * Handle payment failed
     */
    protected function handleInvoicePaymentFailed(array $payload): void
    {
        $team = $this->getTeamFromPayload($payload);

        if ($team) {
            AuditLog::log('payment_failed', null, $team, null, [], [
                'amount' => $payload['data']['object']['amount_due'] ?? 0,
                'invoice_id' => $payload['data']['object']['id'] ?? null,
            ]);
        }
    }

    protected function getTeamFromPayload(array $payload): ?Team
    {
        $stripeId = $payload['data']['object']['customer'] ?? null;

        if (! $stripeId) {
            return null;
        }

        return Team::where('stripe_id', $stripeId)->first();
    }
}
