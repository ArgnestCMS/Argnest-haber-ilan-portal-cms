<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use App\Helpers\NotificationHelper;
use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\PrivateMessageReaction;
use App\Models\User;
use App\Models\UserMessageBlock;
use App\Support\CommunitySafety;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PrivateMessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $search = trim((string) request('q'));

        $conversations = Conversation::query()
            ->select('conversations.*')
            ->join('conversation_participants as current_participant', function ($join) use ($user) {
                $join->on('current_participant.conversation_id', '=', 'conversations.id')
                    ->where('current_participant.user_id', $user->id);
            })
            ->when($search !== '', function ($query) use ($search, $user) {
                $query->where(function ($nested) use ($search, $user) {
                    $nested->whereHas('participants.user', function ($participants) use ($search, $user) {
                        $participants->where('users.id', '!=', $user->id)
                            ->where('users.name', 'like', '%' . $search . '%');
                    })->orWhereHas('messages', function ($messages) use ($search) {
                        $messages->withTrashed()
                            ->where('body', 'like', '%' . $search . '%');
                    });
                });
            })
            ->with([
                'participants.user:id,name,avatar,last_seen_at,message_privacy',
                'latestMessage.sender:id,name',
            ])
            ->orderByDesc('current_participant.is_pinned')
            ->orderByDesc('current_participant.pinned_at')
            ->orderByDesc('conversations.updated_at')
            ->paginate(15);

        $unreadCounts = $this->unreadCounts($conversations->pluck('id')->all(), $user->id);

        return view('frontend.messages.index', compact('conversations', 'unreadCounts', 'search'));
    }

    public function count(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $this->unreadConversationCount($request->user()->id),
        ]);
    }

    public function show(Conversation $conversation)
    {
        $user = auth()->user();
        $this->abortUnlessParticipant($conversation, $user);

        $conversation->load([
            'participants.user:id,name,avatar,last_seen_at,message_privacy',
            'messages.sender:id,name,avatar,last_seen_at',
            'messages.reactions.user:id,name',
            'requester:id,name',
        ]);

        $participant = $conversation->participantFor($user);
        $otherUser = $conversation->otherParticipant($user);
        $isBlocked = $otherUser
            ? ($user->hasBlockedMessagesFrom($otherUser) || $otherUser->hasBlockedMessagesFrom($user))
            : false;

        $conversation->markReadFor($user);

        $sidebarConversations = $this->sidebarConversations($user);
        $unreadCounts = $this->unreadCounts($sidebarConversations->pluck('id')->all(), $user->id);

        return view('frontend.messages.show', compact('conversation', 'participant', 'otherUser', 'isBlocked', 'sidebarConversations', 'unreadCounts'));
    }

    public function start(Request $request, User $user): RedirectResponse
    {
        $sender = $request->user();

        if ($sender->id === $user->id) {
            return back()->with('error', 'Kendinize mesaj gonderemezsiniz.');
        }

        if (! $user->canReceiveMessageRequestFrom($sender)) {
            return back()->with('error', 'Bu kullanici su anda sizden mesaj istegi kabul etmiyor.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:2', 'max:2000'],
        ]);

        if ($this->hasFloodRisk($sender)) {
            return back()->with('error', 'Cok kisa surede fazla mesaj gonderdiniz.');
        }

        $body = trim(strip_tags($validated['body']));
        $safety = CommunitySafety::assess($body, $sender, 'private_message');

        if ($safety->shouldReject()) {
            return back()->with('error', 'Mesaj guvenlik filtresine takildi.');
        }

        $conversation = $this->findDirectConversation($sender, $user);

        if ($conversation) {
            if ($conversation->status === 'rejected') {
                $conversation->update([
                    'status' => 'pending',
                    'requested_by' => $sender->id,
                    'accepted_at' => null,
                ]);
            }

            if ($conversation->status === 'accepted') {
                $message = $this->createMessage($conversation, $sender, $body, $safety->attributes());
                $this->notifyNewMessage($conversation, $message, $user);
                PrivateMessageSent::dispatch($message);

                return redirect()
                    ->route('messages.show', $conversation)
                    ->with('success', 'Mesaj gonderildi.');
            }

            $pendingMessages = $conversation->messages()
                ->where('sender_id', $sender->id)
                ->count();

            if ($pendingMessages >= 1) {
                return redirect()
                    ->route('messages.show', $conversation)
                    ->with('error', 'Mesaj isteginiz yanit bekliyor.');
            }
        }

        [$conversation, $message] = DB::transaction(function () use ($conversation, $sender, $user, $body, $safety) {
            $conversation ??= Conversation::create([
                'type' => 'direct',
                'status' => 'pending',
                'requested_by' => $sender->id,
            ]);

            $conversation->participants()->firstOrCreate(['user_id' => $sender->id]);
            $conversation->participants()->firstOrCreate(['user_id' => $user->id]);

            $message = $this->createMessage($conversation, $sender, $body, $safety->attributes());

            return [$conversation, $message];
        });

        NotificationHelper::sendToUser(
            userId: $user->id,
            type: 'message_request',
            title: 'Yeni mesaj istegi',
            message: $sender->name . ' size mesaj istegi gonderdi.',
            url: route('messages.show', $conversation),
            data: ['conversation_id' => $conversation->id]
        );

        PrivateMessageSent::dispatch($message);

        return redirect()
            ->route('messages.show', $conversation)
            ->with('success', 'Mesaj istegi gonderildi.');
    }

    public function store(Request $request, Conversation $conversation): RedirectResponse
    {
        $sender = $request->user();
        $this->abortUnlessParticipant($conversation, $sender);

        $conversation->load('participants.user');
        $recipient = $conversation->otherParticipant($sender);

        if (! $recipient) {
            return back()->with('error', 'Alici bulunamadi.');
        }

        if ($conversation->status !== 'accepted') {
            return back()->with('error', 'Mesajlasma baslamadan once istek kabul edilmeli.');
        }

        if ($sender->hasBlockedMessagesFrom($recipient) || $recipient->hasBlockedMessagesFrom($sender)) {
            return back()->with('error', 'Engellenen kullanicilar arasinda mesaj gonderilemez.');
        }

        if ($this->hasFloodRisk($sender)) {
            return back()->with('error', 'Cok kisa surede fazla mesaj gonderdiniz.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $body = trim(strip_tags($validated['body']));
        $safety = CommunitySafety::assess($body, $sender, 'private_message');

        if ($safety->shouldReject()) {
            return back()->with('error', 'Mesaj guvenlik filtresine takildi.');
        }

        $message = $this->createMessage($conversation, $sender, $body, $safety->attributes());
        $conversation->touch();

        $this->notifyNewMessage($conversation, $message, $recipient);
        PrivateMessageSent::dispatch($message);

        return back()->with('success', 'Mesaj gonderildi.');
    }

    public function accept(Conversation $conversation): RedirectResponse
    {
        $user = auth()->user();
        $this->abortUnlessParticipant($conversation, $user);

        if ($conversation->requested_by === $user->id) {
            return back()->with('error', 'Kendi mesaj isteginizi kabul edemezsiniz.');
        }

        $conversation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        NotificationHelper::sendToUser(
            userId: $conversation->requested_by,
            type: 'message_request_accepted',
            title: 'Mesaj isteginiz kabul edildi',
            message: $user->name . ' mesaj isteginizi kabul etti.',
            url: route('messages.show', $conversation),
            data: ['conversation_id' => $conversation->id]
        );

        return back()->with('success', 'Mesaj istegi kabul edildi.');
    }

    public function reject(Conversation $conversation): RedirectResponse
    {
        $user = auth()->user();
        $this->abortUnlessParticipant($conversation, $user);

        if ($conversation->requested_by === $user->id) {
            return back()->with('error', 'Kendi mesaj isteginizi reddedemezsiniz.');
        }

        $conversation->update([
            'status' => 'rejected',
            'accepted_at' => null,
        ]);

        NotificationHelper::sendToUser(
            userId: $conversation->requested_by,
            type: 'message_request_rejected',
            title: 'Mesaj isteginiz reddedildi',
            message: $user->name . ' mesaj isteginizi reddetti.',
            url: route('messages.index'),
            data: ['conversation_id' => $conversation->id]
        );

        return redirect()->route('messages.index')->with('success', 'Mesaj istegi reddedildi.');
    }

    public function markRead(Conversation $conversation): JsonResponse
    {
        $user = auth()->user();
        $this->abortUnlessParticipant($conversation, $user);

        $conversation->markReadFor($user);

        return response()->json(['ok' => true]);
    }

    public function latest(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $this->abortUnlessParticipant($conversation, $user);

        $afterId = (int) $request->integer('after_id');

        $messages = $conversation->messages()
            ->with(['sender:id,name,avatar,last_seen_at', 'reactions.user:id,name'])
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->oldest()
            ->take(50)
            ->get()
            ->map(fn (PrivateMessage $message) => $this->messagePayload($message, $user));

        $changedMessages = $conversation->messages()
            ->with(['sender:id,name,avatar,last_seen_at', 'reactions.user:id,name'])
            ->where('updated_at', '>=', now()->subSeconds(40))
            ->when($afterId > 0, fn ($query) => $query->where('id', '<=', $afterId))
            ->oldest()
            ->take(50)
            ->get()
            ->map(fn (PrivateMessage $message) => $this->messagePayload($message, $user));

        if ($messages->isNotEmpty()) {
            $conversation->markReadFor($user);
        }

        return response()->json([
            'messages' => $messages,
            'changed_messages' => $changedMessages,
            'typing_users' => $this->typingUsersFor($conversation, $user),
            'status' => $conversation->status,
        ]);
    }

    public function edit(Request $request, Conversation $conversation, PrivateMessage $message): RedirectResponse
    {
        $user = $request->user();
        $this->abortUnlessParticipant($conversation, $user);
        $this->abortUnlessMessageBelongsToConversation($conversation, $message);

        if (! $message->canBeEditedBy($user)) {
            return back()->with('error', 'Mesaj duzenleme suresi dolmus.');
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $body = trim(strip_tags($validated['body']));
        $safety = CommunitySafety::assess($body, $user, 'private_message');

        if ($safety->shouldReject()) {
            return back()->with('error', 'Mesaj guvenlik filtresine takildi.');
        }

        $message->update([
            'body' => Str::limit($body, 2000, ''),
            'status' => $safety->requiresReview() ? 'flagged' : 'sent',
            'edited_at' => now(),
            ...$safety->attributes(),
        ]);

        $conversation->touch();

        return back()->with('success', 'Mesaj duzenlendi.');
    }

    public function destroy(Conversation $conversation, PrivateMessage $message): RedirectResponse
    {
        $user = auth()->user();
        $this->abortUnlessParticipant($conversation, $user);
        $this->abortUnlessMessageBelongsToConversation($conversation, $message);

        if (! $message->canBeDeletedBy($user)) {
            return back()->with('error', 'Bu mesaj silinemez.');
        }

        $message->delete();
        $conversation->touch();

        return back()->with('success', 'Mesaj silindi.');
    }

    public function react(Request $request, Conversation $conversation, PrivateMessage $message): RedirectResponse
    {
        $user = $request->user();
        $this->abortUnlessParticipant($conversation, $user);
        $this->abortUnlessMessageBelongsToConversation($conversation, $message);

        if ($message->trashed()) {
            return back()->with('error', 'Silinmis mesaja tepki verilemez.');
        }

        $validated = $request->validate([
            'reaction' => ['required', Rule::in(['like', 'heart', 'laugh'])],
        ]);

        $existing = PrivateMessageReaction::query()
            ->where('private_message_id', $message->id)
            ->where('user_id', $user->id)
            ->where('reaction', $validated['reaction'])
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            PrivateMessageReaction::create([
                'private_message_id' => $message->id,
                'user_id' => $user->id,
                'reaction' => $validated['reaction'],
            ]);
        }

        $message->touch();

        return back();
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'message_privacy' => ['required', Rule::in(['everyone', 'followers', 'none'])],
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Mesaj gizlilik ayari guncellendi.');
    }

    public function toggleMute(Conversation $conversation): RedirectResponse
    {
        $user = auth()->user();
        $this->abortUnlessParticipant($conversation, $user);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $participant->update(['is_muted' => ! $participant->is_muted]);

        return back()->with('success', $participant->is_muted ? 'Konusma sessize alindi.' : 'Konusma sesi acildi.');
    }

    public function togglePin(Conversation $conversation): RedirectResponse
    {
        $user = auth()->user();
        $this->abortUnlessParticipant($conversation, $user);

        $participant = $conversation->participants()->where('user_id', $user->id)->firstOrFail();
        $isPinned = ! $participant->is_pinned;

        $participant->update([
            'is_pinned' => $isPinned,
            'pinned_at' => $isPinned ? now() : null,
        ]);

        return back()->with('success', $isPinned ? 'Konusma sabitlendi.' : 'Konusma sabitten cikarildi.');
    }

    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $this->abortUnlessParticipant($conversation, $user);

        Cache::put($this->typingCacheKey($conversation->id, $user->id), [
            'id' => $user->id,
            'name' => $user->name,
        ], now()->addSeconds(8));

        return response()->json(['ok' => true]);
    }

    public function typingUsers(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $request->user();
        $this->abortUnlessParticipant($conversation, $user);

        return response()->json([
            'users' => $this->typingUsersFor($conversation, $user),
        ]);
    }

    public function block(User $user): RedirectResponse
    {
        $actor = auth()->user();

        if ($actor->id === $user->id) {
            return back()->with('error', 'Kendinizi engelleyemezsiniz.');
        }

        UserMessageBlock::firstOrCreate([
            'blocker_id' => $actor->id,
            'blocked_id' => $user->id,
        ]);

        return back()->with('success', 'Kullanici mesajlasma icin engellendi.');
    }

    public function unblock(User $user): RedirectResponse
    {
        auth()->user()
            ->messageBlocks()
            ->where('blocked_id', $user->id)
            ->delete();

        return back()->with('success', 'Mesaj engeli kaldirildi.');
    }

    private function findDirectConversation(User $first, User $second): ?Conversation
    {
        return Conversation::query()
            ->where('type', 'direct')
            ->whereHas('participants', fn ($query) => $query->where('user_id', $first->id))
            ->whereHas('participants', fn ($query) => $query->where('user_id', $second->id))
            ->withCount('participants')
            ->having('participants_count', 2)
            ->first();
    }

    private function createMessage(Conversation $conversation, User $sender, string $body, array $safetyAttributes): PrivateMessage
    {
        $message = $conversation->messages()->create([
            'sender_id' => $sender->id,
            'body' => Str::limit($body, 2000, ''),
            'status' => ($safetyAttributes['ai_review_required'] ?? false) ? 'flagged' : 'sent',
            ...$safetyAttributes,
        ]);

        $conversation->participants()
            ->where('user_id', $sender->id)
            ->update(['last_read_at' => now()]);

        $conversation->touch();

        return $message;
    }

    private function notifyNewMessage(Conversation $conversation, PrivateMessage $message, User $recipient): void
    {
        $participant = $conversation->participants()
            ->where('user_id', $recipient->id)
            ->first();

        if ($participant?->is_muted) {
            return;
        }

        NotificationHelper::sendToUser(
            userId: $recipient->id,
            type: 'private_message',
            title: 'Yeni ozel mesaj',
            message: $message->sender?->name . ': ' . Str::limit($message->body, 80),
            url: route('messages.show', $conversation),
            data: [
                'conversation_id' => $conversation->id,
                'message_id' => $message->id,
            ]
        );
    }

    private function unreadCounts(array $conversationIds, int $userId): array
    {
        if (empty($conversationIds)) {
            return [];
        }

        return Conversation::query()
            ->whereIn('id', $conversationIds)
            ->with('participants')
            ->get()
            ->mapWithKeys(function (Conversation $conversation) use ($userId) {
                $participant = $conversation->participants->firstWhere('user_id', $userId);
                $lastReadAt = $participant?->last_read_at;

                $count = $conversation->messages()
                    ->where('sender_id', '!=', $userId)
                    ->whereNull('deleted_at')
                    ->when($lastReadAt, fn ($query) => $query->where('created_at', '>', $lastReadAt))
                    ->count();

                return [$conversation->id => $count];
            })
            ->all();
    }

    private function unreadConversationCount(int $userId): int
    {
        return DB::table('conversation_participants as cp')
            ->where('cp.user_id', $userId)
            ->whereExists(function ($query) use ($userId) {
                $query->selectRaw('1')
                    ->from('private_messages as pm')
                    ->whereColumn('pm.conversation_id', 'cp.conversation_id')
                    ->where('pm.sender_id', '!=', $userId)
                    ->whereNull('pm.deleted_at')
                    ->where(function ($nested) {
                        $nested->whereNull('cp.last_read_at')
                            ->orWhereColumn('pm.created_at', '>', 'cp.last_read_at');
                    });
            })
            ->count();
    }

    private function sidebarConversations(User $user)
    {
        return Conversation::query()
            ->select('conversations.*')
            ->join('conversation_participants as current_participant', function ($join) use ($user) {
                $join->on('current_participant.conversation_id', '=', 'conversations.id')
                    ->where('current_participant.user_id', $user->id);
            })
            ->with([
                'participants.user:id,name,avatar,last_seen_at,message_privacy',
                'latestMessage.sender:id,name',
            ])
            ->orderByDesc('current_participant.is_pinned')
            ->orderByDesc('current_participant.pinned_at')
            ->orderByDesc('conversations.updated_at')
            ->take(12)
            ->get();
    }

    private function messagePayload(PrivateMessage $message, User $viewer): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'sender' => e($message->sender?->name ?? 'Uye'),
            'body' => $message->trashed() ? 'Bu mesaj silindi.' : e($message->body),
            'is_deleted' => $message->trashed(),
            'is_edited' => (bool) $message->edited_at,
            'can_edit' => $message->canBeEditedBy($viewer),
            'time' => $message->created_at?->format('H:i'),
            'created_at' => $message->created_at?->toISOString(),
            'updated_at' => $message->updated_at?->toISOString(),
            'reactions' => $message->reactionSummary(),
        ];
    }

    private function typingUsersFor(Conversation $conversation, User $viewer): array
    {
        $conversation->loadMissing('participants.user:id,name');

        return $conversation->participants
            ->reject(fn ($participant) => (int) $participant->user_id === (int) $viewer->id)
            ->map(fn ($participant) => Cache::get($this->typingCacheKey($conversation->id, $participant->user_id)))
            ->filter()
            ->values()
            ->all();
    }

    private function typingCacheKey(int $conversationId, int $userId): string
    {
        return 'dm_typing:' . $conversationId . ':' . $userId;
    }

    private function hasFloodRisk(User $user): bool
    {
        $recentMessages = PrivateMessage::query()
            ->where('sender_id', $user->id)
            ->where('created_at', '>=', now()->subMinute())
            ->count();

        $recentRequests = Conversation::query()
            ->where('requested_by', $user->id)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        return $recentMessages >= 5 || $recentRequests >= 3;
    }

    private function abortUnlessParticipant(Conversation $conversation, User $user): void
    {
        abort_unless($conversation->isParticipant($user), 403);
    }

    private function abortUnlessMessageBelongsToConversation(Conversation $conversation, PrivateMessage $message): void
    {
        abort_unless((int) $message->conversation_id === (int) $conversation->id, 404);
    }
}
