<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use App\Support\PushNotificationSender;
use Illuminate\Console\Command;

class SendTestPushNotification extends Command
{
    protected $signature = 'push:test {user_id : Bildirim gonderilecek kullanici id}';

    protected $description = 'Create a test notification and attempt a WebPush send.';

    public function handle(PushNotificationSender $sender): int
    {
        $user = User::query()->find((int) $this->argument('user_id'));

        if (! $user) {
            $this->error('Kullanici bulunamadi.');

            return self::FAILURE;
        }

        $hasSubscription = $user->pushSubscriptions()
            ->where('is_enabled', true)
            ->exists();

        if (! $hasSubscription) {
            $this->warn('Bu kullanicinin etkin push subscription kaydi yok. Test bildirimi olusturulmadi.');

            return self::SUCCESS;
        }

        if (! config('services.webpush.enabled')) {
            $this->warn('WEBPUSH_ENABLED=false. Subscription var, ancak gercek push gonderimi kapali.');

            return self::SUCCESS;
        }

        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'push_test',
            'title' => 'Test push bildirimi',
            'message' => 'Push notification altyapisi test bildirimi.',
            'url' => route('user.notifications'),
            'data' => ['source' => 'push:test'],
            'is_read' => false,
        ]);

        $sender->sendForNotification($notification);

        $this->info('Test notification olusturuldu ve WebPush gonderimi denendi. Notification ID: ' . $notification->id);

        return self::SUCCESS;
    }
}
