<?php

namespace App\Filament\Resources\ActivityLogs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ActivityLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(null),
                TextInput::make('action')
                    ->required(),
                TextInput::make('description')
                    ->default(null),
                TextInput::make('ip_address')
                    ->default(null),
                Textarea::make('user_agent')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('device')
                    ->default(null),
                TextInput::make('browser')
                    ->default(null),
                TextInput::make('platform')
                    ->default(null),
                TextInput::make('url')
                    ->url()
                    ->default(null),
                Textarea::make('properties')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
