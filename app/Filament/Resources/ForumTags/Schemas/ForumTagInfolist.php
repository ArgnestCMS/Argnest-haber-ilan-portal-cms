<?php

namespace App\Filament\Resources\ForumTags\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ForumTagInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Etiket'),
                TextEntry::make('slug')->label('URL'),
                TextEntry::make('color')->label('Renk'),
                IconEntry::make('is_active')->label('Aktif')->boolean(),
                TextEntry::make('sort_order')->label('Siralama')->numeric(),
                TextEntry::make('topics_count')->label('Konu')->counts('topics'),
                TextEntry::make('created_at')->label('Olusturma')->dateTime()->placeholder('-'),
            ]);
    }
}
