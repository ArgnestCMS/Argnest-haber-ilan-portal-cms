<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HealthCheckController extends Controller
{
    private const QUEUES = [
        'broadcasts',
        'realtime',
        'notifications',
        'media',
        'safety',
        'default',
    ];

    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->databaseCheck(),
            'queue' => $this->queueCheck(),
            'storage' => $this->storageCheck(),
            'disk' => $this->diskCheck(),
            'reverb' => $this->reverbCheck(),
        ];

        $healthy = collect($checks)->every(fn (array $check) => $check['ok']);

        return response()->json([
            'ok' => $healthy,
            'checked_at' => now()->toISOString(),
            'environment' => app()->environment(),
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }

    private function databaseCheck(): array
    {
        try {
            DB::select('select 1');

            return [
                'ok' => true,
                'connection' => config('database.default'),
            ];
        } catch (Throwable $exception) {
            return $this->failedCheck($exception);
        }
    }

    private function queueCheck(): array
    {
        try {
            $connection = config('queue.default');
            $sizes = collect(self::QUEUES)
                ->mapWithKeys(fn (string $queue) => [$queue => Queue::connection($connection)->size($queue)])
                ->all();

            $failedJobs = DB::table(config('queue.failed.table', 'failed_jobs'))->count();

            return [
                'ok' => true,
                'connection' => $connection,
                'sizes' => $sizes,
                'failed_jobs' => $failedJobs,
            ];
        } catch (Throwable $exception) {
            return $this->failedCheck($exception);
        }
    }

    private function storageCheck(): array
    {
        try {
            $publicPath = public_path('storage');
            $targetPath = storage_path('app/public');
            $probePath = 'health/.probe';

            Storage::disk('public')->put($probePath, now()->toISOString());
            $canWritePublicDisk = Storage::disk('public')->exists($probePath);
            Storage::disk('public')->delete($probePath);

            return [
                'ok' => $canWritePublicDisk && File::exists($targetPath),
                'public_disk_writable' => $canWritePublicDisk,
                'public_storage_path_exists' => File::exists($targetPath),
                'public_symlink_exists' => File::exists($publicPath),
                'public_symlink_is_link' => is_link($publicPath),
            ];
        } catch (Throwable $exception) {
            return $this->failedCheck($exception);
        }
    }

    private function diskCheck(): array
    {
        $path = storage_path();
        $total = disk_total_space($path) ?: 0;
        $free = disk_free_space($path) ?: 0;
        $usedPercent = $total > 0 ? round((($total - $free) / $total) * 100, 2) : null;

        return [
            'ok' => $usedPercent === null || $usedPercent < 90,
            'path' => $path,
            'used_percent' => $usedPercent,
            'free_bytes' => $free,
            'total_bytes' => $total,
        ];
    }

    private function reverbCheck(): array
    {
        return [
            'ok' => true,
            'broadcast_connection' => config('broadcasting.default'),
            'host' => config('broadcasting.connections.reverb.options.host'),
            'port' => config('broadcasting.connections.reverb.options.port'),
            'scheme' => config('broadcasting.connections.reverb.options.scheme'),
            'note' => 'Configuration readiness only; websocket process monitoring should be handled by the process manager.',
        ];
    }

    private function failedCheck(Throwable $exception): array
    {
        return [
            'ok' => false,
            'error' => class_basename($exception),
            'message' => $exception->getMessage(),
        ];
    }
}
