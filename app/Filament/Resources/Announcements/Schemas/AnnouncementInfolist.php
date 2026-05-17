<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AnnouncementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('slug'),
                TextEntry::make('summary')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('content')
                    ->columnSpanFull(),
                TextEntry::make('institution')
                    ->placeholder('-'),
                TextEntry::make('city')
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->placeholder('-'),
                TextEntry::make('publish_date')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deadline')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('source')
                    ->placeholder('-'),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('document')
                    ->placeholder('-'),
                IconEntry::make('is_headline')
                    ->boolean(),
                IconEntry::make('comments_enabled')
                    ->boolean(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('views')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
