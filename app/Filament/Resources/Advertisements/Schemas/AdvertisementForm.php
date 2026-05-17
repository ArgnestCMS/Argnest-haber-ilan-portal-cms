<?php

namespace App\Filament\Resources\Advertisements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AdvertisementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Reklam Yönetimi')
                    ->tabs([

                        Tabs\Tab::make('Genel')
                            ->schema([

                                TextInput::make('title')
                                    ->label('Reklam Başlığı')
                                    ->required(),

                                Select::make('position')
                                    ->label('Reklam Konumu')
                                    ->options([
                                        'top_banner' => 'Üst Banner',
                                        'bottom_banner' => 'Alt Banner',
                                        'left_sidebar' => 'Sol Sidebar',
                                        'right_sidebar' => 'Sağ Sidebar',
                                    ])
                                    ->required(),

                                Select::make('ad_type')
                                    ->label('Reklam Türü')
                                    ->options([
                                        'image' => 'Görsel Reklam',
                                        'html' => 'HTML Kod',
                                        'adsense' => 'Adsense',
                                    ])
                                    ->default('image')
                                    ->required(),

                                Select::make('device_target')
                                    ->label('Cihaz Hedefi')
                                    ->options([
                                        'all' => 'Tüm Cihazlar',
                                        'desktop' => 'Masaüstü',
                                        'mobile' => 'Mobil',
                                    ])
                                    ->default('all')
                                    ->required(),

                                Select::make('page_target')
                                    ->label('Sayfa Hedefi')
                                    ->options([
                                        'all' => 'Tüm Sayfalar',
                                        'home' => 'Anasayfa',
                                        'news' => 'Haber Sayfaları',
                                        'announcement' => 'İlan Sayfaları',
                                        'list' => 'Liste Sayfaları',
                                    ])
                                    ->default('all')
                                    ->required(),

                            ]),

                        Tabs\Tab::make('Görsel / Kod')
                            ->schema([

                                Placeholder::make('preview')
                                    ->label('Reklam Önizleme')
                                    ->content(function ($record) {

                                        if (! $record?->image) {
                                            return new HtmlString('');
                                        }

                                        $image = str_contains($record->image, '/')
                                            ? $record->image
                                            : 'ads/' . $record->image;

                                        return new HtmlString('
                                            <img
                                                src="' . asset('storage/' . $image) . '"
                                                style="
                                                    width: 320px;
                                                    border-radius: 12px;
                                                    border: 1px solid #333;
                                                    margin-bottom: 15px;
                                                "
                                            >
                                        ');
                                    }),

                                FileUpload::make('image')
                                    ->label('Reklam Görseli')
                                    ->image()
                                    ->disk('public')
                                    ->directory('ads')
                                    ->visibility('public')
                                    ->previewable(false)
                                    ->downloadable()
                                    ->openable()
                                    ->nullable()
                                    ->deletable(),

                                Textarea::make('html_code')
                                    ->label('HTML / Adsense Kodu')
                                    ->rows(8)
                                    ->columnSpanFull(),

                                TextInput::make('url')
                                    ->label('Reklam Linki')
                                    ->url(),

                            ]),

                        Tabs\Tab::make('Yayın Ayarları')
                            ->schema([

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),

                                DateTimePicker::make('start_date')
                                    ->label('Başlangıç Tarihi'),

                                DateTimePicker::make('end_date')
                                    ->label('Bitiş Tarihi'),

                            ]),

                        Tabs\Tab::make('İstatistik')
                            ->schema([

                                TextInput::make('views')
                                    ->label('Gösterim')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),

                                TextInput::make('clicks')
                                    ->label('Tıklama')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),

                                TextInput::make('ctr')
                                    ->label('CTR (%)')
                                    ->numeric()
                                    ->default(0)
                                    ->disabled(),

                            ]),

                    ])

            ]);
    }
}