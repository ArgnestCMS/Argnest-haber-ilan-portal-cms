<?php

namespace App\Filament\Resources\LiveChatMessages;

use App\Filament\Resources\LiveChatMessages\Pages\CreateLiveChatMessage;
use App\Filament\Resources\LiveChatMessages\Pages\EditLiveChatMessage;
use App\Filament\Resources\LiveChatMessages\Pages\ListLiveChatMessages;
use App\Filament\Resources\LiveChatMessages\Pages\ViewLiveChatMessage;
use App\Filament\Resources\LiveChatMessages\Schemas\LiveChatMessageForm;
use App\Filament\Resources\LiveChatMessages\Schemas\LiveChatMessageInfolist;
use App\Filament\Resources\LiveChatMessages\Tables\LiveChatMessagesTable;
use App\Models\LiveChatMessage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LiveChatMessageResource extends Resource
{
    protected static ?string $model = LiveChatMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static ?string $navigationLabel = 'Canlı Sohbet Moderasyonu';

    protected static ?string $modelLabel = 'Canlı Sohbet Mesajı';

    protected static ?string $pluralModelLabel = 'Canlı Sohbet Mesajları';

    protected static string|\UnitEnum|null $navigationGroup = 'Moderasyon & Güvenlik';

    protected static ?string $recordTitleAttribute = 'message';

    public static function form(Schema $schema): Schema
    {
        return LiveChatMessageForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LiveChatMessageInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LiveChatMessagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLiveChatMessages::route('/'),
            'create' => CreateLiveChatMessage::route('/create'),
            'view' => ViewLiveChatMessage::route('/{record}'),
            'edit' => EditLiveChatMessage::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('forum_moderasyonu');
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
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
