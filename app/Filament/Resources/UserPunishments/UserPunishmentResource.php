<?php

namespace App\Filament\Resources\UserPunishments;

use App\Filament\Resources\UserPunishments\Pages\CreateUserPunishment;
use App\Filament\Resources\UserPunishments\Pages\EditUserPunishment;
use App\Filament\Resources\UserPunishments\Pages\ListUserPunishments;
use App\Filament\Resources\UserPunishments\Pages\ViewUserPunishment;
use App\Filament\Resources\UserPunishments\Schemas\UserPunishmentForm;
use App\Filament\Resources\UserPunishments\Schemas\UserPunishmentInfolist;
use App\Filament\Resources\UserPunishments\Tables\UserPunishmentsTable;
use App\Models\UserPunishment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserPunishmentResource extends Resource
{
    protected static ?string $model = UserPunishment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static ?string $navigationLabel = 'Ceza Yönetimi';

    protected static ?string $modelLabel = 'Ceza';

    protected static ?string $pluralModelLabel = 'Cezalar';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Güvenlik';

    protected static ?string $recordTitleAttribute = 'reason';

    public static function form(Schema $schema): Schema
    {
        return UserPunishmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserPunishmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserPunishmentsTable::configure($table);
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
            'index' => ListUserPunishments::route('/'),
            'create' => CreateUserPunishment::route('/create'),
            'view' => ViewUserPunishment::route('/{record}'),
            'edit' => EditUserPunishment::route('/{record}/edit'),
        ];
    }

  public static function canViewAny(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('yorum_moderasyonu');
}

public static function shouldRegisterNavigation(): bool
{
    return auth()->user()?->isAdmin()
        || auth()->user()?->hasPermission('yorum_moderasyonu');
}
}