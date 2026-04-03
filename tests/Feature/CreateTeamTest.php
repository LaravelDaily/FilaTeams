<?php

declare(strict_types=1);

use Tests\Models\User;
use LaravelDaily\FilaTeams\Facades\FilaTeams;
use LaravelDaily\FilaTeams\Actions\CreateTeam;

beforeEach(function (): void {
    $this->user   = User::factory()->create();
    $this->action = new CreateTeam();
});

it('creates a team with the given name', function (): void {
    $team = $this->action->handle($this->user, ['name' => 'Acme Corp']);

    expect($team->name)->toBe('Acme Corp');
    $this->assertDatabaseHas('teams', ['name' => 'Acme Corp']);
});

it('generates a slug from the team name', function (): void {
    $team = $this->action->handle($this->user, ['name' => 'Acme Corp']);

    expect($team->slug)->toBe('acme-corp');
});

it('creates a non-personal team by default', function (): void {
    $team = $this->action->handle($this->user, ['name' => 'Acme Corp']);

    expect($team->is_personal)->toBeFalse();
});

it('can create a personal team', function (): void {
    $team = $this->action->handle($this->user, ['name' => 'My Team', 'is_personal' => true]);

    expect($team->is_personal)->toBeTrue();
});

it('makes the user the owner of the team', function (): void {
    $team = $this->action->handle($this->user, ['name' => 'Acme Corp']);

    $this->assertDatabaseHas('team_members', [
        'team_id' => $team->id,
        'user_id' => $this->user->id,
        'role'    => FilaTeams::ownerRole()->value,
    ]);
});

it('sets the new team as the current team for the user', function (): void {
    $team = $this->action->handle($this->user, ['name' => 'Acme Corp']);

    expect($this->user->fresh()->currentTeam->id)->toBe($team->id);
});

it('returns the created team', function (): void {
    $team = $this->action->handle($this->user, ['name' => 'Acme Corp']);

    expect($team)->toBeInstanceOf(LaravelDaily\FilaTeams\Models\Team::class)
        ->and($team->exists)->toBeTrue();
});
