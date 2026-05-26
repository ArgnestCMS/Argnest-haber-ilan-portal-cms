<?php

namespace App\Filament\Resources\ForumCategories\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ForumCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Kategori Adı'),
                TextEntry::make('slug')->label('URL'),
                TextEntry::make('description')->label('Açıklama')->placeholder('-'),
                IconEntry::make('is_active')->label('Aktif')->boolean(),
                TextEntry::make('sort_order')->label('Sıralama')->numeric(),
                TextEntry::make('created_at')->label('Oluşturma')->dateTime()->placeholder('-'),
                TextEntry::make('updated_at')->label('Güncelleme')->dateTime()->placeholder('-'),
            ]);
    }
}
