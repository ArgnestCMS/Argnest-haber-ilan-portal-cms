<?php

namespace App\Filament\Resources\ForumPosts\Pages;

use App\Filament\Resources\ForumPosts\ForumPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateForumPost extends CreateRecord
{
    protected static string $resource = ForumPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = $data['user_id'] ?? auth()->id();

        if (($data['status'] ?? null) === 'approved') {
            $data['moderated_by'] = auth()->id();
            $data['moderated_at'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->status !== 'approved') {
            return;
        }

        $this->record->topic?->update([
            'last_post_at' => $this->record->created_at,
            'last_post_user_id' => $this->record->user_id,
        ]);
    }
}
