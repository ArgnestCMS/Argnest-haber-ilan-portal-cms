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
                    ->label('User'),
                TextEntry::make('moderator.name')
                    ->label('Moderator')
                    ->placeholder('-'),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('reason')
                    ->columnSpanFull(),
                TextEntry::make('expires_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
