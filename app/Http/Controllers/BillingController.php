<?php

namespace App\Http\Controllers;

use App\Domain\Billing\Enums\Plan;
use App\Domain\Billing\Services\CreditService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BillingController extends Controller
{
    public function __construct(
        protected CreditService $creditService
    ) {
    }

    public function index(Request $request): Response
    {
        $team = $request->user()->currentTeam();

        $subscription = $team->subscription('default');

        $plans = collect(Plan::cases())->map(fn ($plan) => [
            'name' => $plan->value,
            'label' => $plan->label(),
            'price' => $plan->price(),
            'features' => $plan->features(),
        ]);

        $usageStats = $this->creditService->getUsageStats($team);

        return Inertia::render('Billing/Index', [
            'plans' => $plans,
            'currentPlan' => $team->plan,
            'subscription' => $subscription,
            'usageStats' => $usageStats,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:pro,team',
        ]);

        $team = $request->user()->currentTeam();
        $plan = Plan::from($request->input('plan'));

        if (! $plan->stripePriceId()) {
            return back()->with('error', 'Invalid plan selected');
        }

        return $team->newSubscription('default', $plan->stripePriceId())
            ->checkout([
                'success_url' => route('billing.success'),
                'cancel_url' => route('billing.index'),
            ]);
    }

    public function success(Request $request): Response
    {
        return Inertia::render('Billing/Success');
    }

    public function portal(Request $request)
    {
        $team = $request->user()->currentTeam();

        return $team->redirectToBillingPortal(route('billing.index'));
    }

    public function history(Request $request): Response
    {
        $team = $request->user()->currentTeam();

        $history = $this->creditService->getHistory($team, 100);

        return Inertia::render('Billing/History', [
            'history' => $history,
        ]);
    }
}
