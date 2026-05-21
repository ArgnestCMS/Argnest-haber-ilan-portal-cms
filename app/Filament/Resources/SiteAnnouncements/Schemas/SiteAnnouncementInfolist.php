<?php

namespace App\Filament\Resources\SiteAnnouncements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiteAnnouncementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('text')
                    ->label('Duyuru Metni')
                    ->columnSpanFull(),
                TextEntry::make('icon')
                    ->label('İkon')
                    ->placeholder('-'),
                TextEntry::make('link_url')
                    ->label('Link')
                    ->placeholder('-'),
                TextEntry::make('link_target')
                    ->label('Link Hedefi'),
                TextEntry::make('sort_order')
                    ->label('Sıra'),
                IconEntry::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                TextEntry::make('starts_at')
                    ->label('Yayın Başlangıcı')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('ends_at')
                    ->label('Yayın Bitişi')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
