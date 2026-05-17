<?php

namespace App\Filament\Resources\LiveChatMessages\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LiveChatMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')->label('Kullanıcı')->placeholder('Sistem'),
                TextEntry::make('status')->label('Durum')->badge(),
                TextEntry::make('message')->label('Mesaj')->columnSpanFull(),
                TextEntry::make('moderator.name')->label('Son Moderatör')->placeholder('-'),
                TextEntry::make('moderated_at')->label('Son Moderasyon')->dateTime('d.m.Y H:i')->placeholder('-'),
                TextEntry::make('moderation_note')->label('Moderasyon Notu')->placeholder('-')->columnSpanFull(),
                TextEntry::make('ip_address')->label('IP')->placeholder('-'),
                TextEntry::make('created_at')->label('Gönderim')->dateTime('d.m.Y H:i')->placeholder('-'),
            ]);
    }
}
