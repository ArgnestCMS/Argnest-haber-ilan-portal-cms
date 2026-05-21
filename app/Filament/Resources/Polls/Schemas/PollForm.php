<?php

namespace App\Filament\Resources\Polls\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Anket Yönetimi')
                    ->tabs([
                        Tabs\Tab::make('İçerik')
                            ->schema([
                                Section::make('Temel Bilgiler')
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Başlık')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                                        TextInput::make('slug')
                                            ->label('URL (Slug)')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true),

                                        TextInput::make('subtitle')
                                            ->label('Alt Başlık')
                                            ->maxLength(255),

                                        TextInput::make('topic')
                                            ->label('Konu / Kategori')
                                            ->maxLength(255),

                                        FileUpload::make('image')
                                            ->label('Görsel')
                                            ->image()
                                            ->disk('public')
                                            ->directory('polls')
                                            ->columnSpanFull(),

                                        RichEditor::make('description')
                                            ->label('Açıklama')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tabs\Tab::make('Seçenekler')
                            ->schema([
                                Repeater::make('options')
                                    ->label('Anket Seçenekleri')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('title')
                                            ->label('Seçenek Başlığı')
                                            ->required()
                                            ->maxLength(255),

                                        TextInput::make('sort_order')
                                            ->label('Sıra')
                                            ->numeric()
                                            ->default(0),

                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),

                                        FileUpload::make('image')
                                            ->label('Görsel')
                                            ->image()
                                            ->disk('public')
                                            ->directory('poll-options'),

                                        Textarea::make('description')
                                            ->label('Seçenek Açıklaması')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(2)
                                    ->reorderable()
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Yayın ve Oy')
                            ->schema([
                                Section::make('Yayın Ayarları')
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),

                                        DateTimePicker::make('starts_at')
                                            ->label('Başlangıç Tarihi')
                                            ->seconds(false),

                                        DateTimePicker::make('ends_at')
                                            ->label('Bitiş Tarihi')
                                            ->seconds(false),

                                        Toggle::make('share_results')
                                            ->label('Sonuç Paylaşımı Aktif')
                                            ->default(true),

                                        Toggle::make('show_home_popup')
                                            ->label('Ana Sayfa Popup Aktif')
                                            ->default(false),

                                        TextInput::make('popup_cooldown_minutes')
                                            ->label('Popup Kapatma Süresi (dk)')
                                            ->numeric()
                                            ->default(1440)
                                            ->required(),
                                    ])
                                    ->columns(3),

                                Section::make('Oy Ayarları')
                                    ->schema([
                                        Toggle::make('allow_multiple')
                                            ->label('Çoklu Seçenek')
                                            ->default(false),

                                        Toggle::make('allow_guests')
                                            ->label('Misafir Oy Kullanabilir')
                                            ->default(true),

                                        Toggle::make('require_login')
                                            ->label('Üyelik Zorunlu')
                                            ->default(false),

                                        Select::make('duplicate_guard')
                                            ->label('Tekrar Oy Engeli')
                                            ->options([
                                                'user_session_ip' => 'Üye / Session / IP',
                                                'user' => 'Üye bazlı',
                                                'session' => 'Session bazlı',
                                                'ip' => 'IP bazlı',
                                            ])
                                            ->default('user_session_ip')
                                            ->required(),
                                    ])
                                    ->columns(4),
                            ]),
                    ]),
            ]);
    }
}
