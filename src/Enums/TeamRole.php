<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Enums;

use BackedEnum;
use Illuminate\Contracts\Support\Htmlable;
use LaravelDaily\FilaTeams\Contracts\TeamRoleContract;

enum TeamRole: string implements TeamRoleContract
{
    case Owner  = 'owner';
    case Admin  = 'admin';
    case Member = 'member';

    public static function owner(): static
    {
        return self::Owner;
    }

    public static function default(): static
    {
        return self::Member;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function assignable(): array
    {
        return collect(self::cases())
            ->filter(fn (self $role) => $role !== self::Owner)
            ->map(fn (self $role) => ['value' => $role->value, 'label' => $role->getLabel()])
            ->values()
            ->toArray();
    }

    public function getLabel(): string | Htmlable | null
    {
        return __('filateams::filateams.roles.' . $this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Owner  => 'danger',
            self::Admin  => 'warning',
            self::Member => 'info',
        };
    }

    /**
     * @return array<int, string|TeamPermission>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Owner => config('filateams.enums.permission', TeamPermission::class)::cases(),
            self::Admin => [
                TeamPermission::UpdateTeam,
                TeamPermission::CreateInvitation,
                TeamPermission::CancelInvitation,
            ],
            self::Member => [],
        };
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, array_map(
            static fn ($p) => $p instanceof BackedEnum ? $p->value : $p,
            $this->permissions()
        ), strict: true);
    }

    public function level(): int
    {
        return match ($this) {
            self::Owner  => 3,
            self::Admin  => 2,
            self::Member => 1,
        };
    }

    public function isAtLeast(TeamRoleContract $role): bool
    {
        return $this->level() >= $role->level();
    }
}
