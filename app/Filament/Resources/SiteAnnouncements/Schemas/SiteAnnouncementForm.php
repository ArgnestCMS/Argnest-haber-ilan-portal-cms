<?php

namespace App\Filament\Resources\SiteAnnouncements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SiteAnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('text')
                    ->label('Duyuru Metni')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('icon')
                    ->label('İkon / Emoji')
                    ->placeholder('📢')
                    ->maxLength(20),

                TextInput::make('link_url')
                    ->label('Link URL')
                    ->url()
                    ->maxLength(255),

                Select::make('link_target')
                    ->label('Link Hedefi')
                    ->options([
                        '_self' => 'Aynı sekme',
                        '_blank' => 'Yeni sekme',
                    ])
                    ->default('_self')
                    ->required(),

                TextInput::make('sort_order')
                    ->label('Sıra')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                DateTimePicker::make('starts_at')
                    ->label('Yayın Başlangıcı')
                    ->seconds(false),

                DateTimePicker::make('ends_at')
                    ->label('Yayın Bitişi')
                    ->seconds(false),
            ])
            ->columns(2);
    }
}
