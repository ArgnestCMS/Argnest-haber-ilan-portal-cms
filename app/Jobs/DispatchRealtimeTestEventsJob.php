<?php

namespace App\Jobs;

use App\Events\LiveChatMessageCreated;
use App\Events\UserNotificationCreated;
use App\Models\LiveActivity;
use App\Models\LiveChatMessage;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DispatchRealtimeTestEventsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 30;

    public array $backoff = [5, 15];

    public function __construct(public ?int $userId = null)
    {
        $this->onQueue(config('realtime.queues.events', 'realtime'));
    }

    public function handle(): void
    {
        $activity = LiveActivity::record([
            'user_id' => $this->userId,
            'type' => 'realtime_test_event',
            'source' => 'system',
            'severity' => 'info',
            'title' => 'Realtime altyapi test eventi',
            'message' => 'Broadcast ve queue omurgasi test edildi.',
            'metadata' => [
                'queue' => config('realtime.queues.events', 'realtime'),
                'broadcast_queue' => config('realtime.queues.broadcasts', 'broadcasts'),
            ],
        ]);

        $message = LiveChatMessage::approved()
            ->with('user:id,name,last_seen_at,forum_reputation')
            ->latest()
            ->first();

        if ($message) {
            LiveChatMessageCreated::dispatch($message, $activity);
        }

        if ($this->userId) {
            $notification = Notification::create([
                'user_id' => $this->userId,
                'type' => 'realtime_test_event',
                'title' => 'Realtime test bildirimi',
                'message' => 'Notification broadcast hazirligi test edildi.',
                'url' => route('live-activity.index'),
                'data' => ['activity_id' => $activity->id],
                'is_read' => false,
            ]);

            UserNotificationCreated::dispatch($notification);
        }
    }
}
