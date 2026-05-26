<?php

namespace App\Filament\Resources\SiteAnnouncements\Pages;

use App\Filament\Resources\SiteAnnouncements\SiteAnnouncementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSiteAnnouncement extends ViewRecord
{
    protected static string $resource = SiteAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
