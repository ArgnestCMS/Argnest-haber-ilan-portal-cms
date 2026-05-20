<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use App\Filament\Resources\Concerns\HandlesContentAttachments;
use App\Helpers\ActivityLogger;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    use HandlesContentAttachments;

    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [

            ViewAction::make(),

            DeleteAction::make()
                ->after(function () {

                    ActivityLogger::log(
                        action: 'delete_news',

                        description: auth()->user()->name . ' haberi sildi.',

                        properties: [
                            'news_id' => $this->record->id,
                            'title' => $this->record->title,
                            'slug' => $this->record->slug,
                        ]
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        $this->attachPendingContentUploads($this->record, 'news_attachment');

        ActivityLogger::log(
            action: 'edit_news',

            description: auth()->user()->name . ' haberi düzenledi.',

            properties: [
                'news_id' => $this->record->id,
                'title' => $this->record->title,
                'slug' => $this->record->slug,
                'status' => $this->record->status ?? null,
                'category_id' => $this->record->category_id ?? null,
            ]
        );
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->extractContentAttachments($data);
    }
}
