<?php

namespace App\Filament\Resources\UserPunishments\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserPunishmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Kullanıcı'),
                TextEntry::make('moderator.name')
                    ->label('Moderatör')
                    ->placeholder('-'),
                TextEntry::make('type')
                    ->label('Ceza Türü')
                    ->badge(),
                TextEntry::make('reason')
                    ->label('Sebep')
                    ->columnSpanFull(),
                TextEntry::make('expires_at')
                    ->label('Bitiş Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
