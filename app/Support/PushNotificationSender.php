<?php

namespace App\Support;

use App\Models\Notification;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;

class PushNotificationSender
{
    public function sendForNotification(Notification $notification): void
    {
        if (! config('services.webpush.enabled')) {
            return;
        }

        $subscriptions = PushSubscription::query()
            ->where('user_id', $notification->user_id)
            ->where('is_enabled', true)
            ->get()
            ->filter(fn (PushSubscription $subscription) => $subscription->allows($notification->type));

        if ($subscriptions->isEmpty()) {
            return;
        }

        if (! class_exists(\Minishlink\WebPush\WebPush::class)) {
            Log::info('Web push dependency is not installed. Skipping push send.', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
            ]);

            return;
        }

        $webPush = new \Minishlink\WebPush\WebPush([
            'VAPID' => [
                'subject' => config('services.webpush.vapid.subject'),
                'publicKey' => config('services.webpush.vapid.public_key'),
                'privateKey' => config('services.webpush.vapid.private_key'),
            ],
        ]);

        $payload = json_encode([
            'title' => $notification->title,
            'body' => $notification->message,
            'url' => $notification->url ?: route('user.notifications'),
            'tag' => $notification->type,
            'notification_id' => $notification->id,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                \Minishlink\WebPush\Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding,
                ]),
                $payload
            );
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            $subscription = $subscriptions->firstWhere('endpoint', $endpoint);

            if (! $subscription) {
                continue;
            }

            if ($report->isSuccess()) {
                $subscription->markSent();
            } else {
                $subscription->markFailure();

                Log::warning('Web push notification failed.', [
                    'notification_id' => $notification->id,
                    'subscription_id' => $subscription->id,
                    'reason' => $report->getReason(),
                ]);
            }
        }
    }
}
