<?php

namespace App\Filament\Resources\MediaAssets\Pages;

use App\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Models\MediaAsset;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListMediaAssets extends ListRecords
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('storage_stats')
                ->label('Storage Ozeti')
                ->icon('heroicon-o-chart-bar')
                ->modalHeading('Medya Storage Ozeti')
                ->modalDescription(fn (): string => $this->storageSummary())
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Kapat'),
        ];
    }

    private function storageSummary(): string
    {
        $totalCount = MediaAsset::withTrashed()->count();
        $activeCount = MediaAsset::query()->count();
        $orphanCount = MediaAsset::query()->orphan()->count();
        $suspiciousCount = MediaAsset::query()->where('status', 'suspicious')->count();
        $largeCount = MediaAsset::query()
            ->where('size', '>=', (int) config('media.management.large_file_warning_mb', 20) * 1024 * 1024)
            ->count();
        $totalBytes = (int) MediaAsset::withTrashed()->sum('size');

        return implode("\n", [
            'Toplam kayit: ' . number_format($totalCount),
            'Aktif kayit: ' . number_format($activeCount),
            'Orphan medya: ' . number_format($orphanCount),
            'Supheli medya: ' . number_format($suspiciousCount),
            'Buyuk medya: ' . number_format($largeCount),
            'Toplam boyut: ' . $this->formatBytes($totalBytes),
        ]);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024 * 1024) {
            return round($bytes / 1024 / 1024 / 1024, 2) . ' GB';
        }

        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 2) . ' MB';
        }

        return round($bytes / 1024, 1) . ' KB';
    }
}
