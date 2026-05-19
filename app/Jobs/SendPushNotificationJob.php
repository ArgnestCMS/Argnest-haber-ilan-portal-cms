<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Support\PushNotificationSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendPushNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $notificationId) {}

    public function handle(PushNotificationSender $sender): void
    {
        $notification = Notification::query()->find($this->notificationId);

        if (! $notification) {
            return;
        }

        $sender->sendForNotification($notification);
    }
}
