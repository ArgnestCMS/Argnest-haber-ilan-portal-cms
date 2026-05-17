<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListAnnouncements extends ListRecords
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('yeni_ilan')
                ->label('Yeni İlan')
                ->extraAttributes([
                    'onclick' => "window.open('" . route('filament.admin.resources.announcements.create') . "', 'YeniIlan', 'width=1100,height=750,left=200,top=80,resizable=yes,scrollbars=yes'); return false;",
                ]),
        ];
    }
}