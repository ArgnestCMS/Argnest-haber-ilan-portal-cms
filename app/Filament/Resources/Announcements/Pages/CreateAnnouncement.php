<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function afterCreate(): void
    {
        ActivityLogger::log(
            action: 'create_announcement',

            description: auth()->user()->name . ' yeni ilan oluşturdu.',

            properties: [
                'announcement_id' => $this->record->id,
                'title' => $this->record->title,
                'slug' => $this->record->slug,
                'status' => $this->record->status ?? null,
                'category_id' => $this->record->category_id ?? null,
            ]
        );
    }
}