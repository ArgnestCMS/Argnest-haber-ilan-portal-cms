<?php

namespace App\Filament\Resources\ForumTopics\Pages;

use App\Filament\Resources\ForumTopics\ForumTopicResource;
use Filament\Resources\Pages\CreateRecord;

class CreateForumTopic extends CreateRecord
{
    protected static string $resource = ForumTopicResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        $data['last_post_at'] = now();

        return $data;
    }
}
