<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{userId}.notifications', function (User $user, int $userId): bool {
    return $user->id === $userId;
});

Broadcast::channel('admin.live-activity', function (User $user): bool {
    return $user->isAdmin() || $user->hasPermission('forum_moderasyonu');
});

Broadcast::channel('live.chat.presence', function (User $user): array {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'reputation' => $user->forum_reputation ?? 0,
        'level' => $user->forum_level ?? 1,
    ];
});

Broadcast::channel('forum.topic.{topicId}', function (User $user, int $topicId): array {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'reputation' => $user->forum_reputation ?? 0,
        'level' => $user->forum_level ?? 1,
        'profile_url' => url('/profil/' . $user->id),
    ];
});
