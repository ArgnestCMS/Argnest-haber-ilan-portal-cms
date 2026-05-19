<?php

namespace App\Filament\Resources\CommunityReports;

use App\Filament\Resources\CommunityReports\Pages\EditCommunityReport;
use App\Filament\Resources\CommunityReports\Pages\ListCommunityReports;
use App\Filament\Resources\CommunityReports\Pages\ViewCommunityReport;
use App\Filament\Resources\CommunityReports\Schemas\CommunityReportForm;
use App\Filament\Resources\CommunityReports\Schemas\CommunityReportInfolist;
use App\Filament\Resources\CommunityReports\Tables\CommunityReportsTable;
use App\Models\CommunityReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CommunityReportResource extends Resource
{
    protected static ?string $model = CommunityReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Topluluk Raporlari';

    protected static ?string $modelLabel = 'Topluluk Raporu';

    protected static ?string $pluralModelLabel = 'Topluluk Raporlari';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Guvenlik';

    protected static ?string $recordTitleAttribute = 'reason';

    public static function form(Schema $schema): Schema
    {
        return CommunityReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CommunityReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CommunityReportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommunityReports::route('/'),
            'view' => ViewCommunityReport::route('/{record}'),
            'edit' => EditCommunityReport::route('/{record}/edit'),
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

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
