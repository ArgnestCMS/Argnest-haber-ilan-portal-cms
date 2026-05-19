@extends('frontend.layout')

@section('title', 'Mesajlar')
@section('meta_description', 'Ozel mesaj kutunuz, mesaj istekleri ve okunmamis konusmalar.')
@section('robots', 'noindex, nofollow')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-8">
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

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-950">Mesajlar</h1>
            <p class="mt-2 text-sm font-bold text-slate-500">Ozel konusmalar ve mesaj istekleri.</p>
        </div>

        <a href="{{ route('forum.dashboard') }}" class="rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-red-700">
            Forum Panelim
        </a>
    </div>

    <form method="GET" action="{{ route('messages.index') }}" class="mb-5 flex gap-2">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            class="min-w-0 flex-1 rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500"
            placeholder="Mesajlarda veya kullanici adinda ara..."
        >
        <button class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
            Ara
        </button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="divide-y divide-slate-100">
            @forelse($conversations as $conversation)
                @php
                    $otherUser = $conversation->participants
                        ->firstWhere('user_id', '!=', auth()->id())
                        ?->user;
                    $currentParticipant = $conversation->participants->firstWhere('user_id', auth()->id());
                    $latestMessage = $conversation->latestMessage;
                    $unread = $unreadCounts[$conversation->id] ?? 0;
                    $statusLabel = match($conversation->status) {
                        'accepted' => 'Aktif',
                        'rejected' => 'Reddedildi',
                        default => 'Istek',
                    };
                @endphp

                <a href="{{ route('messages.show', $conversation) }}" class="flex flex-col gap-4 p-5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-slate-950 text-xl font-black text-white">
                            @if($otherUser?->avatar)
                                <img src="{{ asset('storage/' . $otherUser->avatar) }}" class="h-full w-full object-cover" alt="{{ $otherUser->name }}">
                            @else
                                {{ str($otherUser?->name ?? 'U')->substr(0, 1)->upper() }}
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate font-black text-slate-950">{{ $otherUser?->name ?? 'Silinmis uye' }}</h2>
                                <span class="rounded-full {{ $conversation->status === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }} px-2 py-0.5 text-[11px] font-black">
                                    {{ $statusLabel }}
                                </span>
                                @if($unread > 0)
                                    <span class="rounded-full bg-red-600 px-2 py-0.5 text-[11px] font-black text-white">{{ $unread }} yeni</span>
                                @endif
                                @if($currentParticipant?->is_pinned)
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-black text-blue-700">Sabit</span>
                                @endif
                                @if($currentParticipant?->is_muted)
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-black text-slate-500">Sessiz</span>
                                @endif
                                @if($otherUser?->isOnline())
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-black text-green-700">Online</span>
                                @endif
                            </div>

                            <p class="mt-1 truncate text-sm font-bold text-slate-500">
                                @if($latestMessage)
                                    {{ $latestMessage->sender_id === auth()->id() ? 'Siz: ' : '' }}{{ $latestMessage->trashed() ? 'Bu mesaj silindi.' : \Illuminate\Support\Str::limit($latestMessage->body, 120) }}
                                    @if($latestMessage->edited_at && ! $latestMessage->trashed())
                                        <span class="text-xs text-slate-400">(duzenlendi)</span>
                                    @endif
                                @else
                                    Henuz mesaj yok.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="text-xs font-bold text-slate-400">
                        {{ $latestMessage?->created_at?->diffForHumans() ?? $conversation->created_at?->diffForHumans() }}
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <div class="text-xl font-black text-slate-950">Henuz mesaj yok</div>
                    <p class="mt-2 text-sm font-bold text-slate-500">Bir kullanici profilinden mesaj istegi baslatabilirsiniz.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        {{ $conversations->links() }}
    </div>
</section>
@endsection
