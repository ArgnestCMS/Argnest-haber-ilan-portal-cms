<?php

namespace App\Filament\Resources\ThemeSettings;

use App\Filament\Resources\ThemeSettings\Pages\CreateThemeSetting;
use App\Filament\Resources\ThemeSettings\Pages\EditThemeSetting;
use App\Filament\Resources\ThemeSettings\Pages\ListThemeSettings;
use App\Filament\Resources\ThemeSettings\Pages\ViewThemeSetting;
use App\Filament\Resources\ThemeSettings\Schemas\ThemeSettingForm;
use App\Filament\Resources\ThemeSettings\Schemas\ThemeSettingInfolist;
use App\Filament\Resources\ThemeSettings\Tables\ThemeSettingsTable;
use App\Models\ThemeSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ThemeSettingResource extends Resource
{
    protected static ?string $model = ThemeSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $navigationLabel = 'Tema Ayarları';

    protected static ?string $modelLabel = 'Tema Ayarı';

    protected static ?string $pluralModelLabel = 'Tema Ayarları';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return ThemeSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ThemeSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ThemeSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThemeSettings::route('/'),
            'create' => CreateThemeSetting::route('/create'),
            'view' => ViewThemeSetting::route('/{record}'),
            'edit' => EditThemeSetting::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return (auth()->user()?->isAdmin() ?? false)
            && ! ThemeSetting::query()->exists();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
