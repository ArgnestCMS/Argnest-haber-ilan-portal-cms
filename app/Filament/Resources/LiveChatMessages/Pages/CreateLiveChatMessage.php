<?php

namespace App\Filament\Resources\LiveChatMessages\Pages;

use App\Filament\Resources\LiveChatMessages\LiveChatMessageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLiveChatMessage extends CreateRecord
{
    protected static string $resource = LiveChatMessageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (($data['status'] ?? null) === 'approved') {
            $data['moderated_by'] = auth()->id();
            $data['moderated_at'] = now();
        }

        return $data;
    }
}
