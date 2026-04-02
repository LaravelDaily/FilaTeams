<?php

namespace LaravelDaily\FilaTeams\Listeners;

use Filament\Auth\Events\Registered;
use LaravelDaily\FilaTeams\Actions\CreateTeam;

class CreatePersonalTeam
{
    public function handle(Registered $event): void
    {
        if (! config('filateams.create_personal_team_on_registration', true)) {
            return;
        }

        $user = $event->getUser();

        $action = new CreateTeam;

        $action($user, [
            'name' => $user->name."'s Team",
            'is_personal' => true,
        ]);
    }
}
