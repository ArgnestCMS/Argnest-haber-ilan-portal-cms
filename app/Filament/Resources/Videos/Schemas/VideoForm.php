<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Models\Category;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Video Bilgileri')

                    ->schema([

                        TextInput::make('title')
                            ->label('Video Başlığı')
                            ->required()
                            ->maxLength(255)
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

                        Select::make('video_type')
                            ->label('Video Türü')
                            ->options([
                                'youtube' => 'YouTube',
                                'upload' => 'Video Upload',
                            ])
                            ->default('youtube')
                            ->required()
                            ->live(),

                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->visible(fn ($get) =>
                                $get('video_type') === 'youtube'
                            ),

                        FileUpload::make('video_path')
                            ->label('Video Dosyası')
                            ->disk('public')
                            ->directory('videos')
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/webm',
                            ])
                            ->visible(fn ($get) =>
                                $get('video_type') === 'upload'
                            ),

                        FileUpload::make('thumbnail')
                            ->label('Kapak Görseli')
                            ->image()
                            ->disk('public')
                            ->directory('video-thumbnails'),

                        RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                Section::make('Yayın Ayarları')

                    ->schema([

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Öne Çıkan')
                            ->default(false),

                        Placeholder::make('views')
                            ->label('İzlenme')
                            ->content(fn ($record) =>
                                $record?->views ?? 0
                            ),

                    ])

                    ->columns(3),

            ]);
    }
}