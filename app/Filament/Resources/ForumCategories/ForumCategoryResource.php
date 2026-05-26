<?php

namespace App\Filament\Resources\ForumCategories;

use App\Filament\Resources\ForumCategories\Pages\CreateForumCategory;
use App\Filament\Resources\ForumCategories\Pages\EditForumCategory;
use App\Filament\Resources\ForumCategories\Pages\ListForumCategories;
use App\Filament\Resources\ForumCategories\Pages\ViewForumCategory;
use App\Filament\Resources\ForumCategories\Schemas\ForumCategoryForm;
use App\Filament\Resources\ForumCategories\Schemas\ForumCategoryInfolist;
use App\Filament\Resources\ForumCategories\Tables\ForumCategoriesTable;
use App\Models\ForumCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForumCategoryResource extends Resource
{
    protected static ?string $model = ForumCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Forum Kategorileri';

    protected static ?string $modelLabel = 'Forum Kategorisi';

    protected static ?string $pluralModelLabel = 'Forum Kategorileri';

    protected static string|\UnitEnum|null $navigationGroup = 'Forum Yönetimi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ForumCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ForumCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumCategoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForumCategories::route('/'),
            'create' => CreateForumCategory::route('/create'),
            'view' => ViewForumCategory::route('/{record}'),
            'edit' => EditForumCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_yonet')
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_yonet');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_yonet');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_yonet');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
