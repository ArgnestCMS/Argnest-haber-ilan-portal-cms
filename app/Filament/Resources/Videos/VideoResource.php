<?php

namespace App\Filament\Resources\Videos;

use App\Filament\Resources\Videos\Pages\CreateVideo;
use App\Filament\Resources\Videos\Pages\EditVideo;
use App\Filament\Resources\Videos\Pages\ListVideos;
use App\Filament\Resources\Videos\Pages\ViewVideo;
use App\Filament\Resources\Videos\Schemas\VideoForm;
use App\Filament\Resources\Videos\Schemas\VideoInfolist;
use App\Filament\Resources\Videos\Tables\VideosTable;
use App\Models\Video;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationLabel = 'Videolar';

    protected static ?string $modelLabel = 'Video';

    protected static ?string $pluralModelLabel = 'Videolar';

    protected static string|\UnitEnum|null $navigationGroup = 'Medya Merkezi';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;

    public static function form(Schema $schema): Schema
    {
        return VideoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VideoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VideosTable::configure($table);
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
            'index' => ListVideos::route('/'),
            'create' => CreateVideo::route('/create'),
            'view' => ViewVideo::route('/{record}'),
            'edit' => EditVideo::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('video_ekle')
            || auth()->user()?->hasPermission('video_duzenle');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('video_ekle');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('video_duzenle');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('video_ekle')
            || auth()->user()?->hasPermission('video_duzenle');
    }
}