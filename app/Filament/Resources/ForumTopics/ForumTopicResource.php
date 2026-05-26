<?php

namespace App\Filament\Resources\ForumTopics;

use App\Filament\Resources\ForumTopics\Pages\CreateForumTopic;
use App\Filament\Resources\ForumTopics\Pages\EditForumTopic;
use App\Filament\Resources\ForumTopics\Pages\ListForumTopics;
use App\Filament\Resources\ForumTopics\Pages\ViewForumTopic;
use App\Filament\Resources\ForumTopics\Schemas\ForumTopicForm;
use App\Filament\Resources\ForumTopics\Schemas\ForumTopicInfolist;
use App\Filament\Resources\ForumTopics\Tables\ForumTopicsTable;
use App\Models\ForumTopic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ForumTopicResource extends Resource
{
    protected static ?string $model = ForumTopic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Forum Konuları';

    protected static ?string $modelLabel = 'Forum Konusu';

    protected static ?string $pluralModelLabel = 'Forum Konuları';

    protected static string|\UnitEnum|null $navigationGroup = 'Forum Yönetimi';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ForumTopicForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ForumTopicInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ForumTopicsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListForumTopics::route('/'),
            'create' => CreateForumTopic::route('/create'),
            'view' => ViewForumTopic::route('/{record}'),
            'edit' => EditForumTopic::route('/{record}/edit'),
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
            || auth()->user()?->hasPermission('forum_yonet')
            || auth()->user()?->hasPermission('forum_moderasyonu');
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
