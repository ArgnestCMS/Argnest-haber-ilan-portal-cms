<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLiveChatMessageRequest;
use App\Models\LiveChatMessage;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\UserPunishment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    public function messages(): JsonResponse
    {
        $messages = LiveChatMessage::approved()
            ->with('user:id,name,last_seen_at,forum_reputation')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (LiveChatMessage $message) => [
                'id' => $message->id,
                'user' => $message->user?->name ?? 'Sistem',
                'message' => e($message->message),
                'time' => $message->created_at?->format('H:i'),
                'is_online' => $message->user?->isOnline() ?? false,
                'reputation' => $message->user?->forum_reputation ?? 0,
            ]);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(StoreLiveChatMessageRequest $request): JsonResponse
    {
        if (! $this->liveChatIsEnabled()) {
            return response()->json([
                'message' => 'Canlı sohbet şu anda kapalı.',
            ], 403);
        }

        if ($this->userIsMuted()) {
            return response()->json([
                'message' => 'Sohbet yazma yetkiniz kısıtlanmıştır.',
            ], 403);
        }

        if ($this->hasFloodRisk()) {
            return response()->json([
                'message' => 'Çok kısa sürede fazla mesaj gönderdiniz.',
            ], 429);
        }

        $content = trim((string) $request->string('message'));

        if ($this->hasSpamRisk($content)) {
            return response()->json([
                'message' => 'Mesaj spam filtresine takıldı.',
            ], 422);
        }

        $message = LiveChatMessage::create([
            'user_id' => auth()->id(),
            'message' => $content,
            'status' => 'approved',
            'ip_address' => $request->ip(),
        ]);

        auth()->user()?->forceFill([
            'last_seen_at' => now(),
        ])->save();

        return response()->json([
            'message' => 'Mesaj gönderildi.',
            'data' => [
                'id' => $message->id,
            ],
        ], 201);
    }

    public function onlineUsers(): JsonResponse
    {
        $users = User::query()
            ->where('last_seen_at', '>=', now()->subMinutes(5))
            ->orderBy('name')
            ->take(20)
            ->get(['id', 'name', 'forum_reputation', 'last_seen_at'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'reputation' => $user->forum_reputation ?? 0,
            ]);

        return response()->json([
            'users' => $users,
        ]);
    }

    private function liveChatIsEnabled(): bool
    {
        return (bool) (SiteSetting::query()->first()?->live_chat_enabled ?? false);
    }

    private function userIsMuted(): bool
    {
        return UserPunishment::query()
            ->where('user_id', auth()->id())
            ->where('is_active', true)
            ->whereIn('type', ['mute', 'temporary_ban', 'permanent_ban'])
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    private function hasFloodRisk(): bool
    {
        return LiveChatMessage::query()
            ->where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subSeconds(20))
            ->count() >= 3;
    }

    private function hasSpamRisk(string $content): bool
    {
        $lowerContent = Str::lower($content);
        $bannedWords = ['spam', 'dolandırıcılık', 'küfür1', 'küfür2', 'hakaret1', 'hakaret2'];

        foreach ($bannedWords as $word) {
            if (Str::contains($lowerContent, Str::lower($word))) {
                return true;
            }
        }

        preg_match_all('/https?:\/\/|www\.|\.com|\.net|\.org|\.xyz/i', $content, $matches);

        return count($matches[0]) >= 2;
    }
}
