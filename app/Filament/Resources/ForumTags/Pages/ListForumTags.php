<?php

namespace App\Filament\Resources\ForumTags\Pages;

use App\Filament\Resources\ForumTags\ForumTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForumTags extends ListRecords
{
    protected static string $resource = ForumTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Yeni Forum Etiketi'),
        ];
    }
}
