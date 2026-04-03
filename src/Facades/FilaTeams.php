<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelDaily\FilaTeams\Contracts\TeamRoleContract;
use LaravelDaily\FilaTeams\Contracts\TeamPermissionContract;

/**
 * @method static class-string<TeamRoleContract> roleClass()
 * @method static class-string<TeamPermissionContract> permissionClass()
 * @method static TeamRoleContract ownerRole()
 * @method static TeamRoleContract defaultRole()
 * @method static array<int, array{value: string, label: string}> assignableRoles()
 *
 * @see \LaravelDaily\FilaTeams\FilaTeams
 */
class FilaTeams extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filateams';
    }
}
