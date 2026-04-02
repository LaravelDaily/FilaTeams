<?php

namespace LaravelDaily\FilaTeams\Contracts;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;

interface HasTeamMembership extends FilamentUser, HasTenants {}
