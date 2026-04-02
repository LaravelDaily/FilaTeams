<?php

namespace LaravelDaily\FilaTeams\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Models\TeamInvitation;

class UniqueTeamInvitation implements ValidationRule
{
    public function __construct(
        protected Team $team,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->team->members()->where('email', $value)->exists()) {
            $fail('This user is already a team member.');

            return;
        }

        $hasPendingInvitation = TeamInvitation::where('team_id', $this->team->id)
            ->where('email', $value)
            ->whereNull('accepted_at')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($hasPendingInvitation) {
            $fail('A pending invitation already exists for this email.');
        }
    }
}
