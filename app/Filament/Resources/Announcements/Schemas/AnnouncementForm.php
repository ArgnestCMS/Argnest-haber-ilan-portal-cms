<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Models\Category;
use App\Models\City;
use App\Models\Institution;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Tabs::make('Sekmeler')
                    ->tabs([

                        Tabs\Tab::make('Genel')
                            ->schema([

                                TextInput::make('title')
                                    ->label('İlan Başlığı')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    })
                                    ->required(),

                                TextInput::make('slug')
                                    ->label('URL (Slug)')
                                    ->required(),

                                Select::make('institution')
                                    ->label('Kurum')
                                    ->options(fn () => Institution::where('is_active', true)->pluck('name', 'name'))
                                    ->searchable(),

                                Select::make('city')
                                    ->label('Şehir')
                                    ->options(fn () => City::where('is_active', true)->pluck('name', 'name'))
                                    ->searchable(),

Select::make('category_id')
    ->label('Yeni Kategori Sistemi')
    ->options(fn () => \App\Models\Category::where('type', 'announcement')
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->pluck('name', 'id'))
    ->searchable()
    ->preload(),

Select::make('category')
    ->label('Eski Kategori Adı')
    ->options(fn () => \App\Models\Category::where('type', 'announcement')
        ->where('is_active', true)
        ->pluck('name', 'name'))
    ->searchable(),

                                TextInput::make('source')
                                    ->label('Kaynak'),

                                DatePicker::make('publish_date')
                                    ->label('Yayın Tarihi'),

                                DatePicker::make('deadline')
                                    ->label('Son Başvuru Tarihi'),

                            ]),

                        Tabs\Tab::make('Açıklama')
                            ->schema([

                                Textarea::make('summary')
                                    ->label('Kısa Açıklama')
                                    ->rows(6)
                                    ->columnSpanFull(),

                                RichEditor::make('content')
                                    ->label('İlan İçeriği')
                                    ->required()
                                    ->extraInputAttributes([
                                        'style' => 'min-height: 500px;',
                                    ])
                                    ->columnSpanFull(),

                            ]),

                        Tabs\Tab::make('Resim')
                            ->schema([

                                FileUpload::make('image')
                                    ->label('İlan Görseli')
                                    ->image(),

                            ]),

                        Tabs\Tab::make('Dökümanlar')
                            ->schema([

                                FileUpload::make('document')
                                    ->label('PDF / Döküman'),

                            ]),

                        Tabs\Tab::make('Ayarlar')
                            ->schema([

                                Toggle::make('is_headline')
                                    ->label('Manşette Göster'),

                                Toggle::make('comments_enabled')
                                    ->label('Yorumlara Açık')
                                    ->default(true),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),

                            ]),

                    ])

            ]);
    }
}