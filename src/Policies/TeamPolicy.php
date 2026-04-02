<?php

namespace LaravelDaily\FilaTeams\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use LaravelDaily\FilaTeams\Models\Team;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function view($user, Team $team): bool
    {
        return $user->belongsToTeam($team);
    }

    public function update($user, Team $team): bool
    {
        return $user->hasTeamPermission($team, 'team:update');
    }

    public function delete($user, Team $team): bool
    {
        return ! $team->is_personal && $user->hasTeamPermission($team, 'team:delete');
    }

    public function addMember($user, Team $team): bool
    {
        return $user->hasTeamPermission($team, 'member:add');
    }

    public function updateMember($user, Team $team): bool
    {
        return $user->hasTeamPermission($team, 'member:update');
    }

    public function removeMember($user, Team $team): bool
    {
        return $user->hasTeamPermission($team, 'member:remove');
    }

    public function inviteMember($user, Team $team): bool
    {
        return $user->hasTeamPermission($team, 'invitation:create');
    }

    public function cancelInvitation($user, Team $team): bool
    {
        return $user->hasTeamPermission($team, 'invitation:cancel');
    }
}
