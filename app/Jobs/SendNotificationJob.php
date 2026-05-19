<?php

namespace App\Jobs;

use App\Events\UserNotificationCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    public array $users;

    public string $type;

    public string $title;

    public string $message;

    public ?string $url;

    public array $data;

    /**
     * Create a new job instance.
     */
    public function __construct(
        array $users,
        string $type,
        string $title,
        string $message,
        ?string $url = null,
        array $data = []
    ) {
        $this->users = $users;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->users as $userId) {

            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $this->type,
                'title' => $this->title,
                'message' => $this->message,
                'url' => $this->url,
                'data' => $this->data,
                'is_read' => false,
            ]);

            UserNotificationCreated::dispatch($notification);
            SendPushNotificationJob::dispatch($notification->id);
        }
    }
}
