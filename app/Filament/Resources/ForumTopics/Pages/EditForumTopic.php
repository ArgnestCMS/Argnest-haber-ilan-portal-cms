<?php

namespace App\Filament\Resources\ForumTopics\Pages;

use App\Filament\Resources\ForumTopics\ForumTopicResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditForumTopic extends EditRecord
{
    protected static string $resource = ForumTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
