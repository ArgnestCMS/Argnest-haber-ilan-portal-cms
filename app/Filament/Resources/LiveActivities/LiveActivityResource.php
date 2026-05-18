<?php

namespace App\Filament\Resources\LiveActivities;

use App\Filament\Resources\LiveActivities\Pages\EditLiveActivity;
use App\Filament\Resources\LiveActivities\Pages\ListLiveActivities;
use App\Filament\Resources\LiveActivities\Pages\ViewLiveActivity;
use App\Filament\Resources\LiveActivities\Schemas\LiveActivityForm;
use App\Filament\Resources\LiveActivities\Schemas\LiveActivityInfolist;
use App\Filament\Resources\LiveActivities\Tables\LiveActivitiesTable;
use App\Models\LiveActivity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveActivityResource extends Resource
{
    protected static ?string $model = LiveActivity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Canlı Aktivite Akışı';

    protected static ?string $modelLabel = 'Canlı Aktivite';

    protected static ?string $pluralModelLabel = 'Canlı Aktiviteler';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Güvenlik';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return LiveActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LiveActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveActivitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLiveActivities::route('/'),
            'view' => ViewLiveActivity::route('/{record}'),
            'edit' => EditLiveActivity::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canRestore($record): bool
    {
        return static::canViewAny();
    }

    public static function canRestoreAny(): bool
    {
        return static::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
