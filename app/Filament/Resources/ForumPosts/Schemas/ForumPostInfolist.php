<?php

namespace App\Filament\Resources\ForumPosts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ForumPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('topic.title')->label('Konu'),
                TextEntry::make('user.name')->label('Kullanıcı')->placeholder('Sistem'),
                TextEntry::make('status')->label('Durum')->badge(),
                TextEntry::make('content')->label('Cevap')->html()->columnSpanFull(),
                TextEntry::make('moderator.name')->label('Moderatör')->placeholder('-'),
                TextEntry::make('moderated_at')->label('Moderasyon Tarihi')->dateTime()->placeholder('-'),
                TextEntry::make('moderation_note')->label('Moderasyon Notu')->placeholder('-'),
                IconEntry::make('is_edited')->label('Düzenlendi')->boolean(),
                TextEntry::make('created_at')->label('Oluşturma')->dateTime()->placeholder('-'),
            ]);
    }
}
