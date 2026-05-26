<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Helpers\ActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function afterCreate(): void
    {
        ActivityLogger::log(
            action: 'create_category',

            description: auth()->user()->name . ' yeni kategori oluşturdu.',

            properties: [
                'category_id' => $this->record->id,
                'name' => $this->record->name ?? null,
                'slug' => $this->record->slug ?? null,
            ]
        );
    }
}