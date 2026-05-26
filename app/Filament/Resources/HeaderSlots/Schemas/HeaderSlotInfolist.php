<?php

namespace App\Filament\Resources\HeaderSlots\Schemas;

use App\Models\HeaderSlot;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HeaderSlotInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel')
                    ->schema([
                        TextEntry::make('title')->label('Başlık / ad'),
                        TextEntry::make('slot_type')
                            ->label('Mod')
                            ->formatStateUsing(fn ($state): string => match ($state) {
                                HeaderSlot::TYPE_BUTTON => 'Buton Modu',
                                HeaderSlot::TYPE_BANNER => 'Reklam / Banner Modu',
                                default => $state,
                            }),
                        IconEntry::make('is_active')->label('Aktif')->boolean(),
                        TextEntry::make('sort_order')->label('Sıralama'),
                        TextEntry::make('display_position')->label('Gösterim konumu'),
                        TextEntry::make('starts_at')->label('Başlangıç tarihi')->dateTime('d.m.Y H:i')->placeholder('-'),
                        TextEntry::make('ends_at')->label('Bitiş tarihi')->dateTime('d.m.Y H:i')->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Buton')
                    ->schema([
                        TextEntry::make('button_text')->label('Buton yazısı')->placeholder('-'),
                        TextEntry::make('button_url')->label('Link URL')->placeholder('-'),
                        TextEntry::make('button_target')->label('Link hedefi')->placeholder('-'),
                        TextEntry::make('button_size')->label('Buton boyutu')->placeholder('-'),
                        TextEntry::make('button_radius')->label('Border radius')->suffix('px')->placeholder('-'),
                        TextEntry::make('custom_css_class')->label('Özel CSS class')->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Banner')
                    ->schema([
                        ImageEntry::make('banner_image')->label('Banner görseli')->disk('public'),
                        TextEntry::make('banner_url')->label('Banner linki')->placeholder('-'),
                        TextEntry::make('banner_target')->label('Link hedefi')->placeholder('-'),
                        TextEntry::make('banner_width')->label('Genişlik')->suffix('px')->placeholder('-'),
                        TextEntry::make('banner_height')->label('Yükseklik')->suffix('px')->placeholder('-'),
                        TextEntry::make('banner_alt')->label('Alt text')->placeholder('-'),
                    ])
                    ->columns(2),
            ]);
    }
}
