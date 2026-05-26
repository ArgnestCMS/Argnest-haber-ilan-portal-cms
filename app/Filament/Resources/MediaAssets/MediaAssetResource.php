<?php

namespace App\Filament\Resources\MediaAssets;

use App\Filament\Resources\MediaAssets\Pages\ListMediaAssets;
use App\Filament\Resources\MediaAssets\Pages\ViewMediaAsset;
use App\Filament\Resources\MediaAssets\Schemas\MediaAssetInfolist;
use App\Filament\Resources\MediaAssets\Tables\MediaAssetsTable;
use App\Models\MediaAsset;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MediaAssetResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Medya Yonetimi';

    protected static ?string $modelLabel = 'Medya';

    protected static ?string $pluralModelLabel = 'Medya Dosyalari';

    protected static string|\UnitEnum|null $navigationGroup = 'Forum Yonetimi';

    protected static ?string $recordTitleAttribute = 'original_name';

    public static function infolist(Schema $schema): Schema
    {
        return MediaAssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaAssetsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['owner', 'attachable']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMediaAssets::route('/'),
            'view' => ViewMediaAsset::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_yonet')
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_yonet')
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
