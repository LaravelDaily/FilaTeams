<?php

namespace LaravelDaily\FilaTeams\Actions;

use Illuminate\Support\Facades\DB;
use LaravelDaily\FilaTeams\Enums\TeamRole;
use LaravelDaily\FilaTeams\Models\Membership;
use LaravelDaily\FilaTeams\Models\Team;

class CreateTeam
{
    /**
     * @param  array{name: string, is_personal?: bool}  $data
     */
    public function __invoke($user, array $data): Team
    {
        return DB::transaction(function () use ($user, $data) {
            $team = Team::create([
                'name' => $data['name'],
                'is_personal' => $data['is_personal'] ?? false,
            ]);

            Membership::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'role' => TeamRole::Owner->value,
            ]);

            $user->switchTeam($team);

            return $team;
        });
    }
}
