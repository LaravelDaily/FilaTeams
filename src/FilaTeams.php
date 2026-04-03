<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams;

use LaravelDaily\FilaTeams\Enums\TeamRole;
use LaravelDaily\FilaTeams\Enums\TeamPermission;
use LaravelDaily\FilaTeams\Contracts\TeamRoleContract;
use LaravelDaily\FilaTeams\Contracts\TeamPermissionContract;

class FilaTeams
{
    /**
     * @return class-string<TeamRoleContract>
     */
    public function roleClass(): string
    {
        return config('filateams.enums.role', TeamRole::class);
    }

    /**
     * @return class-string<TeamPermissionContract>
     */
    public function permissionClass(): string
    {
        return config('filateams.enums.permission', TeamPermission::class);
    }

    public function ownerRole(): TeamRoleContract
    {
        return $this->roleClass()::owner();
    }

    public function defaultRole(): TeamRoleContract
    {
        return $this->roleClass()::default();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function assignableRoles(): array
    {
        return $this->roleClass()::assignable();
    }
}
