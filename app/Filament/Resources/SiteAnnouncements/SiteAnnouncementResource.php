<?php

namespace App\Filament\Resources\SiteAnnouncements;

use App\Filament\Resources\SiteAnnouncements\Pages\CreateSiteAnnouncement;
use App\Filament\Resources\SiteAnnouncements\Pages\EditSiteAnnouncement;
use App\Filament\Resources\SiteAnnouncements\Pages\ListSiteAnnouncements;
use App\Filament\Resources\SiteAnnouncements\Pages\ViewSiteAnnouncement;
use App\Filament\Resources\SiteAnnouncements\Schemas\SiteAnnouncementForm;
use App\Filament\Resources\SiteAnnouncements\Schemas\SiteAnnouncementInfolist;
use App\Filament\Resources\SiteAnnouncements\Tables\SiteAnnouncementsTable;
use App\Models\SiteAnnouncement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SiteAnnouncementResource extends Resource
{
    protected static ?string $model = SiteAnnouncement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static ?string $navigationLabel = 'Site Duyuruları';

    protected static ?string $modelLabel = 'Site Duyurusu';

    protected static ?string $pluralModelLabel = 'Site Duyuruları';

    protected static string|\UnitEnum|null $navigationGroup = 'Portal Yönetimi';

    protected static ?string $recordTitleAttribute = 'text';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return SiteAnnouncementForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SiteAnnouncementInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteAnnouncementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteAnnouncements::route('/'),
            'create' => CreateSiteAnnouncement::route('/create'),
            'view' => ViewSiteAnnouncement::route('/{record}'),
            'edit' => EditSiteAnnouncement::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin()
            || auth()->user()?->hasPermission('site_ayarlarini_yonet');
    }
}
