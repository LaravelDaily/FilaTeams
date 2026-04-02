<?php

namespace LaravelDaily\FilaTeams\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Gate;
use LaravelDaily\FilaTeams\Rules\TeamName;

class EditTeam extends EditTenantProfile
{
    protected static ?string $slug = 'settings';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function getLabel(): string
    {
        return 'Team Settings';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Team Name')
                    ->required()
                    ->maxLength(255)
                    ->rules([new TeamName]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
                Livewire::make('filateams-members-table', [
                    'teamId' => $this->tenant->id,
                ]),
                Livewire::make('filateams-invitations-table', [
                    'teamId' => $this->tenant->id,
                ])->visible(fn () => auth()->user()->hasTeamPermission($this->tenant, 'invitation:create')),
                Section::make('Delete Team')
                    ->schema([
                        Actions::make([
                            Action::make('deleteTeam')
                                ->label('Delete Team')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->modalHeading('Delete Team')
                                ->modalDescription('Are you sure you want to delete this team? This action cannot be undone.')
                                ->modalSubmitActionLabel('Delete Team')
                                ->action(fn () => $this->deleteTeam()),
                        ]),
                    ])
                    ->visible(fn () => Gate::allows('delete', $this->tenant)),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return static::getUrl(tenant: $this->tenant);
    }

    public function deleteTeam(): void
    {
        $team = $this->tenant;

        if ($team->is_personal) {
            Notification::make()
                ->danger()
                ->title('Cannot delete personal team.')
                ->send();

            return;
        }

        // Switch all members to their personal or fallback team
        foreach ($team->members as $member) {
            if ($member->isCurrentTeam($team)) {
                $fallback = $member->personalTeam() ?? $member->fallbackTeam($team);
                $member->forceFill(['current_team_id' => $fallback?->id])->save();
            }
        }

        $team->invitations()->delete();
        $team->memberships()->delete();
        $team->delete();

        $this->redirect(Filament::getUrl(), navigate: FilamentView::hasSpaMode());
    }
}
