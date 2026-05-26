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
                    ->label('Kullanıcı'),
                TextEntry::make('type')
                    ->label('Tür'),
                TextEntry::make('status')
                    ->label('Durum'),
                TextEntry::make('started_at')
                    ->label('Başlangıç')
                    ->dateTime(),
                TextEntry::make('ended_at')
                    ->label('Bitiş')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('duration_minutes')
                    ->label('Süre (dk)')
                    ->numeric(),
                TextEntry::make('ip_address')
                    ->label('IP Adresi')
                    ->placeholder('-'),
                TextEntry::make('device')
                    ->label('Cihaz')
                    ->placeholder('-'),
                TextEntry::make('browser')
                    ->label('Tarayıcı')
                    ->placeholder('-'),
                TextEntry::make('platform')
                    ->label('Platform')
                    ->placeholder('-'),
                TextEntry::make('note')
                    ->label('Not')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
