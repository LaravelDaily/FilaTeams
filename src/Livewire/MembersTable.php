<?php

namespace LaravelDaily\FilaTeams\Livewire;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use LaravelDaily\FilaTeams\Enums\TeamRole;
use LaravelDaily\FilaTeams\Models\Membership;
use LaravelDaily\FilaTeams\Models\Team;

class MembersTable extends TableWidget
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
            ->query(Membership::query()->where('team_id', $this->teamId)->with('user'))
            ->heading('Team Members')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->formatStateUsing(fn (TeamRole $state) => $state->label())
                    ->badge()
                    ->color(fn (TeamRole $state) => match ($state) {
                        TeamRole::Owner => 'danger',
                        TeamRole::Admin => 'warning',
                        TeamRole::Member => 'info',
                    }),
            ])
            ->actions([
                Action::make('changeRole')
                    ->label('Change Role')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        Select::make('role')
                            ->label('Role')
                            ->options(collect(TeamRole::assignable())->pluck('label', 'value'))
                            ->required(),
                    ])
                    ->fillForm(fn (Membership $record) => ['role' => $record->role->value])
                    ->action(function (Membership $record, array $data) {
                        $record->update(['role' => $data['role']]);

                        Notification::make()
                            ->success()
                            ->title('Role updated.')
                            ->send();
                    })
                    ->visible(fn (Membership $record) => $user->hasTeamPermission($team, 'member:update') && $record->role !== TeamRole::Owner),

                Action::make('remove')
                    ->label('Remove')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Membership $record) use ($team) {
                        $member = $record->user;

                        $record->delete();

                        if ($member->isCurrentTeam($team)) {
                            $fallback = $member->personalTeam() ?? $member->fallbackTeam($team);
                            $member->forceFill(['current_team_id' => $fallback?->id])->save();
                        }

                        Notification::make()
                            ->success()
                            ->title('Member removed.')
                            ->send();
                    })
                    ->visible(fn (Membership $record) => $user->hasTeamPermission($team, 'member:remove') && $record->role !== TeamRole::Owner && $record->user_id !== $user->id),

                Action::make('leave')
                    ->label('Leave Team')
                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Membership $record) use ($team) {
                        $member = $record->user;

                        $record->delete();

                        if ($member->isCurrentTeam($team)) {
                            $fallback = $member->personalTeam() ?? $member->fallbackTeam($team);
                            $member->forceFill(['current_team_id' => $fallback?->id])->save();
                        }

                        Notification::make()
                            ->success()
                            ->title('You have left the team.')
                            ->send();

                        $this->redirect(route('filament.admin.pages.dashboard'));
                    })
                    ->visible(fn (Membership $record) => $record->user_id === $user->id && $record->role !== TeamRole::Owner),
            ])
            ->paginated(false);
    }
}
