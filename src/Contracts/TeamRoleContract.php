<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Contracts;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Contract for team role enums.
 *
 * Implementing class must be a string-backed PHP enum, e.g.:
 *   enum MyTeamRole: string implements TeamRoleContract { ... }
 *
 * @property string $value The string value of this enum case (e.g. 'owner', 'admin')
 * @property string $name The name of this enum case (e.g. 'Owner', 'Admin')
 *
 * @method static static from(string $value) Create a case from its string value
 * @method static static|null tryFrom(string $value) Create a case from its string value, or null
 * @method static static[] cases() Return all cases of the enum
 * @method static static owner() Return the case representing the team owner
 * @method static static default()                 Return the default assignable role (e.g. for invitations)
 * @method static array<int, array{value: string, label: string}> assignable() Return all non-owner roles for dropdowns
 * @method string|array|null getColor() Return the Filament badge color (from HasColor)
 * @method array<int, string|TeamPermissionContract> permissions() Return permission strings for this role
 * @method bool hasPermission(string $permission) Check whether this role has the given permission
 * @method int level()                             Return the numeric hierarchy level (higher = more privileged)
 * @method bool isAtLeast(self $role) Return true if this role's level is >= the given role's level
 */
interface TeamRoleContract extends HasColor, HasLabel
{
    /**
     * Returns the case that represents the team owner.
     * Replaces all hardcoded TeamRole::Owner references.
     */
    public static function owner(): static;

    /**
     * Returns the default role used when creating an invitation.
     * Replaces all hardcoded TeamRole::Member references.
     */
    public static function default(): static;

    /**
     * Returns assignable roles (excluding owner) as [{value, label}] arrays.
     * Used to populate role dropdowns in the UI.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function assignable(): array;

    /**
     * Returns the permission strings granted to this role.
     *
     * @return array<int, string|TeamPermissionContract>
     */
    public function permissions(): array;

    public function hasPermission(string $permission): bool;

    /**
     * Returns a numeric hierarchy level. Higher = more privileged.
     */
    public function level(): int;

    public function isAtLeast(self $role): bool;
}
