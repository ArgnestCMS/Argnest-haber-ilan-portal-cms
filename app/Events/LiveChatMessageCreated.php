<?php

namespace App\Events;

use App\Models\LiveActivity;
use App\Models\LiveChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LiveChatMessageCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public bool $afterCommit = true;

    public function __construct(
        public LiveChatMessage $message,
        public ?LiveActivity $activity = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel(config('realtime.channels.live_chat', 'live.chat')),
        ];
    }

    public function broadcastAs(): string
    {
        return 'live-chat.message.created';
    }

    public function broadcastQueue(): string
    {
        return config('realtime.queues.broadcasts', 'broadcasts');
    }

    public function broadcastWith(): array
    {
        $this->message->loadMissing('user:id,name,last_seen_at,forum_reputation');

        return [
            'id' => $this->message->id,
            'user_id' => $this->message->user_id,
            'user' => $this->message->user?->name ?? 'Sistem',
            'message' => e($this->message->message),
            'time' => $this->message->created_at?->format('H:i'),
            'is_online' => $this->message->user?->isOnline() ?? false,
            'reputation' => $this->message->user?->forum_reputation ?? 0,
            'report_url' => route('reports.live-chat-messages.store', $this->message),
            'activity_id' => $this->activity?->id,
        ];
    }
}
