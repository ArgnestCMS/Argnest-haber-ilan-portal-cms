<?php

namespace App\Filament\Resources\SeoSettings\Pages;

use App\Filament\Resources\SeoSettings\SeoSettingResource;
use App\Models\SeoSetting;
use App\Services\SitemapService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSeoSettings extends ListRecords
{
    protected static string $resource = SeoSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearSitemapCache')
                ->label('Sitemap cache temizle')
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    app(SitemapService::class)->clearCache();

                    Notification::make()
                        ->title('Sitemap cache temizlendi.')
                        ->success()
                        ->send();
                }),
            CreateAction::make()
                ->label('SEO Ayarı Oluştur')
                ->visible(fn () => SeoSetting::query()->count() === 0),
        ];
    }
}
