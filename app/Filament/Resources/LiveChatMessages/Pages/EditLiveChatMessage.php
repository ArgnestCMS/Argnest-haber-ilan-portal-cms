<?php

namespace App\Filament\Resources\LiveChatMessages\Pages;

use App\Filament\Resources\LiveChatMessages\LiveChatMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditLiveChatMessage extends EditRecord
{
    protected static string $resource = LiveChatMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (($data['status'] ?? null) !== $this->record->status) {
            $data['moderated_by'] = auth()->id();
            $data['moderated_at'] = now();
        }

        return $data;
    }
}
