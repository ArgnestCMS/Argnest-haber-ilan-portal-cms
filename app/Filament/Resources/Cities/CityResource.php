<?php

namespace App\Filament\Resources\Cities;

use App\Filament\Resources\Cities\Pages\CreateCity;
use App\Filament\Resources\Cities\Pages\EditCity;
use App\Filament\Resources\Cities\Pages\ListCities;
use App\Filament\Resources\Cities\Pages\ViewCity;
use App\Filament\Resources\Cities\Schemas\CityForm;
use App\Filament\Resources\Cities\Schemas\CityInfolist;
use App\Filament\Resources\Cities\Tables\CitiesTable;
use App\Models\City;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Şehirler';

    protected static ?string $modelLabel = 'Şehir';

    protected static ?string $pluralModelLabel = 'Şehirler';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCities::route('/'),
            'create' => CreateCity::route('/create'),
            'view' => ViewCity::route('/{record}'),
            'edit' => EditCity::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }
}