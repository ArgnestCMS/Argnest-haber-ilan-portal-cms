<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Filament\Resources\Concerns\HandlesContentAttachments;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateAnnouncement extends CreateRecord
{
    use HandlesContentAttachments;

    protected static string $resource = AnnouncementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractContentAttachments($data);
    }

    protected function afterCreate(): void
    {
        $this->attachPendingContentUploads($this->record, 'announcement_attachment');

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
