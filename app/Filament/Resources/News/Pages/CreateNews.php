<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\Concerns\HandlesContentAttachments;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    use HandlesContentAttachments;

    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->extractContentAttachments($data);
    }

    protected function afterCreate(): void
    {
        $this->attachPendingContentUploads($this->record, 'news_attachment');

        ActivityLogger::log(
            action: 'create_news',

            description: auth()->user()->name . ' yeni haber oluşturdu.',

            properties: [
                'news_id' => $this->record->id,
                'title' => $this->record->title,
                'slug' => $this->record->slug,
                'status' => $this->record->status ?? null,
                'category_id' => $this->record->category_id ?? null,
            ]
        );
    }
}
