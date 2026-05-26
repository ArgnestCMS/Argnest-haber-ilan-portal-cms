<?php

namespace App\Filament\Resources\HeaderSlots;

use App\Filament\Resources\HeaderSlots\Pages\CreateHeaderSlot;
use App\Filament\Resources\HeaderSlots\Pages\EditHeaderSlot;
use App\Filament\Resources\HeaderSlots\Pages\ListHeaderSlots;
use App\Filament\Resources\HeaderSlots\Pages\ViewHeaderSlot;
use App\Filament\Resources\HeaderSlots\Schemas\HeaderSlotForm;
use App\Filament\Resources\HeaderSlots\Schemas\HeaderSlotInfolist;
use App\Filament\Resources\HeaderSlots\Tables\HeaderSlotsTable;
use App\Models\HeaderSlot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HeaderSlotResource extends Resource
{
    protected static ?string $model = HeaderSlot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Header Slot Yönetimi';

    protected static ?string $modelLabel = 'Header Slot';

    protected static ?string $pluralModelLabel = 'Header Slot Yönetimi';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return HeaderSlotForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HeaderSlotInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HeaderSlotsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHeaderSlots::route('/'),
            'create' => CreateHeaderSlot::route('/create'),
            'view' => ViewHeaderSlot::route('/{record}'),
            'edit' => EditHeaderSlot::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }
}
