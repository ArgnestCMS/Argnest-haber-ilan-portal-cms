<?php

namespace App\Http\Controllers;

use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SocialPresenceController extends Controller
{
    public function heartbeat(Request $request): JsonResponse
    {
        $request->user()?->forceFill(['last_seen_at' => now()])->save();

        return response()->json(['ok' => true]);
    }

    public function user(User $user): JsonResponse
    {
        return response()->json([
            'id' => $user->id,
            'is_online' => $user->isOnline(),
            'last_seen' => $user->last_seen_at?->diffForHumans(),
            'last_seen_at' => $user->last_seen_at?->toIso8601String(),
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $ids = collect($request->input('ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->take(100)
            ->values();

        return response()->json([
            'users' => User::query()
                ->whereIn('id', $ids)
                ->get(['id', 'name', 'last_seen_at'])
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'is_online' => $user->isOnline(),
                    'last_seen' => $user->last_seen_at?->diffForHumans(),
                ])
                ->values(),
        ]);
    }

    public function touchTopic(Request $request, ForumTopic $topic): JsonResponse
    {
        abort_unless($topic->status === 'published', 404);

        $user = $request->user();
        $user->forceFill(['last_seen_at' => now()])->save();

        Cache::put($this->topicPresenceUserKey($topic, $user->id), now()->timestamp, now()->addMinutes(2));
        $this->mergeCacheId($this->topicPresenceIndexKey($topic), $user->id, now()->addMinutes(5));

        return response()->json(['ok' => true]);
    }

    public function topicUsers(ForumTopic $topic): JsonResponse
    {
        abort_unless($topic->status === 'published', 404);

        $userIds = collect(Cache::get($this->topicPresenceIndexKey($topic), []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => Cache::has($this->topicPresenceUserKey($topic, $id)))
            ->unique()
            ->values();

        Cache::put($this->topicPresenceIndexKey($topic), $userIds->all(), now()->addMinutes(5));

        return response()->json([
            'users' => User::query()
                ->whereIn('id', $userIds)
                ->get(['id', 'name', 'avatar', 'forum_reputation', 'forum_level', 'last_seen_at'])
                ->map(fn (User $user) => $this->presenceUserPayload($user))
                ->values(),
        ]);
    }

    public function liveChatTyping(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->forceFill(['last_seen_at' => now()])->save();

        Cache::put('live_chat_typing_user:' . $user->id, now()->timestamp, now()->addSeconds(6));
        $this->mergeCacheId('live_chat_typing_users', $user->id, now()->addMinutes(2));

        return response()->json(['ok' => true]);
    }

    public function liveChatTypingUsers(Request $request): JsonResponse
    {
        $authId = $request->user()?->id;
        $ids = collect(Cache::get('live_chat_typing_users', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id !== $authId && Cache::has('live_chat_typing_user:' . $id))
            ->unique()
            ->values();

        Cache::put('live_chat_typing_users', $ids->all(), now()->addMinutes(2));

        return response()->json([
            'users' => User::query()
                ->whereIn('id', $ids)
                ->get(['id', 'name'])
                ->map(fn (User $user) => ['id' => $user->id, 'name' => $user->name])
                ->values(),
        ]);
    }

    private function topicPresenceIndexKey(ForumTopic $topic): string
    {
        return 'forum_topic_presence:' . $topic->id . ':users';
    }

    private function topicPresenceUserKey(ForumTopic $topic, int $userId): string
    {
        return 'forum_topic_presence:' . $topic->id . ':user:' . $userId;
    }

    private function mergeCacheId(string $key, int $id, \DateTimeInterface $ttl): void
    {
        $ids = collect(Cache::get($key, []))
            ->push($id)
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values()
            ->all();

        Cache::put($key, $ids, $ttl);
    }

    private function presenceUserPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            'reputation' => $user->forum_reputation ?? 0,
            'level' => $user->forum_level ?? 1,
            'is_online' => $user->isOnline(),
            'last_seen' => $user->last_seen_at?->diffForHumans(),
            'profile_url' => url('/profil/' . $user->id),
        ];
    }
}
