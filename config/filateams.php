<?php

declare(strict_types=1);

use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Enums\TeamRole;
use LaravelDaily\FilaTeams\Models\Membership;
use LaravelDaily\FilaTeams\Enums\TeamPermission;
use LaravelDaily\FilaTeams\Models\TeamInvitation;

return [
    'enums' => [
        'role'       => TeamRole::class,
        'permission' => TeamPermission::class,
    ],
    'models' => [
        'team'       => Team::class,
        'membership' => Membership::class,
        'invitation' => TeamInvitation::class,
    ],
    'invitation' => [
        'expires_after_days' => 7,
    ],
    'create_personal_team_on_registration' => true,
];
