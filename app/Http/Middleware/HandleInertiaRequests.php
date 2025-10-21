<?php

namespace App\Http\Middleware;

use App\Domain\Billing\Services\CreditService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $team = $user?->currentTeam();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ] : null,
                'team' => $team ? [
                    'id' => $team->id,
                    'name' => $team->name,
                    'plan' => $team->plan->value,
                    'is_owner' => $user && $team->isOwner($user),
                ] : null,
            ],
            'credits' => $team ? app(CreditService::class)->getUsageStats($team) : null,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'warning' => $request->session()->get('warning'),
                'info' => $request->session()->get('info'),
            ],
        ];
    }
}
