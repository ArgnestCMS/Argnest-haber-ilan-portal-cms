<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Filament\Resources\ActivityLogs\Pages\ListActivityLogs;
use App\Filament\Resources\ActivityLogs\Pages\ViewActivityLog;
use App\Filament\Resources\ActivityLogs\Schemas\ActivityLogInfolist;
use App\Filament\Resources\ActivityLogs\Tables\ActivityLogsTable;
use App\Models\ActivityLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $navigationLabel = 'Aktivite Logları';

    protected static ?string $modelLabel = 'Aktivite Logu';

    protected static ?string $pluralModelLabel = 'Aktivite Logları';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Güvenlik';

    protected static ?string $recordTitleAttribute = 'action';

    public static function infolist(Schema $schema): Schema
    {
        return ActivityLogInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivityLogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
            'view' => ViewActivityLog::route('/{record}'),
        ];
    }

   public static function canViewAny(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('panel_giris');
}

public static function shouldRegisterNavigation(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('kullanici_yonet');
}
}