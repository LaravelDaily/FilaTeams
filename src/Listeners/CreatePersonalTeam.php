<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Listeners;

use Filament\Auth\Events\Registered;
use LaravelDaily\FilaTeams\Actions\CreateTeam;

class CreatePersonalTeam
{
    public function __construct(private readonly CreateTeam $action) {}

    public function handle(Registered $event): void
    {
        if (! config('filateams.create_personal_team_on_registration', true)) {
            return;
        }

        $user = $event->getUser();

        $this->action->handle($user, [
            'name'        => __('filateams::filateams.personal_team_name', ['name' => $user->name]),
            'is_personal' => true,
        ]);
    }
}
