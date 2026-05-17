<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {

                    ActivityLogger::log(
                        action: 'delete_category',

                        description: auth()->user()->name . ' kategoriyi sildi.',

                        properties: [
                            'category_id' => $this->record->id,
                            'name' => $this->record->name ?? null,
                            'slug' => $this->record->slug ?? null,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLogger::log(
            action: 'edit_category',

            description: auth()->user()->name . ' kategoriyi düzenledi.',

            properties: [
                'category_id' => $this->record->id,
                'name' => $this->record->name ?? null,
                'slug' => $this->record->slug ?? null,
            ]
        );
    }
}