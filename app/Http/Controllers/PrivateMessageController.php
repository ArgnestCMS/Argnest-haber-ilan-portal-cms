<?php

namespace App\Http\Controllers;

use App\Events\PrivateMessageSent;
use App\Helpers\NotificationHelper;
use App\Models\Conversation;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Models\UserMessageBlock;
use App\Support\CommunitySafety;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PrivateMessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $conversations = Conversation::query()
            ->whereHas('participants', fn ($query) => $query->where('user_id', $user->id))
            ->with([
                'participants.user:id,name,avatar,last_seen_at,message_privacy',
                'latestMessage.sender:id,name',
            ])
            ->latest('updated_at')
            ->paginate(15);

        $unreadCounts = $this->unreadCounts($conversations->pluck('id')->all(), $user->id);

        return view('frontend.messages.index', compact('conversations', 'unreadCounts'));
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
            'requester:id,name',
        ]);

        $participant = $conversation->participantFor($user);
        $otherUser = $conversation->otherParticipant($user);
        $isBlocked = $otherUser
            ? ($user->hasBlockedMessagesFrom($otherUser) || $otherUser->hasBlockedMessagesFrom($user))
            : false;

        $conversation->markReadFor($user);

        return view('frontend.messages.show', compact('conversation', 'participant', 'otherUser', 'isBlocked'));
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
            ->with('sender:id,name,avatar,last_seen_at')
            ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
            ->oldest()
            ->take(50)
            ->get()
            ->map(fn (PrivateMessage $message) => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sender' => e($message->sender?->name ?? 'Uye'),
                'body' => e($message->body),
                'time' => $message->created_at?->format('H:i'),
                'created_at' => $message->created_at?->toISOString(),
            ]);

        if ($messages->isNotEmpty()) {
            $conversation->markReadFor($user);
        }

        return response()->json([
            'messages' => $messages,
            'status' => $conversation->status,
        ]);
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
                    ->where(function ($nested) {
                        $nested->whereNull('cp.last_read_at')
                            ->orWhereColumn('pm.created_at', '>', 'cp.last_read_at');
                    });
            })
            ->count();
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
}
