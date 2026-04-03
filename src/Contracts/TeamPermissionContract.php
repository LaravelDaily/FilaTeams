<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Contracts;

/**
 * Marker contract for team permission enums.
 *
 * Implementing class must be a string-backed PHP enum, e.g.:
 *   enum MyTeamPermission: string implements TeamPermissionContract { ... }
 *
 * No additional methods are required — enum cases and ->value are sufficient.
 */
interface TeamPermissionContract {}
