<?php

declare(strict_types=1);

namespace LaravelDaily\FilaTeams\Pages;

use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use LaravelDaily\FilaTeams\Rules\TeamName;
use LaravelDaily\FilaTeams\Actions\CreateTeam;

class CreateTeamPage extends RegisterTenant
{
    protected static ?string $slug = 'new';

    public static function getLabel(): string
    {
        return __('filateams::filateams.pages.create_team.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('filateams::filateams.fields.team_name.label'))
                    ->required()
                    ->maxLength(255)
                    ->rules([new TeamName])
                    ->autofocus(),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        return app(CreateTeam::class)->handle(auth()->user(), $data);
    }
}
