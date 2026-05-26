<?php

namespace App\Filament\Resources\ForumCategories\Pages;

use App\Filament\Resources\ForumCategories\ForumCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewForumCategory extends ViewRecord
{
    protected static string $resource = ForumCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
