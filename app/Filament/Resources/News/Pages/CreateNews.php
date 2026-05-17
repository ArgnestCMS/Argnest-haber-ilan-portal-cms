<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected function afterCreate(): void
    {
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