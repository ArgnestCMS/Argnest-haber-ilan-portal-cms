<?php

namespace App\Filament\Resources\Announcements\Pages;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {

                    ActivityLogger::log(
                        action: 'delete_announcement',

                        description: auth()->user()->name . ' ilanı sildi.',

                        properties: [
                            'announcement_id' => $this->record->id,
                            'title' => $this->record->title,
                            'slug' => $this->record->slug,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        ActivityLogger::log(
            action: 'edit_announcement',

            description: auth()->user()->name . ' ilanı düzenledi.',

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