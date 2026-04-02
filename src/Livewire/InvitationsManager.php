<?php

namespace LaravelDaily\FilaTeams\Livewire;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use LaravelDaily\FilaTeams\Enums\TeamRole;
use LaravelDaily\FilaTeams\Models\Team;
use LaravelDaily\FilaTeams\Models\TeamInvitation;
use LaravelDaily\FilaTeams\Notifications\TeamInvitationNotification;
use LaravelDaily\FilaTeams\Rules\UniqueTeamInvitation;

class InvitationsManager extends TableWidget
{
    protected static bool $isDiscovered = false;

    public int $teamId;

    public function getTeam(): Team
    {
        return Team::findOrFail($this->teamId);
    }

    public function table(Table $table): Table
    {
        $team = $this->getTeam();
        $user = auth()->user();

        return $table
            ->query(
                TeamInvitation::query()
                    ->where('team_id', $this->teamId)
                    ->whereNull('accepted_at')
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->with('inviter')
            )
            ->heading('Pending Invitations')
            ->headerActions([
                Action::make('invite')
                    ->label('Invite Member')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->rules([new UniqueTeamInvitation($team)]),
                        Select::make('role')
                            ->label('Role')
                            ->options(collect(TeamRole::assignable())->pluck('label', 'value'))
                            ->default(TeamRole::Member->value)
                            ->required(),
                    ])
                    ->action(function (array $data) use ($team, $user) {
                        $invitation = TeamInvitation::create([
                            'team_id' => $team->id,
                            'email' => $data['email'],
                            'role' => $data['role'],
                            'invited_by' => $user->id,
                            'expires_at' => now()->addDays(config('filateams.invitation.expires_after_days', 7)),
                        ]);

                        NotificationFacade::route('mail', $data['email'])
                            ->notify(new TeamInvitationNotification($invitation));

                        Notification::make()
                            ->success()
                            ->title('Invitation sent to '.$data['email'].'.')
                            ->send();
                    })
                    ->visible(fn () => $user->hasTeamPermission($team, 'invitation:create')),
            ])
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->formatStateUsing(fn (TeamRole $state) => $state->label())
                    ->badge(),
                TextColumn::make('inviter.name')
                    ->label('Invited By'),
                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime(),
            ])
            ->actions([
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (TeamInvitation $record) {
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title('Invitation cancelled.')
                            ->send();
                    })
                    ->visible(fn () => $user->hasTeamPermission($team, 'invitation:cancel')),
            ])
            ->emptyStateHeading('No pending invitations')
            ->emptyStateDescription('Invite team members by clicking the button above.')
            ->paginated(false);
    }
}
