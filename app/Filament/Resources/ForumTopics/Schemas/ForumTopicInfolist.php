<?php

namespace App\Filament\Resources\ForumTopics\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ForumTopicInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label('Başlık'),
                TextEntry::make('category.name')->label('Kategori'),
                TextEntry::make('user.name')->label('Kullanıcı')->placeholder('Sistem'),
                TextEntry::make('status')->label('Durum')->badge(),
                IconEntry::make('is_pinned')->label('Sabit')->boolean(),
                IconEntry::make('is_locked')->label('Kilitli')->boolean(),
                IconEntry::make('is_solved')->label('Çözüldü')->boolean(),
                TextEntry::make('lastPostUser.name')->label('Son Cevaplayan')->placeholder('-'),
                TextEntry::make('views')->label('Görüntülenme')->numeric(),
                TextEntry::make('content')->label('İçerik')->html()->columnSpanFull(),
                TextEntry::make('created_at')->label('Oluşturma')->dateTime()->placeholder('-'),
            ]);
    }
}
