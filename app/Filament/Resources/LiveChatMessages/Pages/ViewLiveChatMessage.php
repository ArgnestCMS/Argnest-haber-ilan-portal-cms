<?php

namespace App\Filament\Resources\LiveChatMessages\Pages;

use App\Filament\Resources\LiveChatMessages\LiveChatMessageResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLiveChatMessage extends ViewRecord
{
    protected static string $resource = LiveChatMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
