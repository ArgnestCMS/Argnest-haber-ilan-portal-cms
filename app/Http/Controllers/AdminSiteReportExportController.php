<?php

namespace App\Http\Controllers;

use App\Services\SiteReportService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminSiteReportExportController extends Controller
{
    public function __invoke(string $format, Request $request, SiteReportService $reports): StreamedResponse|BinaryFileResponse
    {
        $this->authorizeReportAccess();
        set_time_limit(0);

        $report = $reports->report(
            (string) $request->query('period', 'today'),
            $request->query('start_date'),
            $request->query('end_date'),
        );
        $rows = $reports->exportRows($report);
        $fileBase = 'site-raporu-' . now()->format('Y-m-d-H-i-s');

        if ($format === 'xlsx') {
            return $this->downloadXlsx($rows, $fileBase . '.xlsx');
        }

        abort_unless($format === 'csv', 404);

        return response()->streamDownload(function () use ($rows): void {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['sep=,']);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $fileBase . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function downloadXlsx(array $rows, string $fileName): BinaryFileResponse
    {
        $path = storage_path('app/reports/' . $fileName);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $writer = new Writer();
        $writer->openToFile($path);

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();

        return response()->download($path, $fileName)->deleteFileAfterSend(true);
    }

    private function authorizeReportAccess(): void
    {
        $user = auth()->user();

        if (
            ! $user
            || (
                ! $user->isAdmin()
                && $user->role !== 'super_admin'
                && $user->roleModel?->slug !== 'super_admin'
            )
        ) {
            abort(403);
        }
    }
}
