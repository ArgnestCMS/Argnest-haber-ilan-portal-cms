<?php

namespace App\Events;

use App\Models\PrivateMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public bool $afterCommit = true;

    public function __construct(public PrivateMessage $message) {}

    public function broadcastOn(): array
    {
        $this->message->loadMissing('conversation.participants');

        $channels = [
            new PrivateChannel('conversations.' . $this->message->conversation_id),
        ];

        foreach ($this->message->conversation->participants as $participant) {
            if ((int) $participant->user_id === (int) $this->message->sender_id) {
                continue;
            }

            $channels[] = new PrivateChannel('users.' . $participant->user_id . '.messages');
        }

        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'private-message.sent';
    }

    public function broadcastQueue(): string
    {
        return config('realtime.queues.broadcasts', 'broadcasts');
    }

    public function broadcastWith(): array
    {
        $this->message->loadMissing('sender:id,name,avatar,last_seen_at');

        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender' => e($this->message->sender?->name ?? 'Uye'),
            'body' => e($this->message->body),
            'time' => $this->message->created_at?->format('H:i'),
            'created_at' => $this->message->created_at?->toISOString(),
        ];
    }
}
