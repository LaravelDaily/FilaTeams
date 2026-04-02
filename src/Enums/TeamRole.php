<?php

namespace LaravelDaily\FilaTeams\Enums;

enum TeamRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    /**
     * @return array<int, string>
     */
    public function permissions(): array
    {
        return match ($this) {
            self::Owner => [
                'team:update', 'team:delete',
                'member:add', 'member:update', 'member:remove',
                'invitation:create', 'invitation:cancel',
            ],
            self::Admin => [
                'team:update',
                'invitation:create', 'invitation:cancel',
            ],
            self::Member => [],
        };
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions());
    }

    public function level(): int
    {
        return match ($this) {
            self::Owner => 3,
            self::Admin => 2,
            self::Member => 1,
        };
    }

    public function isAtLeast(TeamRole $role): bool
    {
        return $this->level() >= $role->level();
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function assignable(): array
    {
        return collect(self::cases())
            ->filter(fn (self $role) => $role !== self::Owner)
            ->map(fn (self $role) => ['value' => $role->value, 'label' => $role->label()])
            ->values()
            ->toArray();
    }
}
