<?php

namespace App\Events;

use App\Models\LiveActivity;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveActivityRecorded implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public bool $afterCommit = true;

    public function __construct(public LiveActivity $activity) {}

    public function broadcastOn(): array
    {
        $channels = [
            new PrivateChannel(config('realtime.channels.admin_activity', 'admin.live-activity')),
        ];

        if ($this->activity->is_public) {
            $channels[] = new Channel(config('realtime.channels.live_activity', 'live.activities'));
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'live-activity.recorded';
    }

    public function broadcastQueue(): string
    {
        return config('realtime.queues.broadcasts', 'broadcasts');
    }

    public function broadcastWith(): array
    {
        $this->activity->loadMissing('user:id,name');

        return [
            'activity' => $this->activity->toFeedItem(),
        ];
    }
}
