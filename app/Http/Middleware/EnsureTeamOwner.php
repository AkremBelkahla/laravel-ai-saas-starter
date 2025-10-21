<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        $team = $request->user()?->currentTeam();

        if (! $team || ! $team->isOwner($request->user())) {
            abort(403, 'You must be the team owner to perform this action.');
        }

        return $next($request);
    }
}
