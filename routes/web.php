<?php

use Illuminate\Support\Facades\Route;
use LaravelDaily\FilaTeams\Http\Controllers\AcceptInvitationController;

Route::get('/team-invitations/{code}/accept', AcceptInvitationController::class)
    ->middleware('web')
    ->name('filateams.invitations.accept');
