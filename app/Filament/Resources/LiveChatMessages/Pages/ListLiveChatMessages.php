<?php

namespace App\Filament\Resources\LiveChatMessages\Pages;

use App\Filament\Resources\LiveChatMessages\LiveChatMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLiveChatMessages extends ListRecords
{
    protected static string $resource = LiveChatMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni Mesaj'),
        ];
    }
}
