<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Kullanıcılar';

    protected static ?string $modelLabel = 'Kullanıcı';

    protected static ?string $pluralModelLabel = 'Kullanıcılar';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yönetimi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('kullanici_yonet');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('kullanici_yonet');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('kullanici_yonet');
    }

    public static function canDelete($record): bool
    {
        return (auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('kullanici_yonet'))
            && $record instanceof User
            && static::canSafelyDelete($record);
    }

    public static function canSafelyDelete(User $record): bool
    {
        if ($record->trashed()) {
            return false;
        }

        if (auth()->id() === $record->id) {
            return false;
        }

        if (! static::isAdminRecord($record)) {
            return true;
        }

        return static::adminCount() > 1;
    }

    public static function deleteBlockReason(User $record): string
    {
        if ($record->trashed()) {
            return 'Kullanıcı zaten silinmiş.';
        }

        if (auth()->id() === $record->id) {
            return 'Kendi hesabınızı silemezsiniz.';
        }

        if (static::isAdminRecord($record) && static::adminCount() <= 1) {
            return 'Son kalan admin kullanıcı silinemez.';
        }

        return 'Bu kullanıcı silinemiyor.';
    }

    public static function isAdminRecord(User $record): bool
    {
        return $record->role === 'admin'
            || $record->roleModel?->slug === 'admin';
    }

    public static function adminCount(): int
    {
        return User::query()
            ->where(function (Builder $query): void {
                $query
                    ->where('role', 'admin')
                    ->orWhereHas('roleModel', fn (Builder $roleQuery) => $roleQuery->where('slug', 'admin'));
            })
            ->count();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('kullanici_yonet');
    }
}
