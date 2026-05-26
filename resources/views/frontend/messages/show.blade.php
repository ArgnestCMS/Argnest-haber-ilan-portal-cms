@extends('frontend.layout')

@section('title', ($otherUser?->name ?? 'Konusma') . ' Mesajlari')
@section('meta_description', 'Ozel mesaj konusmasi.')
@section('robots', 'noindex, nofollow')

@section('content')
<style>
    @media (max-width: 767px) {
        .mobile-dm-shell {
            padding-bottom: calc(82px + env(safe-area-inset-bottom, 0px));
        }

        .mobile-dm-list {
            max-height: 58vh;
        }

        .mobile-dm-composer {
            position: sticky;
            bottom: calc(74px + env(safe-area-inset-bottom, 0px));
            z-index: 20;
        }
    }
</style>

<section
    x-data="privateConversation({
        latestUrl: '{{ route('messages.latest', $conversation) }}',
        readUrl: '{{ route('messages.read', $conversation) }}',
        typingUrl: '{{ route('messages.typing', $conversation) }}',
        typingStoreUrl: '{{ route('messages.typing.store', $conversation) }}',
        conversationId: {{ $conversation->id }},
        currentUserId: {{ auth()->id() }},
        lastMessageId: {{ $conversation->messages->max('id') ?? 0 }},
    })"
    x-init="init()"
    class="mobile-dm-shell mx-auto max-w-7xl px-3 py-6 md:px-4 md:py-8"
>
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-bold text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid gap-5 lg:grid-cols-[320px_1fr]">
        <aside class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:block">
            <div class="border-b border-slate-100 p-4">
                <div class="text-lg font-black text-slate-950">Konusmalar</div>
                <a href="{{ route('messages.index') }}" class="mt-1 inline-block text-xs font-black text-red-700">Tum mesajlar</a>
            </div>

            <div class="max-h-[680px] divide-y divide-slate-100 overflow-y-auto">
                @foreach($sidebarConversations as $sidebarConversation)
                    @php
                        $sidebarOther = $sidebarConversation->participants
                            ->firstWhere('user_id', '!=', auth()->id())
                            ?->user;
                        $sidebarParticipant = $sidebarConversation->participants->firstWhere('user_id', auth()->id());
                        $sidebarLatest = $sidebarConversation->latestMessage;
                        $sidebarUnread = $unreadCounts[$sidebarConversation->id] ?? 0;
                    @endphp

                    <a href="{{ route('messages.show', $sidebarConversation) }}" class="block p-4 transition {{ $sidebarConversation->id === $conversation->id ? 'bg-red-50' : 'hover:bg-slate-50' }}">
                        <div class="flex items-center gap-3">
                            <div class="relative flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-950 text-sm font-black text-white">
                                @if($sidebarOther?->avatar)
                                    <img src="{{ asset('storage/' . $sidebarOther->avatar) }}" class="h-full w-full object-cover" alt="{{ $sidebarOther->name }}">
                                @else
                                    {{ str($sidebarOther?->name ?? 'U')->substr(0, 1)->upper() }}
                                @endif
                                @if($sidebarOther?->isOnline())
                                    <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-green-500"></span>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <div class="truncate text-sm font-black text-slate-950">{{ $sidebarOther?->name ?? 'Silinmis uye' }}</div>
                                    @if($sidebarParticipant?->is_pinned)
                                        <span class="text-[11px] font-black text-blue-700">Sabit</span>
                                    @endif
                                    @if($sidebarUnread > 0)
                                        <span class="ml-auto rounded-full bg-red-600 px-2 py-0.5 text-[10px] font-black text-white">{{ $sidebarUnread }}</span>
                                    @endif
                                </div>
                                <div class="mt-1 truncate text-xs font-bold text-slate-500">
                                    @if($sidebarLatest)
                                        {{ $sidebarLatest->sender_id === auth()->id() ? 'Siz: ' : '' }}{{ $sidebarLatest->trashed() ? 'Bu mesaj silindi.' : \Illuminate\Support\Str::limit($sidebarLatest->body, 60) }}
                                    @else
                                        Henuz mesaj yok.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </aside>

        <div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('messages.index') }}" class="text-sm font-black text-red-700">Mesajlara Don</a>

        <div class="flex gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0">
            <form method="POST" action="{{ route('messages.mute', $conversation) }}">
                @csrf
                <button class="rounded-lg bg-slate-200 px-4 py-2 text-xs font-black text-slate-800 transition hover:bg-slate-300">
                    {{ $participant?->is_muted ? 'Sesi Ac' : 'Sessize Al' }}
                </button>
            </form>

            <form method="POST" action="{{ route('messages.pin', $conversation) }}">
                @csrf
                <button class="rounded-lg bg-slate-200 px-4 py-2 text-xs font-black text-slate-800 transition hover:bg-slate-300">
                    {{ $participant?->is_pinned ? 'Sabitten Cikar' : 'Sabitle' }}
                </button>
            </form>

            @if($otherUser)
                @if(auth()->user()->hasBlockedMessagesFrom($otherUser))
                    <form method="POST" action="{{ route('messages.unblock', $otherUser) }}">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-lg bg-green-600 px-4 py-2 text-xs font-black text-white transition hover:bg-green-700">
                            Engeli Kaldir
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('messages.block', $otherUser) }}">
                        @csrf
                        <button class="rounded-lg bg-slate-950 px-4 py-2 text-xs font-black text-white transition hover:bg-red-700">
                            Engelle
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center gap-3 border-b border-slate-100 p-4 md:gap-4 md:p-5">
            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-slate-950 text-xl font-black text-white">
                @if($otherUser?->avatar)
                    <img src="{{ asset('storage/' . $otherUser->avatar) }}" class="h-full w-full object-cover" alt="{{ $otherUser->name }}">
                @else
                    {{ str($otherUser?->name ?? 'U')->substr(0, 1)->upper() }}
                @endif
            </div>

            <div class="min-w-0">
                <h1 class="truncate text-xl font-black text-slate-950 md:text-2xl">{{ $otherUser?->name ?? 'Silinmis uye' }}</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">
                    {{ $conversation->status === 'accepted' ? 'Aktif konusma' : ($conversation->status === 'rejected' ? 'Reddedilmis istek' : 'Mesaj istegi') }}
                    @if($otherUser)
                        · {{ $otherUser->isOnline() ? 'Online' : 'Offline' }}
                        @if($otherUser->last_seen_at)
                            · Son gorulme {{ $otherUser->last_seen_at->diffForHumans() }}
                        @endif
                    @endif
                </p>
            </div>
        </div>

        @if($conversation->status === 'pending' && $conversation->requested_by !== auth()->id())
            <div class="border-b border-amber-100 bg-amber-50 p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="font-black text-amber-900">Bu bir mesaj istegi</div>
                        <p class="mt-1 text-sm font-bold text-amber-700">Kabul etmeden karsilikli mesajlasma baslamaz.</p>
                    </div>

                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('messages.accept', $conversation) }}">
                            @csrf
                            <button class="rounded-lg bg-green-600 px-4 py-2 text-sm font-black text-white transition hover:bg-green-700">Kabul Et</button>
                        </form>

                        <form method="POST" action="{{ route('messages.reject', $conversation) }}">
                            @csrf
                            <button class="rounded-lg bg-white px-4 py-2 text-sm font-black text-slate-800 ring-1 ring-slate-200 transition hover:bg-slate-50">Reddet</button>
                        </form>
                    </div>
                </div>
            </div>
        @elseif($conversation->status === 'pending')
            <div class="border-b border-amber-100 bg-amber-50 p-5 text-sm font-bold text-amber-800">
                Mesaj isteginiz yanit bekliyor. Kabul edilmeden yeni mesaj gonderilemez.
            </div>
        @elseif($conversation->status === 'rejected')
            <div class="border-b border-red-100 bg-red-50 p-5 text-sm font-bold text-red-800">
                Bu mesaj istegi reddedildi.
            </div>
        @endif

        @if($isBlocked)
            <div class="border-b border-red-100 bg-red-50 p-5 text-sm font-bold text-red-800">
                Engelleme nedeniyle bu konusmada mesaj gonderilemez.
            </div>
        @endif

        <div id="message-list" class="mobile-dm-list max-h-[560px] space-y-3 overflow-y-auto bg-slate-50 p-3 md:space-y-4 md:p-5">
            @foreach($conversation->messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                    <div class="max-w-[88%] rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-slate-950 text-white' : 'bg-white text-slate-900 ring-1 ring-slate-200' }} px-4 py-3 shadow-sm md:max-w-[82%]">
                        <div class="text-xs font-black {{ $message->sender_id === auth()->id() ? 'text-slate-300' : 'text-slate-500' }}">
                            {{ $message->sender?->name ?? 'Uye' }} · {{ $message->created_at?->format('H:i') }}
                            @if($message->ai_review_required)
                                · riskli
                            @endif
                            @if($message->edited_at && ! $message->trashed())
                                · duzenlendi
                            @endif
                        </div>
                        <div class="mt-1 whitespace-pre-line text-sm leading-6" data-message-body>{{ $message->trashed() ? 'Bu mesaj silindi.' : $message->body }}</div>

                        @if(! $message->trashed())
                            @php($reactionSummary = $message->reactionSummary())
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                @foreach(['like' => 'Like', 'heart' => 'Kalp', 'laugh' => 'Gul'] as $reaction => $label)
                                    <form method="POST" action="{{ route('messages.reactions.toggle', [$conversation, $message]) }}">
                                        @csrf
                                        <input type="hidden" name="reaction" value="{{ $reaction }}">
                                        <button class="rounded-full bg-white/10 px-2 py-1 text-[11px] font-black {{ $message->sender_id === auth()->id() ? 'text-white ring-1 ring-white/20' : 'text-slate-600 ring-1 ring-slate-200' }}">
                                            {{ $label }} {{ $reactionSummary[$reaction] ?? 0 }}
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif

                        @if($message->canBeEditedBy(auth()->user()) || $message->canBeDeletedBy(auth()->user()))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($message->canBeEditedBy(auth()->user()))
                                    <details class="w-full">
                                        <summary class="cursor-pointer text-xs font-black {{ $message->sender_id === auth()->id() ? 'text-slate-300' : 'text-red-700' }}">Duzenle</summary>
                                        <form method="POST" action="{{ route('messages.edit', [$conversation, $message]) }}" class="mt-2 space-y-2">
                                            @csrf
                                            @method('PATCH')
                                            <textarea name="body" rows="2" class="w-full rounded-lg border-slate-300 text-sm text-slate-900">{{ $message->body }}</textarea>
                                            <button class="rounded-lg bg-red-600 px-3 py-2 text-xs font-black text-white">Kaydet</button>
                                        </form>
                                    </details>
                                @endif

                                @if($message->canBeDeletedBy(auth()->user()))
                                    <form method="POST" action="{{ route('messages.destroy', [$conversation, $message]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-xs font-black {{ $message->sender_id === auth()->id() ? 'text-slate-300' : 'text-red-700' }}">Sil</button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-slate-100 bg-white px-4 py-2 text-xs font-bold text-slate-500 md:px-5" x-show="typingText" x-text="typingText" style="display:none;"></div>

        <div class="mobile-dm-composer border-t border-slate-100 bg-white p-3 md:p-5">
            @if($conversation->status === 'accepted' && ! $isBlocked)
                <form method="POST" action="{{ route('messages.store', $conversation) }}" class="space-y-3">
                    @csrf
                    <textarea
                        name="body"
                        rows="3"
                        required
                        maxlength="2000"
                        x-ref="messageInput"
                        @input="sendTyping()"
                        class="w-full rounded-xl border-slate-300 text-base focus:border-red-500 focus:ring-red-500 md:text-sm"
                        placeholder="Mesajinizi yazin..."
                    >{{ old('body') }}</textarea>

                    <div class="flex gap-2 overflow-x-auto pb-1 sm:flex-wrap sm:overflow-visible sm:pb-0">
                        @foreach(['👍', '❤', '😂', '👏', '🔥', '🙏'] as $emoji)
                            <button type="button" @click="insertEmoji('{{ $emoji }}')" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-black transition hover:bg-slate-200">
                                {{ $emoji }}
                            </button>
                        @endforeach
                    </div>

                    @error('body')
                        <p class="text-sm font-bold text-red-700">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end">
                        <button class="w-full rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700 sm:w-auto">
                            Gonder
                        </button>
                    </div>
                </form>
            @else
                <div class="rounded-xl bg-slate-100 p-4 text-sm font-bold text-slate-500">
                    Mesaj gonderimi su anda kapali.
                </div>
            @endif
        </div>
    </div>
        </div>
    </div>
</section>

<script>
function privateConversation(config) {
    return {
        lastMessageId: Number(config.lastMessageId || 0),
        typingText: '',
        lastTypingAt: 0,
        init() {
            this.scrollBottom();
            this.listenRealtime();
            setInterval(() => this.fetchLatest(), 5000);
            setInterval(() => this.markRead(), 15000);
        },
        fetchLatest() {
            fetch(config.latestUrl + '?after_id=' + this.lastMessageId)
                .then(response => response.json())
                .then(data => {
                    (data.messages || []).forEach(message => this.appendMessage(message));
                    (data.changed_messages || []).forEach(message => this.updateMessage(message));
                    this.setTypingUsers(data.typing_users || []);
                })
                .catch(() => {});
        },
        markRead() {
            fetch(config.readUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            }).catch(() => {});
        },
        listenRealtime() {
            if (!window.Echo) {
                return;
            }

            window.Echo.private('conversations.' + config.conversationId)
                .listen('.private-message.sent', message => this.appendMessage(message));
        },
        appendMessage(message) {
            if (!message || Number(message.id) <= this.lastMessageId) {
                return;
            }

            const list = document.getElementById('message-list');
            const own = Number(message.sender_id) === Number(config.currentUserId);
            const wrapper = document.createElement('div');
            wrapper.className = 'flex ' + (own ? 'justify-end' : 'justify-start');
            wrapper.dataset.messageId = message.id;
            wrapper.innerHTML = `
                <div class="max-w-[82%] rounded-2xl ${own ? 'bg-slate-950 text-white' : 'bg-white text-slate-900 ring-1 ring-slate-200'} px-4 py-3 shadow-sm">
                    <div class="text-xs font-black ${own ? 'text-slate-300' : 'text-slate-500'}">${message.sender || 'Uye'} · ${message.time || ''}</div>
                    <div class="mt-1 whitespace-pre-line text-sm leading-6">${message.body || ''}</div>
                </div>
            `;
            list.appendChild(wrapper);
            this.lastMessageId = Number(message.id);
            this.scrollBottom();
        },
        updateMessage(message) {
            if (!message) {
                return;
            }

            const wrapper = document.querySelector(`[data-message-id="${message.id}"]`);
            if (!wrapper) {
                if (Number(message.id) > this.lastMessageId) {
                    this.appendMessage(message);
                }

                return;
            }

            const body = wrapper.querySelector('[data-message-body]') || wrapper.querySelector('.leading-6');
            if (body) {
                body.textContent = message.body || '';
                body.classList.toggle('italic', Boolean(message.is_deleted));
                body.classList.toggle('opacity-70', Boolean(message.is_deleted));
            }
        },
        sendTyping() {
            const now = Date.now();

            if (now - this.lastTypingAt < 3000) {
                return;
            }

            this.lastTypingAt = now;
            fetch(config.typingStoreUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            }).catch(() => {});
        },
        setTypingUsers(users) {
            const names = (users || []).map(user => user.name).filter(Boolean);
            this.typingText = names.length ? names.join(', ') + ' yaziyor...' : '';
        },
        insertEmoji(emoji) {
            const input = this.$refs.messageInput;

            if (!input) {
                return;
            }

            const start = input.selectionStart || input.value.length;
            const end = input.selectionEnd || input.value.length;
            input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
            input.focus();
            input.selectionStart = input.selectionEnd = start + emoji.length;
            this.sendTyping();
        },
        scrollBottom() {
            requestAnimationFrame(() => {
                const list = document.getElementById('message-list');
                if (list) {
                    list.scrollTop = list.scrollHeight;
                }
            });
        },
    };
}
</script>
@endsection




