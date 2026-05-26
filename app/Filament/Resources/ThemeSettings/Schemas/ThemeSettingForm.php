<?php

namespace App\Filament\Resources\ThemeSettings\Schemas;

use App\Models\ThemeSetting;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ThemeSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tema Renkleri')
                    ->description('Frontend ana renk noktalarını yönetir. Boş alanlarda varsayılan renkler kullanılır.')
                    ->schema([
                        self::color('primary_color', 'Ana renk'),
                        self::color('secondary_color', 'İkincil renk'),
                        self::color('topbar_color', 'Üst bar rengi'),
                        self::color('navbar_color', 'Alt menü bar rengi'),
                        self::color('breaking_bar_color', 'Son dakika bar rengi'),
                        self::color('announcement_bar_color', 'Site duyuru bar rengi'),
                        self::color('button_color', 'Buton rengi'),
                        self::color('button_hover_color', 'Buton hover rengi'),
                        self::color('link_color', 'Link rengi'),
                        self::color('heading_color', 'Başlık rengi'),
                        self::color('text_color', 'Metin rengi'),
                        self::color('card_background_color', 'Kart arka plan rengi'),
                        self::color('footer_color', 'Footer rengi'),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                        'xl' => 3,
                    ]),
            ]);
    }

    private static function color(string $name, string $label): ColorPicker
    {
        return ColorPicker::make($name)
            ->label($label)
            ->default(ThemeSetting::DEFAULTS[$name])
            ->placeholder(ThemeSetting::DEFAULTS[$name]);
    }
}
