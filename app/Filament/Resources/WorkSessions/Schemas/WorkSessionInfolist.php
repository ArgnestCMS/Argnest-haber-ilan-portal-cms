<?php

namespace App\Filament\Resources\WorkSessions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class WorkSessionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('type'),
                TextEntry::make('status'),
                TextEntry::make('started_at')
                    ->dateTime(),
                TextEntry::make('ended_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('duration_minutes')
                    ->numeric(),
                TextEntry::make('ip_address')
                    ->placeholder('-'),
                TextEntry::make('device')
                    ->placeholder('-'),
                TextEntry::make('browser')
                    ->placeholder('-'),
                TextEntry::make('platform')
                    ->placeholder('-'),
                TextEntry::make('note')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
