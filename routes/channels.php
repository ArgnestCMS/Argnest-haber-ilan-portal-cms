<?php

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users.{userId}.notifications', function (User $user, int $userId): bool {
    return $user->id === $userId;
});

Broadcast::channel('users.{userId}.messages', function (User $user, int $userId): bool {
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

Broadcast::channel('conversations.{conversationId}', function (User $user, int $conversationId): bool {
    return Conversation::query()
        ->whereKey($conversationId)
        ->whereHas('participants', fn ($query) => $query->where('user_id', $user->id))
        ->exists();
});
