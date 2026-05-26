<?php

namespace App\Filament\Resources\WorkSessions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class WorkSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('type')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                DateTimePicker::make('started_at')
                    ->required(),
                DateTimePicker::make('ended_at'),
                TextInput::make('duration_minutes')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('ip_address')
                    ->default(null),
                TextInput::make('device')
                    ->default(null),
                TextInput::make('browser')
                    ->default(null),
                TextInput::make('platform')
                    ->default(null),
                Textarea::make('note')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
