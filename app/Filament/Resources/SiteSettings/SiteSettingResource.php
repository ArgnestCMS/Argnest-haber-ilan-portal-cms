<?php

namespace App\Filament\Resources\SiteSettings;

use App\Filament\Resources\SiteSettings\Pages\CreateSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Filament\Resources\SiteSettings\Pages\ListSiteSettings;
use App\Filament\Resources\SiteSettings\Pages\ViewSiteSetting;
use App\Filament\Resources\SiteSettings\Schemas\SiteSettingForm;
use App\Filament\Resources\SiteSettings\Schemas\SiteSettingInfolist;
use App\Filament\Resources\SiteSettings\Tables\SiteSettingsTable;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site Ayarları';

    protected static ?string $modelLabel = 'Site Ayarı';

    protected static ?string $pluralModelLabel = 'Site Ayarları';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?string $recordTitleAttribute = 'site_name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return SiteSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiteSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteSettingsTable::configure($table);
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
            'index' => ListSiteSettings::route('/'),
            'create' => CreateSiteSetting::route('/create'),
            'view' => ViewSiteSetting::route('/{record}'),
            'edit' => EditSiteSetting::route('/{record}/edit'),
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
        return auth()->user()?->isAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }
}