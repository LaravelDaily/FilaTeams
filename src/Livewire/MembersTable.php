<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Livewire;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Widgets\TableWidget;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use LaravelDaily\FilaTeams\Models\Team;
use Filament\Notifications\Notification;
use LaravelDaily\FilaTeams\Facades\FilaTeams;
use LaravelDaily\FilaTeams\Models\Membership;

class MembersTable extends TableWidget
{
    public int $teamId;

    protected static bool $isDiscovered = false;

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
            ->heading(__('filateams::filateams.tables.members.heading'))
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('filateams::filateams.fields.name.label'))
                    ->searchable(),
                TextColumn::make('user.email')
                    ->label(__('filateams::filateams.fields.email.label'))
                    ->searchable(),
                TextColumn::make('role')
                    ->label(__('filateams::filateams.fields.role.label'))
                    ->badge(),
            ])
            ->recordActions([
                Action::make('changeRole')
                    ->label(__('filateams::filateams.actions.change_role.label'))
                    ->icon(Heroicon::OutlinedPencil)
                    ->schema([
                        Select::make('role')
                            ->label(__('filateams::filateams.fields.role.label'))
                            ->options(collect(FilaTeams::assignableRoles())->pluck('label', 'value'))
                            ->required(),
                    ])
                    ->fillForm(fn (Membership $record) => ['role' => $record->role->value])
                    ->action(function (Membership $record, array $data): void {
                        $record->update(['role' => $data['role']]);

                        Notification::make()
                            ->success()
                            ->title(__('filateams::filateams.notifications.role_updated.title'))
                            ->send();
                    })
                    ->visible(fn (Membership $record) => $user->hasTeamPermission($team, 'member:update') && $record->role !== FilaTeams::ownerRole()),

                Action::make('remove')
                    ->label(__('filateams::filateams.actions.remove_member.label'))
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Membership $record) use ($team): void {
                        $member = $record->user;

                        $record->delete();

                        if ($member->isCurrentTeam($team)) {
                            $fallback = $member->personalTeam() ?? $member->fallbackTeam($team);
                            $member->forceFill(['current_team_id' => $fallback?->id])->save();
                        }

                        Notification::make()
                            ->success()
                            ->title(__('filateams::filateams.notifications.member_removed.title'))
                            ->send();
                    })
                    ->visible(fn (Membership $record) => $user->hasTeamPermission($team, 'member:remove') && $record->role !== FilaTeams::ownerRole() && $record->user_id !== $user->id),

                Action::make('leave')
                    ->label(__('filateams::filateams.actions.leave_team.label'))
                    ->icon(Heroicon::OutlinedArrowRightStartOnRectangle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Membership $record) use ($team): void {
                        $member = $record->user;

                        $record->delete();

                        if ($member->isCurrentTeam($team)) {
                            $fallback = $member->personalTeam() ?? $member->fallbackTeam($team);
                            $member->forceFill(['current_team_id' => $fallback?->id])->save();
                        }

                        Notification::make()
                            ->success()
                            ->title(__('filateams::filateams.notifications.left_team.title'))
                            ->send();

                        $this->redirect(Filament::getCurrentPanel()->getUrl());
                    })
                    ->visible(fn (Membership $record) => $record->user_id === $user->id && $record->role !== FilaTeams::ownerRole()),
            ])
            ->paginated(false);
    }
}
