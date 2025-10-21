<?php

namespace App\Http\Middleware;

use App\Domain\Billing\Services\CreditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCredits
{
    public function __construct(
        protected CreditService $creditService
    ) {
    }

    public function handle(Request $request, Closure $next, string $type, int $amount = 1): Response
    {
        $team = $request->user()?->currentTeam();

        if (! $team) {
            return redirect()->route('dashboard')
                ->with('error', 'No team found');
        }

        if (! $this->creditService->hasCredits($team, $type, $amount)) {
            return redirect()->route('billing.index')
                ->with('error', "Insufficient {$type} credits. Please upgrade your plan.");
        }

        return $next($request);
    }
}
