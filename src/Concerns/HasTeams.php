<?php

namespace LaravelDaily\FilaTeams\Concerns;

use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use LaravelDaily\FilaTeams\Enums\TeamRole;
use LaravelDaily\FilaTeams\Models\Membership;
use LaravelDaily\FilaTeams\Models\Team;

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
        return $this->hasMany(Membership::class)->where('role', TeamRole::Owner->value);
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
        return $this->teamRole($team) === TeamRole::Owner;
    }

    public function teamRole(Team $team): ?TeamRole
    {
        $membership = $this->teamMemberships()->where('team_id', $team->id)->first();

        return $membership?->role;
    }

    public function hasTeamPermission(Team $team, string $permission): bool
    {
        $role = $this->teamRole($team);

        return $role !== null && $role->hasPermission($permission);
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
    public function getTenants(Panel $panel): array|Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->belongsToTeam($tenant);
    }
}
