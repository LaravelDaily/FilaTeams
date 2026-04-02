# FilaTeams

Team management plugin for Filament 5. This is the exact implementation of [Laravel 13 Starter Kit Teams](https://www.youtube.com/watch?v=mJtOxawILJk) functionality, just in Filament.

Adds full team functionality — creating teams, switching between them, inviting members, managing roles — wired into Filament's panel with zero configuration on your part. Uses the same database schema as the Laravel starter kit, so you can share team data between a Filament admin panel and a starter kit frontend.

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- Filament 5

This package is designed to be installed on a **fresh Filament project**, right after `filament:install --panels`. It creates its own database tables and takes over the panel's tenancy system, so it's best to set it up before building your resources and pages on top.

## Installation

### 1. Install via Composer

```bash
composer require laraveldaily/filateams
```

### 2. Update your User model

Add the `HasTeamMembership` interface and `HasTeams` trait:

```php
use LaravelDaily\FilaTeams\Concerns\HasTeams;
use LaravelDaily\FilaTeams\Contracts\HasTeamMembership;

class User extends Authenticatable implements HasTeamMembership
{
    use HasTeams;

    // ... rest of your model
}
```

### 3. Register the plugin in your PanelProvider

```php
use LaravelDaily\FilaTeams\FilaTeamsPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        // ... your existing config
        ->registration() // enable if not already
        ->plugin(FilaTeamsPlugin::make());
}
```

### 4. Run migrations

```bash
php artisan migrate
```

That's it. No additional configuration needed.

## What you get

- **Automatic personal team** — when a user registers, a personal team ("{name}'s Team") is created automatically
- **Team switcher** — Filament's built-in tenant switcher lets users switch between their teams
- **Create team** — available at `/admin/new`
- **Team settings page** — at `/admin/{team}/settings`, with sections for:
    - Updating team name
    - Managing members (change roles, remove members, leave team)
    - Inviting new members by email
    - Deleting the team (non-personal teams only, owner only)
- **Email invitations** — invited users receive an email with an accept link
- **Role-based permissions** — Owner, Admin, and Member roles with granular permissions

## Roles & Permissions

| Permission | Owner | Admin | Member |
|---|:---:|:---:|:---:|
| Update team name | Yes | Yes | - |
| Delete team | Yes | - | - |
| Add/remove members | Yes | - | - |
| Change member roles | Yes | - | - |
| Create invitations | Yes | Yes | - |
| Cancel invitations | Yes | Yes | - |

## Invitation flow

1. An Owner or Admin invites a user by email from the team settings page
2. The invited user receives an email with an accept link
3. If they're logged in and the email matches — they're added to the team immediately
4. If they're not logged in — they're redirected to login first, then back to accept
5. Invitations expire after 7 days (configurable)

## For existing users

If you install the package on a project that already has users, those users won't have teams yet. Create personal teams for them:

```php
use App\Models\User;
use LaravelDaily\FilaTeams\Actions\CreateTeam;

$action = new CreateTeam;

User::whereDoesntHave('teams')->each(function ($user) use ($action) {
    $action($user, [
        'name' => $user->name . "'s Team",
        'is_personal' => true,
    ]);
});
```

You can run this in tinker or in a seeder/migration.

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=filateams-config
```

This creates `config/filateams.php`:

```php
return [
    // Override model classes if you need to extend them
    'models' => [
        'team' => LaravelDaily\FilaTeams\Models\Team::class,
        'membership' => LaravelDaily\FilaTeams\Models\Membership::class,
        'invitation' => LaravelDaily\FilaTeams\Models\TeamInvitation::class,
    ],

    // How long invitations are valid
    'invitation' => [
        'expires_after_days' => 7,
    ],

    // Create a personal team when a user registers
    'create_personal_team_on_registration' => true,
];
```

## Database schema

The package creates three tables and adds one column:

**`teams`** — `id`, `name`, `slug` (unique), `is_personal`, `timestamps`, `soft_deletes`

**`team_members`** — `id`, `team_id`, `user_id`, `role`, `timestamps` (unique on `team_id + user_id`)

**`team_invitations`** — `id`, `code` (unique, 64 chars), `team_id`, `email`, `role`, `invited_by`, `expires_at`, `accepted_at`, `timestamps`

**`users`** — adds `current_team_id` (nullable foreign key to teams)

This schema matches the Laravel 13 starter kit exactly.

## License

MIT
