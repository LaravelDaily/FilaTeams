<?php

namespace LaravelDaily\FilaTeams\Support;

readonly class TeamPermissions
{
    public function __construct(
        public bool $canUpdateTeam = false,
        public bool $canDeleteTeam = false,
        public bool $canAddMember = false,
        public bool $canUpdateMember = false,
        public bool $canRemoveMember = false,
        public bool $canCreateInvitation = false,
        public bool $canCancelInvitation = false,
    ) {}
}
