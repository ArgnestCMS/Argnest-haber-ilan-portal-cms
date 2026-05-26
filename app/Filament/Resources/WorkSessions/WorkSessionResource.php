<?php

namespace App\Filament\Resources\WorkSessions;

use App\Filament\Resources\WorkSessions\Pages\ListWorkSessions;
use App\Filament\Resources\WorkSessions\Pages\ViewWorkSession;
use App\Filament\Resources\WorkSessions\Schemas\WorkSessionInfolist;
use App\Filament\Resources\WorkSessions\Tables\WorkSessionsTable;
use App\Models\WorkSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkSessionResource extends Resource
{
    protected static ?string $model = WorkSession::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Mesai / Mola Logları';

    protected static ?string $modelLabel = 'Mesai Kaydı';

    protected static ?string $pluralModelLabel = 'Mesai / Mola Logları';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Güvenlik';

    protected static ?string $recordTitleAttribute = 'type';

    public static function infolist(Schema $schema): Schema
    {
        return WorkSessionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkSessionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkSessions::route('/'),
            'view' => ViewWorkSession::route('/{record}'),
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