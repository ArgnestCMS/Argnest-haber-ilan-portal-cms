<?php

namespace App\Filament\Resources\News\Pages;

use App\Filament\Resources\News\NewsResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('yeni_haber')
                ->label('Yeni Haber')
                ->extraAttributes([
                    'onclick' => "window.open('" . route('filament.admin.resources.news.create') . "', 'YeniHaber', 'width=1100,height=750,left=200,top=80,resizable=yes,scrollbars=yes'); return false;",
                ]),
        ];
    }
}