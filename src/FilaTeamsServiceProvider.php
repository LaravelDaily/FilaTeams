<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams;

use RuntimeException;
use Livewire\Livewire;
use Filament\Auth\Events\Registered;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Policies\TeamPolicy;
use LaravelDaily\FilaTeams\Livewire\MembersTable;
use LaravelDaily\FilaTeams\Contracts\TeamRoleContract;
use LaravelDaily\FilaTeams\Livewire\InvitationsManager;
use LaravelDaily\FilaTeams\Listeners\CreatePersonalTeam;
use LaravelDaily\FilaTeams\Contracts\TeamPermissionContract;

class FilaTeamsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/filateams.php', 'filateams');

        $this->app->singleton('filateams', fn () => new FilaTeams());
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'filateams');

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'filateams');

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        Livewire::component('filateams-members-table', MembersTable::class);
        Livewire::component('filateams-invitations-table', InvitationsManager::class);

        Gate::policy(Team::class, TeamPolicy::class);

        Event::listen(Registered::class, CreatePersonalTeam::class);

        $roleClass = config('filateams.enums.role');
        throw_unless(
            is_a($roleClass, TeamRoleContract::class, true),
            RuntimeException::class,
            "filateams.enums.role [{$roleClass}] must implement TeamRoleContract."
        );

        $permClass = config('filateams.enums.permission');
        throw_unless(
            is_a($permClass, TeamPermissionContract::class, true),
            RuntimeException::class,
            "filateams.enums.permission [{$permClass}] must implement TeamPermissionContract."
        );

        $this->publishes([
            __DIR__ . '/../config/filateams.php' => config_path('filateams.php'),
        ], 'filateams-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'filateams-migrations');

        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/filateams'),
        ], 'filateams-translations');
    }
}
