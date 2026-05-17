<?php

namespace App\Filament\Resources\Galleries\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Section::make('Galeri Bilgileri')

                    ->schema([

                        TextInput::make('title')
                            ->label('Galeri Başlığı')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),

                        FileUpload::make('cover_image')
                            ->label('Kapak Görseli')
                            ->image()
                            ->disk('public')
                            ->directory('gallery-covers'),

                        RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                Section::make('Galeri Resimleri')

                    ->schema([

                        Repeater::make('images')

                            ->relationship()

                            ->schema([

                                FileUpload::make('image')
                                    ->label('Resim')
                                    ->image()
                                    ->disk('public')
                                    ->directory('gallery-images')
                                    ->required(),

                                TextInput::make('title')
                                    ->label('Başlık'),

                                TextInput::make('sort_order')
                                    ->label('Sıralama')
                                    ->numeric()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),

                            ])

                            ->columns(2)

                            ->collapsible()

                            ->cloneable()

                            ->reorderable(),

                    ]),

                Section::make('Yayın Ayarları')

                    ->schema([

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Öne Çıkan')
                            ->default(false),

                        DateTimePicker::make('published_at')
                            ->label('Yayın Tarihi'),

                    ])

                    ->columns(3),

            ]);
    }
}