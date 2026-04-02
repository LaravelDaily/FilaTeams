<?php

namespace LaravelDaily\FilaTeams;

use Filament\Contracts\Plugin;
use Filament\Panel;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Pages\CreateTeamPage;
use LaravelDaily\FilaTeams\Pages\EditTeam;

class FilaTeamsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filateams';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->tenant(Team::class, slugAttribute: 'slug')
            ->tenantRegistration(CreateTeamPage::class)
            ->tenantProfile(EditTeam::class);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
