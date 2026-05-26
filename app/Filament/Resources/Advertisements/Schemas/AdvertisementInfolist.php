<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AdvertisementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('position'),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('url')
                    ->placeholder('-'),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('views')
                    ->numeric(),
                TextEntry::make('clicks')
                    ->numeric(),
                TextEntry::make('start_date')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('end_date')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
