<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Support\PushNotificationSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPushNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 45;

    public array $backoff = [30, 120, 300];

    public function __construct(public int $notificationId)
    {
        $this->onQueue(config('realtime.queues.notifications', 'notifications'));
    }

    public function handle(PushNotificationSender $sender): void
    {
        $notification = Notification::query()->find($this->notificationId);

        if (! $notification) {
            return;
        }

        $sender->sendForNotification($notification);
    }
}
