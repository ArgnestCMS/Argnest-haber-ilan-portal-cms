<?php

namespace App\Filament\Resources\News\Schemas;

use Filament\Schemas\Components\View;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Haber Sekmeleri')
                    ->tabs([

                        Tabs\Tab::make('Genel')
                            ->schema([

                                TextInput::make('title')
                                    ->label('Başlık')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->required(),

                                TextInput::make('slug')
                                    ->label('URL (Slug)')
                                    ->required(),

                                TextInput::make('source')
                                    ->label('Kaynak'),

Select::make('category_id')
    ->label('Kategori')
    ->options(fn () => \App\Models\Category::where('type', 'news')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->pluck('name', 'id'))
    ->searchable()
    ->preload()
    ->required(),

                                DateTimePicker::make('publish_date')
                                    ->label('Yayın Tarihi')
                                    ->seconds(false),

                                DateTimePicker::make('end_date')
                                    ->label('Bitiş Tarihi')
                                    ->seconds(false)
                                    ->nullable(),

                                Select::make('news_type')
                                    ->label('Haber Türü')
                                    ->options([
                                        'normal' => 'Normal',
                                        'manset' => 'Manşet',
                                        'son_dakika' => 'Son Dakika',
                                    ])
                                    ->default('normal'),

                                Toggle::make('is_headline')
                                    ->label('Manşete Göster'),

                                Toggle::make('comments_enabled')
                                    ->label('Yorumlara Açık')
                                    ->default(true),
                            ]),

                        Tabs\Tab::make('Açıklama')
                            ->schema([

                                Textarea::make('summary')
                                    ->label('Kısa Açıklama')
                                    ->rows(6)
                                    ->columnSpanFull(),

                                RichEditor::make('content')
                                    ->label('Haber İçeriği')
                                    ->required()
                                    ->extraInputAttributes([
                                        'style' => 'min-height: 500px;',
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Detay')
                            ->schema([

                                TextInput::make('views')
                                    ->label('Görüntülenme')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Tabs\Tab::make('Sosyal')
                            ->schema([

                                Toggle::make('share_facebook')
                                    ->label('Facebook için yayınla'),

                                Toggle::make('share_twitter')
                                    ->label('Twitter/X için yayınla'),
                            ]),

                        Tabs\Tab::make('Resim')
                            ->schema([

                                View::make('filament.forms.components.news-image-preview'),

                                FileUpload::make('image')
                                    ->label('Haber Görseli')
                                    ->image()
                                    ->disk('public')
                                    ->directory('news')
                                    ->visibility('public')
                                    ->previewable(false)
                                    ->downloadable()
                                    ->openable(),

                            ]),

                        Tabs\Tab::make('Dökümanlar')
                            ->schema([

                                FileUpload::make('document')
                                    ->label('PDF / Döküman'),
                            ]),
                    ]),
            ]);
    }
}