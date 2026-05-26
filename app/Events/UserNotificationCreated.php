<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserNotificationCreated implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public bool $afterCommit = true;

    public function __construct(public Notification $notification) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->notification->user_id.'.notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user-notification.created';
    }

    public function broadcastQueue(): string
    {
        return config('realtime.queues.notifications', 'notifications');
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => e($this->notification->title),
            'message' => e($this->notification->message),
            'url' => $this->notification->url,
            'is_read' => $this->notification->is_read,
            'created_at' => $this->notification->created_at?->format('d.m.Y H:i'),
        ];
    }
}
