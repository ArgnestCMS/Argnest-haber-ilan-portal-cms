@extends('frontend.layout')

@section('title', ($otherUser?->name ?? 'Konusma') . ' Mesajlari')
@section('meta_description', 'Ozel mesaj konusmasi.')
@section('robots', 'noindex, nofollow')

@section('content')
<section
    x-data="privateConversation({
        latestUrl: '{{ route('messages.latest', $conversation) }}',
        readUrl: '{{ route('messages.read', $conversation) }}',
        conversationId: {{ $conversation->id }},
        currentUserId: {{ auth()->id() }},
        lastMessageId: {{ $conversation->messages->max('id') ?? 0 }},
    })"
    x-init="init()"
    class="mx-auto max-w-5xl px-4 py-8"
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

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('messages.index') }}" class="text-sm font-black text-red-700">Mesajlara Don</a>

        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('messages.mute', $conversation) }}">
                @csrf
                <button class="rounded-lg bg-slate-200 px-4 py-2 text-xs font-black text-slate-800 transition hover:bg-slate-300">
                    {{ $participant?->is_muted ? 'Sesi Ac' : 'Sessize Al' }}
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
        <div class="flex items-center gap-4 border-b border-slate-100 p-5">
            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-slate-950 text-xl font-black text-white">
                @if($otherUser?->avatar)
                    <img src="{{ asset('storage/' . $otherUser->avatar) }}" class="h-full w-full object-cover" alt="{{ $otherUser->name }}">
                @else
                    {{ str($otherUser?->name ?? 'U')->substr(0, 1)->upper() }}
                @endif
            </div>

            <div class="min-w-0">
                <h1 class="truncate text-2xl font-black text-slate-950">{{ $otherUser?->name ?? 'Silinmis uye' }}</h1>
                <p class="mt-1 text-xs font-bold text-slate-500">
                    {{ $conversation->status === 'accepted' ? 'Aktif konusma' : ($conversation->status === 'rejected' ? 'Reddedilmis istek' : 'Mesaj istegi') }}
                    @if($otherUser)
                        · {{ $otherUser->isOnline() ? 'Online' : 'Offline' }}
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

        <div id="message-list" class="max-h-[560px] space-y-4 overflow-y-auto bg-slate-50 p-5">
            @foreach($conversation->messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                    <div class="max-w-[82%] rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-slate-950 text-white' : 'bg-white text-slate-900 ring-1 ring-slate-200' }} px-4 py-3 shadow-sm">
                        <div class="text-xs font-black {{ $message->sender_id === auth()->id() ? 'text-slate-300' : 'text-slate-500' }}">
                            {{ $message->sender?->name ?? 'Uye' }} · {{ $message->created_at?->format('H:i') }}
                            @if($message->ai_review_required)
                                · riskli
                            @endif
                        </div>
                        <div class="mt-1 whitespace-pre-line text-sm leading-6">{{ $message->body }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-t border-slate-100 p-5">
            @if($conversation->status === 'accepted' && ! $isBlocked)
                <form method="POST" action="{{ route('messages.store', $conversation) }}" class="space-y-3">
                    @csrf
                    <textarea
                        name="body"
                        rows="3"
                        required
                        maxlength="2000"
                        class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500"
                        placeholder="Mesajinizi yazin..."
                    >{{ old('body') }}</textarea>

                    @error('body')
                        <p class="text-sm font-bold text-red-700">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end">
                        <button class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
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
</section>

<script>
function privateConversation(config) {
    return {
        lastMessageId: Number(config.lastMessageId || 0),
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
