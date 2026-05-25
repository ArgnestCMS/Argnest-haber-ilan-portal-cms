<?php

namespace App\Filament\Pages;

use App\Services\SiteReportService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class SiteReports extends Page
{
    protected string $view = 'filament.pages.site-reports';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Site Raporlari';

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem Yonetimi';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'site-reports';

    protected static ?string $title = 'Site Raporlari';

    public string $period = 'today';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin()
            || $user?->role === 'super_admin'
            || $user?->roleModel?->slug === 'super_admin';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Site Raporlari';
    }

    public function report(): array
    {
        return app(SiteReportService::class)->report($this->period, $this->startDate, $this->endDate);
    }

    public function exportUrl(string $format): string
    {
        return route('admin.site-reports.export', [
            'format' => $format,
            'period' => $this->period,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
        ]);
    }
}
