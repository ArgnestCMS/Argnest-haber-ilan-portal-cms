<?php

namespace App\Filament\Resources\ForumTags;

use App\Filament\Resources\ForumTags\Pages\CreateForumTag;
use App\Filament\Resources\ForumTags\Pages\EditForumTag;
use App\Filament\Resources\ForumTags\Pages\ListForumTags;
use App\Filament\Resources\ForumTags\Pages\ViewForumTag;
use App\Filament\Resources\ForumTags\Schemas\ForumTagForm;
use App\Filament\Resources\ForumTags\Schemas\ForumTagInfolist;
use App\Filament\Resources\ForumTags\Tables\ForumTagsTable;
use App\Models\ForumTag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForumTagResource extends Resource
{
    protected static ?string $model = ForumTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Forum Etiketleri';

    protected static ?string $modelLabel = 'Forum Etiketi';

    protected static ?string $pluralModelLabel = 'Forum Etiketleri';

    protected static string|\UnitEnum|null $navigationGroup = 'Forum YÃ¶netimi';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ForumTagForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ForumTagInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumTagsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForumTags::route('/'),
            'create' => CreateForumTag::route('/create'),
            'view' => ViewForumTag::route('/{record}'),
            'edit' => EditForumTag::route('/{record}/edit'),
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
            || auth()->user()?->hasPermission('forum_yonet')
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public static function canEdit($record): bool
    {
        return static::canCreate();
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
