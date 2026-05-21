<?php

namespace App\Filament\Resources\Polls\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PollInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label('Başlık'),
                TextEntry::make('subtitle')->label('Alt Başlık')->placeholder('-'),
                TextEntry::make('topic')->label('Konu')->placeholder('-'),
                IconEntry::make('is_active')->label('Aktif')->boolean(),
                IconEntry::make('show_home_popup')->label('Popup')->boolean(),
                IconEntry::make('share_results')->label('Sonuç Paylaşımı')->boolean(),
                TextEntry::make('starts_at')->label('Başlangıç')->dateTime()->placeholder('-'),
                TextEntry::make('ends_at')->label('Bitiş')->dateTime()->placeholder('-'),
                TextEntry::make('options_sum_votes_count')
                    ->label('Toplam Oy')
                    ->state(fn ($record) => $record->options()->sum('votes_count')),
                TextEntry::make('participants_count')
                    ->label('Katılım')
                    ->state(fn ($record) => $record->votes()->distinct('voter_key')->count('voter_key')),
            ]);
    }
}
