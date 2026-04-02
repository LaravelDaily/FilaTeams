<?php

namespace LaravelDaily\FilaTeams\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamMembership
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->currentTeam) {
            abort(403, 'You must be a member of a team to access this resource.');
        }

        if (! $user->belongsToTeam($user->currentTeam)) {
            $fallback = $user->fallbackTeam();
            $user->forceFill(['current_team_id' => $fallback?->id])->save();

            if (! $fallback) {
                abort(403, 'You are not a member of any team.');
            }
        }

        return $next($request);
    }
}
