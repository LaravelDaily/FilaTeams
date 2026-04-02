<?php

namespace LaravelDaily\FilaTeams;

use Filament\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use LaravelDaily\FilaTeams\Listeners\CreatePersonalTeam;
use LaravelDaily\FilaTeams\Livewire\InvitationsManager;
use LaravelDaily\FilaTeams\Livewire\MembersTable;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Policies\TeamPolicy;
use Livewire\Livewire;

class FilaTeamsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filateams.php', 'filateams');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filateams');

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Livewire::component('filateams-members-table', MembersTable::class);
        Livewire::component('filateams-invitations-table', InvitationsManager::class);

        Gate::policy(Team::class, TeamPolicy::class);

        Event::listen(Registered::class, CreatePersonalTeam::class);

        $this->publishes([
            __DIR__.'/../config/filateams.php' => config_path('filateams.php'),
        ], 'filateams-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'filateams-migrations');
    }
}
