<?php

return [

    'queues' => [
        'broadcasts' => env('REALTIME_BROADCAST_QUEUE', 'broadcasts'),
        'events' => env('REALTIME_EVENTS_QUEUE', 'realtime'),
        'notifications' => env('REALTIME_NOTIFICATIONS_QUEUE', 'notifications'),
    ],

    'channels' => [
        'live_chat' => env('REALTIME_LIVE_CHAT_CHANNEL', 'live.chat'),
        'live_activity' => env('REALTIME_ACTIVITY_CHANNEL', 'live.activities'),
        'admin_activity' => env('REALTIME_ADMIN_ACTIVITY_CHANNEL', 'admin.live-activity'),
        'user_notifications_prefix' => env('REALTIME_USER_NOTIFICATIONS_PREFIX', 'users'),
    ],

];
