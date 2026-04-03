<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LaravelDaily\FilaTeams\Facades\FilaTeams;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\HasCurrentTenantLabel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use LaravelDaily\FilaTeams\Database\Factories\TeamFactory;
use LaravelDaily\FilaTeams\Concerns\GeneratesUniqueTeamSlugs;

class Team extends Model implements HasCurrentTenantLabel
{
    /** @use HasFactory<TeamFactory> */
    use GeneratesUniqueTeamSlugs;

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'is_personal',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getCurrentTenantLabel(): string
    {
        return $this->name;
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model', 'App\\Models\\User'), 'team_members')
            ->using(Membership::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function owner(): ?Model
    {
        return $this->members()->wherePivot('role', FilaTeams::ownerRole()->value)->first();
    }

    protected static function newFactory(): TeamFactory
    {
        return TeamFactory::new();
    }

    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
        ];
    }
}
