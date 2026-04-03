<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Actions;

use Illuminate\Support\Facades\DB;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Enums\TeamRole;
use LaravelDaily\FilaTeams\Models\Membership;
use Illuminate\Contracts\Auth\Authenticatable;

class CreateTeam
{
    /**
     * @param  array{name: string, is_personal?: bool}  $data
     */
    public function handle(Authenticatable $user, array $data): Team
    {
        return DB::transaction(function () use ($user, $data) {
            $team = Team::create([
                'name'        => $data['name'],
                'is_personal' => $data['is_personal'] ?? false,
            ]);

            Membership::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'role'    => TeamRole::Owner->value,
            ]);

            $user->switchTeam($team);

            return $team;
        });
    }
}
