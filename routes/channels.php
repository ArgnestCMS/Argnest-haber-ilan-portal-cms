<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{userId}.notifications', function (User $user, int $userId): bool {
    return $user->id === $userId;
});

Broadcast::channel('admin.live-activity', function (User $user): bool {
    return $user->isAdmin() || $user->hasPermission('forum_moderasyonu');
});
