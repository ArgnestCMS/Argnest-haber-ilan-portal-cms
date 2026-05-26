<?php

namespace App\Filament\Resources\ThemeSettings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ThemeSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('primary_color')->label('Ana renk'),
                TextEntry::make('secondary_color')->label('İkincil renk'),
                TextEntry::make('topbar_color')->label('Üst bar rengi'),
                TextEntry::make('navbar_color')->label('Alt menü bar rengi'),
                TextEntry::make('breaking_bar_color')->label('Son dakika bar rengi'),
                TextEntry::make('announcement_bar_color')->label('Site duyuru bar rengi'),
                TextEntry::make('button_color')->label('Buton rengi'),
                TextEntry::make('button_hover_color')->label('Buton hover rengi'),
                TextEntry::make('link_color')->label('Link rengi'),
                TextEntry::make('heading_color')->label('Başlık rengi'),
                TextEntry::make('text_color')->label('Metin rengi'),
                TextEntry::make('card_background_color')->label('Kart arka plan rengi'),
                TextEntry::make('footer_color')->label('Footer rengi'),
            ]);
    }
}
