<?php

use LaravelDaily\FilaTeams\Models\Membership;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Models\TeamInvitation;

return [
    'models' => [
        'team' => Team::class,
        'membership' => Membership::class,
        'invitation' => TeamInvitation::class,
    ],
    'invitation' => [
        'expires_after_days' => 7,
    ],
    'create_personal_team_on_registration' => true,
];
