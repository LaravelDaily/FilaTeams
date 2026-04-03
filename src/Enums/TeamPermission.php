<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Enums;

enum TeamPermission: string implements \LaravelDaily\FilaTeams\Contracts\TeamPermissionContract
{
    case UpdateTeam = 'team:update';
    case DeleteTeam = 'team:delete';

    case AddMember    = 'member:add';
    case UpdateMember = 'member:update';
    case RemoveMember = 'member:remove';

    case CreateInvitation = 'invitation:create';
    case CancelInvitation = 'invitation:cancel';
}
