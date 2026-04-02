<?php

namespace LaravelDaily\FilaTeams\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use LaravelDaily\FilaTeams\Models\Membership;
use LaravelDaily\FilaTeams\Models\TeamInvitation;

class AcceptInvitationController extends Controller
{
    public function __invoke(Request $request, string $code): RedirectResponse
    {
        $invitation = TeamInvitation::where('code', $code)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($invitation->isExpired()) {
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'This invitation has expired.');
        }

        $user = $request->user();

        // Not logged in — redirect to login with return URL
        if (! $user) {
            session()->put('url.intended', route('filateams.invitations.accept', $code));

            return redirect()->route('filament.admin.auth.login');
        }

        // Verify the authenticated user's email matches the invitation
        if ($user->email !== $invitation->email) {
            abort(403, 'This invitation was sent to a different email address.');
        }

        // Already a member — just mark accepted and redirect
        if ($user->belongsToTeam($invitation->team)) {
            $invitation->update(['accepted_at' => now()]);

            return redirect(Filament::getUrl($invitation->team));
        }

        // Accept the invitation
        Membership::firstOrCreate(
            [
                'team_id' => $invitation->team_id,
                'user_id' => $user->id,
            ],
            [
                'role' => $invitation->role->value,
            ]
        );

        $invitation->update(['accepted_at' => now()]);

        $user->switchTeam($invitation->team);

        return redirect(Filament::getUrl($invitation->team));
    }
}
