<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SiteSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('site_name')
                    ->placeholder('-'),
                TextEntry::make('site_slogan')
                    ->placeholder('-'),
                TextEntry::make('logo')
                    ->placeholder('-'),
                TextEntry::make('favicon')
                    ->placeholder('-'),
                TextEntry::make('seo_title')
                    ->placeholder('-'),
                TextEntry::make('seo_description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('seo_keywords')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('email')
                    ->label('E-Posta')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-'),
                TextEntry::make('facebook')
                    ->placeholder('-'),
                TextEntry::make('twitter')
                    ->placeholder('-'),
                TextEntry::make('instagram')
                    ->placeholder('-'),
                TextEntry::make('youtube')
                    ->placeholder('-'),
                TextEntry::make('telegram')
                    ->placeholder('-'),
                TextEntry::make('header_scripts')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('footer_scripts')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('google_analytics')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('adsense_code')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('footer_about')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('footer_copyright')
                    ->placeholder('-')
                    ->columnSpanFull(),
                IconEntry::make('maintenance_mode')
                    ->boolean(),
                TextEntry::make('maintenance_message')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('maintenance_ends_at')
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
