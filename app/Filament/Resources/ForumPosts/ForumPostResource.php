<?php

namespace App\Filament\Resources\ForumPosts;

use App\Filament\Resources\ForumPosts\Pages\CreateForumPost;
use App\Filament\Resources\ForumPosts\Pages\EditForumPost;
use App\Filament\Resources\ForumPosts\Pages\ListForumPosts;
use App\Filament\Resources\ForumPosts\Pages\ViewForumPost;
use App\Filament\Resources\ForumPosts\Schemas\ForumPostForm;
use App\Filament\Resources\ForumPosts\Schemas\ForumPostInfolist;
use App\Filament\Resources\ForumPosts\Tables\ForumPostsTable;
use App\Models\ForumPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForumPostResource extends Resource
{
    protected static ?string $model = ForumPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $navigationLabel = 'Forum Cevapları';

    protected static ?string $modelLabel = 'Forum Cevabı';

    protected static ?string $pluralModelLabel = 'Forum Cevapları';

    protected static string|\UnitEnum|null $navigationGroup = 'Forum Yönetimi';

    protected static ?string $recordTitleAttribute = 'content';

    public static function form(Schema $schema): Schema
    {
        return ForumPostForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ForumPostInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumPostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForumPosts::route('/'),
            'create' => CreateForumPost::route('/create'),
            'view' => ViewForumPost::route('/{record}'),
            'edit' => EditForumPost::route('/{record}/edit'),
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
