<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Concerns;

use BackedEnum;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Facades\FilaTeams;
use LaravelDaily\FilaTeams\Models\Membership;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LaravelDaily\FilaTeams\Contracts\TeamRoleContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use LaravelDaily\FilaTeams\Contracts\TeamPermissionContract;

trait HasTeams
{
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Membership::class)->where('role', FilaTeams::ownerRole()->value);
    }

    public function teamMemberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function personalTeam(): ?Team
    {
        return $this->teams()->where('is_personal', true)->first();
    }

    public function switchTeam(Team $team): bool
    {
        if (! $this->belongsToTeam($team)) {
            return false;
        }

        $this->forceFill(['current_team_id' => $team->id])->save();

        $this->setRelation('currentTeam', $team);

        return true;
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->teams()->where('teams.id', $team->id)->exists();
    }

    public function isCurrentTeam(Team $team): bool
    {
        return $this->current_team_id === $team->id;
    }

    public function ownsTeam(Team $team): bool
    {
        return $this->teamRole($team) === FilaTeams::ownerRole();
    }

    public function teamRole(Team $team): ?TeamRoleContract
    {
        $membership = $this->teamMemberships()->where('team_id', $team->id)->first();

        return $membership?->role;
    }

    public function hasTeamPermission(Team $team, string | TeamPermissionContract $permission): bool
    {
        $role  = $this->teamRole($team);
        $value = $permission instanceof BackedEnum ? $permission->value : $permission;

        return $role !== null && $role->hasPermission($value);
    }

    public function fallbackTeam(?Team $excluding = null): ?Team
    {
        return $this->teams()
            ->when($excluding, fn ($query) => $query->where('teams.id', '!=', $excluding->id))
            ->orderBy('name')
            ->first();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    /**
     * @return array<Model>|Collection
     */
    public function getTenants(Panel $panel): array | Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->belongsToTeam($tenant);
    }
}
