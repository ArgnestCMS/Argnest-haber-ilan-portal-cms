<?php

namespace App\Console\Commands;

use App\Jobs\DispatchRealtimeTestEventsJob;
use Illuminate\Console\Command;

class DispatchRealtimeTestEvents extends Command
{
    protected $signature = 'realtime:test-events {--user-id= : Optional user id for private notification broadcast test} {--sync : Run immediately instead of pushing to queue}';

    protected $description = 'Dispatch realtime infrastructure test events for broadcast and queue preparation';

    public function handle(): int
    {
        $userId = $this->option('user-id') ? (int) $this->option('user-id') : null;
        $job = new DispatchRealtimeTestEventsJob($userId);

        if ($this->option('sync')) {
            dispatch_sync($job);
            $this->info('Realtime test events dispatched synchronously.');
        } else {
            dispatch($job);
            $this->info('Realtime test event job queued.');
        }

        $this->line('Worker queue hint: php artisan queue:work --queue=' . implode(',', [
            config('realtime.queues.broadcasts', 'broadcasts'),
            config('realtime.queues.events', 'realtime'),
            config('realtime.queues.notifications', 'notifications'),
            'default',
        ]));

        return self::SUCCESS;
    }
}
