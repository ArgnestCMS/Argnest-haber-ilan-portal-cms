<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GalleryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('user.name')
                    ->label('Kullanıcı')
                    ->placeholder('-'),
                TextEntry::make('category.name')
                    ->label('Kategori')
                    ->placeholder('-'),
                TextEntry::make('title')
                    ->label('Başlık'),
                TextEntry::make('slug')
                    ->label('URL'),
                TextEntry::make('description')
                    ->label('Açıklama')
                    ->placeholder('-')
                    ->columnSpanFull(),
                ImageEntry::make('cover_image')
                    ->label('Kapak Görseli')
                    ->placeholder('-'),
                TextEntry::make('views')
                    ->label('Görüntülenme')
                    ->numeric(),
                IconEntry::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                IconEntry::make('is_featured')
                    ->label('Öne Çıkan')
                    ->boolean(),
                TextEntry::make('published_at')
                    ->label('Yayın Tarihi')
                    ->dateTime()
                    ->placeholder('-'),
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
