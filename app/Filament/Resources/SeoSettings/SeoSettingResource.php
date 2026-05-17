<?php

namespace App\Filament\Resources\SeoSettings;

use App\Filament\Resources\SeoSettings\Pages\CreateSeoSetting;
use App\Filament\Resources\SeoSettings\Pages\EditSeoSetting;
use App\Filament\Resources\SeoSettings\Pages\ListSeoSettings;
use App\Models\SeoSetting;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoSettingResource extends Resource
{
    protected static ?string $model = SeoSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMagnifyingGlass;

    protected static string|UnitEnum|null $navigationGroup = 'Site Yönetimi';

    protected static ?string $navigationLabel = 'SEO Yönetimi';

    protected static ?string $modelLabel = 'SEO Ayarı';

    protected static ?string $pluralModelLabel = 'SEO Ayarları';

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermission('seo_yonet') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermission('seo_yonet') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasPermission('seo_yonet') ?? false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Genel SEO')
                    ->description('Sitenin temel arama motoru bilgileri.')
                    ->schema([
                        TextInput::make('site_title')
                            ->label('Site Başlığı')
                            ->maxLength(255),

                        Textarea::make('site_description')
                            ->label('Site Açıklaması')
                            ->rows(3),

                        TextInput::make('site_keywords')
                            ->label('Anahtar Kelimeler')
                            ->maxLength(255),
                    ])
                    ->columns(1),

                Section::make('Open Graph')
                    ->description('Facebook, WhatsApp ve sosyal medya paylaşım bilgileri.')
                    ->schema([
                        TextInput::make('og_title')
                            ->label('OG Başlık')
                            ->maxLength(255),

                        Textarea::make('og_description')
                            ->label('OG Açıklama')
                            ->rows(3),

                        FileUpload::make('og_image')
                            ->label('OG Görsel')
                            ->image()
                            ->directory('seo'),
                    ])
                    ->columns(1),

                Section::make('Twitter Cards')
                    ->schema([
                        TextInput::make('twitter_title')
                            ->label('Twitter Başlık')
                            ->maxLength(255),

                        Textarea::make('twitter_description')
                            ->label('Twitter Açıklama')
                            ->rows(3),

                        FileUpload::make('twitter_image')
                            ->label('Twitter Görsel')
                            ->image()
                            ->directory('seo'),
                    ])
                    ->columns(1),

                Section::make('Robots & Canonical')
                    ->schema([
                        TextInput::make('canonical_url')
                            ->label('Canonical URL')
                            ->url(),

                        Toggle::make('indexing')
                            ->label('Site indekslensin')
                            ->default(true),

                        Toggle::make('robots_index')
                            ->label('Robots index')
                            ->default(true),

                        Toggle::make('robots_follow')
                            ->label('Robots follow')
                            ->default(true),
                    ])
                    ->columns(2),

                Section::make('Analytics Kodları')
                    ->schema([
                        Textarea::make('google_analytics')
                            ->label('Google Analytics Kodu')
                            ->rows(5),

                        Textarea::make('google_tag_manager')
                            ->label('Google Tag Manager Kodu')
                            ->rows(5),
                    ]),

                Section::make('JSON-LD Structured Data')
                    ->schema([
                        Textarea::make('json_ld')
                            ->label('JSON-LD')
                            ->rows(8),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('site_title')
                    ->label('Site Başlığı')
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeoSettings::route('/'),
            'create' => CreateSeoSetting::route('/create'),
            'edit' => EditSeoSetting::route('/{record}/edit'),
        ];
    }
}