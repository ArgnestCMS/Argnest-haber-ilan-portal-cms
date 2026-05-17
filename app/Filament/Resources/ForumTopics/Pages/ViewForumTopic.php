<?php

namespace App\Filament\Resources\ForumTopics\Pages;

use App\Filament\Resources\ForumTopics\ForumTopicResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewForumTopic extends ViewRecord
{
    protected static string $resource = ForumTopicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
