<?php

namespace App\Console\Commands;

use App\Jobs\SendPushNotificationJob;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Console\Command;

class SendTestPushNotification extends Command
{
    protected $signature = 'push:test {user_id : Bildirim gonderilecek kullanici id}';

    protected $description = 'Create a test notification and dispatch the push notification job.';

    public function handle(): int
    {
        $user = User::query()->find((int) $this->argument('user_id'));

        if (! $user) {
            $this->error('Kullanici bulunamadi.');

            return self::FAILURE;
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

        SendPushNotificationJob::dispatch($notification->id);

        $this->info('Test notification olusturuldu ve push job dispatch edildi. Notification ID: ' . $notification->id);

        return self::SUCCESS;
    }
}
