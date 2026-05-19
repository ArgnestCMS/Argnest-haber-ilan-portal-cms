@extends('frontend.layout')

@section('title', $user->name . ' Sosyal Profili')

@section('meta_description', str($user->bio ?: $user->name . ' kullanicisinin forum profili, rozetleri, reputation seviyesi ve topluluk aktiviteleri.')->limit(155))

@section('content')

<section
    x-data="profilePresence({
        userUrl: '{{ route('presence.user', $user) }}',
        initialOnline: @js($user->isOnline()),
        initialLastSeen: @js($user->last_seen_at?->diffForHumans()),
    })"
    x-init="init()"
    class="mx-auto max-w-7xl px-4 py-8"
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

    <div class="overflow-hidden rounded-3xl bg-white shadow">
        <div class="h-52 bg-slate-950"></div>

        <div class="p-6 md:p-8">
            <div class="-mt-28 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="flex flex-col gap-5 md:flex-row md:items-end">
                    <div class="h-40 w-40 overflow-hidden rounded-3xl border-8 border-white bg-slate-100 shadow-xl">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-6xl font-black text-slate-400">
                                {{ str($user->name)->substr(0, 1)->upper() }}
                            </div>
                        @endif
                    </div>

                    <div class="pb-2">
                        <h1 class="text-4xl font-black text-slate-950">{{ $user->name }}</h1>
                        <p class="mt-2 text-sm font-bold text-slate-500">
                            {{ $user->isOnline() ? 'Online' : 'Offline' }} · {{ $user->created_at?->format('d.m.Y') }} tarihinden beri uye
                        </p>

                        <p class="mt-2 text-sm font-bold text-slate-500">
                            <span
                                class="rounded-full px-2 py-0.5 text-xs font-black"
                                :class="isOnline ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                                x-text="isOnline ? 'Realtime Online' : 'Realtime Offline'"
                            ></span>
                            <span class="ml-2" x-text="lastSeen ? 'Son gorulme: ' + lastSeen : ''"></span>
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">{{ $user->forum_reputation ?? 0 }} reputation</span>
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-black text-indigo-700">Seviye {{ $user->forum_level ?? 1 }}</span>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">{{ number_format($user->forum_xp ?? 0) }} XP</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $user->followers_count }} takipci</span>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $user->following_count }} takip</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    @auth
                        @if(auth()->id() === $user->id)
                            <a href="{{ route('forum.dashboard') }}" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                                Forum Panelim
                            </a>
                        @else
                            <form method="POST" action="{{ route('users.follow.toggle', $user) }}">
                                @csrf
                                <button type="submit" class="rounded-lg {{ $isFollowing ? 'bg-slate-200 text-slate-800 hover:bg-slate-300' : 'bg-red-600 text-white hover:bg-red-700' }} px-5 py-3 text-sm font-black transition">
                                    {{ $isFollowing ? 'Takipten Cik' : 'Takip Et' }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                            Takip Et
                        </a>
                    @endauth
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1fr_360px]">
                <div class="space-y-6">
                    @if($user->bio)
                        <section class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                            <h2 class="text-xl font-black text-slate-950">Hakkinda</h2>
                            <p class="mt-3 leading-7 text-slate-700">{{ $user->bio }}</p>
                        </section>
                    @endif

                    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="text-xs font-black uppercase text-slate-400">Seviye</div>
                            <div class="mt-2 text-3xl font-black text-slate-950">{{ $user->forum_level ?? 1 }}</div>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-red-600" style="width: {{ $levelProgress['percent'] ?? 0 }}%"></div>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="text-xs font-black uppercase text-slate-400">XP</div>
                            <div class="mt-2 text-3xl font-black text-slate-950">{{ number_format($user->forum_xp ?? 0) }}</div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="text-xs font-black uppercase text-slate-400">Forum Konusu</div>
                            <div class="mt-2 text-3xl font-black text-slate-950">{{ $forumStats['topics'] ?? 0 }}</div>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="text-xs font-black uppercase text-slate-400">Forum Cevabi</div>
                            <div class="mt-2 text-3xl font-black text-slate-950">{{ $forumStats['posts'] ?? 0 }}</div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-slate-950">Activity Feed</h2>
                        <div class="mt-4 divide-y divide-slate-100">
                            @forelse($activityFeed as $activity)
                                <a href="{{ $activity['url'] ?: '#' }}" class="block py-4">
                                    <div class="text-sm font-black text-slate-900">{{ $activity['title'] }}</div>
                                    @if($activity['message'])
                                        <div class="mt-1 text-sm text-slate-600">{{ $activity['message'] }}</div>
                                    @endif
                                    <div class="mt-2 text-xs font-bold text-slate-400">{{ $activity['source'] }} · {{ $activity['relative_time'] }}</div>
                                </a>
                            @empty
                                <div class="py-5 text-sm font-bold text-slate-500">Henuz public aktivite yok.</div>
                            @endforelse
                        </div>
                    </section>

                    <div class="grid gap-6 xl:grid-cols-2">
                        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-xl font-black text-slate-950">Son Forum Konulari</h2>
                            <div class="mt-4 divide-y divide-slate-100">
                                @forelse($latestForumTopics as $topic)
                                    <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block py-3">
                                        <div class="font-black text-slate-800">{{ $topic->title }}</div>
                                        <div class="mt-1 text-xs font-bold text-slate-500">{{ $topic->created_at?->diffForHumans() }} · {{ number_format($topic->views) }} goruntulenme</div>
                                    </a>
                                @empty
                                    <div class="py-4 text-sm text-slate-500">Yayinlanmis forum konusu yok.</div>
                                @endforelse
                            </div>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h2 class="text-xl font-black text-slate-950">Son Forum Cevaplari</h2>
                            <div class="mt-4 divide-y divide-slate-100">
                                @forelse($latestForumPosts as $post)
                                    <a href="{{ $post->topic?->status === 'published' ? route('forum.topics.show', $post->topic->slug) : '#' }}" class="block py-3">
                                        <div class="font-black text-slate-800">{{ $post->topic?->title ?? 'Silinmis konu' }}</div>
                                        <div class="mt-1 text-sm text-slate-600">{{ \Illuminate\Support\Str::limit(\App\Support\ForumContent::plainText($post->content), 120) }}</div>
                                        <div class="mt-1 text-xs font-bold text-slate-500">{{ $post->created_at?->diffForHumans() }}</div>
                                    </a>
                                @empty
                                    <div class="py-4 text-sm text-slate-500">Onaylanmis forum cevabi yok.</div>
                                @endforelse
                            </div>
                        </section>
                    </div>
                </div>

                <aside class="space-y-6">
                    @auth
                        @if(auth()->id() === $user->id)
                            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h2 class="text-xl font-black text-slate-950">Mesaj Ayarlari</h2>
                                <form method="POST" action="{{ route('messages.settings.update') }}" class="mt-4 space-y-3">
                                    @csrf
                                    @method('PATCH')

                                    <label class="block">
                                        <span class="text-sm font-bold text-slate-600">Kim mesaj istegi gonderebilir?</span>
                                        <select name="message_privacy" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500">
                                            <option value="followers" @selected(($user->message_privacy ?? 'followers') === 'followers')>Takipcilerim</option>
                                            <option value="everyone" @selected(($user->message_privacy ?? 'followers') === 'everyone')>Herkes</option>
                                            <option value="none" @selected(($user->message_privacy ?? 'followers') === 'none')>Kimse</option>
                                        </select>
                                    </label>

                                    <button class="w-full rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-red-700">
                                        Kaydet
                                    </button>
                                </form>
                            </section>
                        @else
                            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                                <h2 class="text-xl font-black text-slate-950">Ozel Mesaj</h2>

                                @if(auth()->user()->hasBlockedMessagesFrom($user))
                                    <form method="POST" action="{{ route('messages.unblock', $user) }}" class="mt-4">
                                        @csrf
                                        @method('DELETE')
                                        <button class="w-full rounded-lg bg-green-600 px-4 py-3 text-sm font-black text-white transition hover:bg-green-700">
                                            Mesaj Engelini Kaldir
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('messages.start', $user) }}" class="mt-4 space-y-3">
                                        @csrf
                                        <textarea
                                            name="body"
                                            rows="4"
                                            required
                                            maxlength="2000"
                                            class="w-full rounded-xl border-slate-300 text-sm focus:border-red-500 focus:ring-red-500"
                                            placeholder="Kisa bir mesaj yazin..."
                                        >{{ old('body') }}</textarea>

                                        <button class="w-full rounded-lg bg-red-600 px-4 py-3 text-sm font-black text-white transition hover:bg-red-700">
                                            Mesaj Istegi Gonder
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('messages.block', $user) }}" class="mt-3">
                                        @csrf
                                        <button class="w-full rounded-lg bg-slate-200 px-4 py-3 text-xs font-black text-slate-700 transition hover:bg-slate-300">
                                            Mesajlasmayi Engelle
                                        </button>
                                    </form>
                                @endif
                            </section>
                        @endif
                    @endauth

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-slate-950">Rozet Vitrini</h2>
                        <div class="mt-4 flex flex-wrap gap-2">
                            @forelse($user->forumBadges as $badge)
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $badge->name }}</span>
                            @empty
                                <span class="text-sm font-bold text-slate-500">Henuz rozet yok.</span>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-slate-950">Mini Sosyal Kart</h2>
                        <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-2xl bg-slate-950 text-xl font-black text-white">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="h-full w-full object-cover" alt="{{ $user->name }}">
                                    @else
                                        {{ str($user->name)->substr(0, 1)->upper() }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-black text-slate-950">{{ $user->name }}</div>
                                    <div class="text-xs font-bold text-slate-500">Seviye {{ $user->forum_level ?? 1 }} · {{ $user->forum_reputation ?? 0 }} rep</div>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                                <div class="rounded-xl bg-white p-3">
                                    <div class="text-xl font-black text-slate-950">{{ $user->followers_count }}</div>
                                    <div class="text-xs font-bold text-slate-500">Takipci</div>
                                </div>
                                <div class="rounded-xl bg-white p-3">
                                    <div class="text-xl font-black text-slate-950">{{ $user->following_count }}</div>
                                    <div class="text-xs font-bold text-slate-500">Takip</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-slate-950">Takipciler</h2>
                        <div class="mt-4 space-y-3">
                            @forelse($followersPreview as $follower)
                                <a href="{{ url('/profil/' . $follower->id) }}" class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3">
                                    <div>
                                        <div class="text-sm font-black text-slate-900">{{ $follower->name }}</div>
                                        <div class="text-xs font-bold text-slate-500">{{ $follower->forum_reputation ?? 0 }} rep</div>
                                    </div>
                                    <span class="text-xs font-black text-red-700">Profil</span>
                                </a>
                            @empty
                                <div class="text-sm font-bold text-slate-500">Henuz takipci yok.</div>
                            @endforelse
                        </div>
                    </section>

                    <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-black text-slate-950">Takip Ettikleri</h2>
                        <div class="mt-4 space-y-3">
                            @forelse($followingPreview as $followed)
                                <a href="{{ url('/profil/' . $followed->id) }}" class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3">
                                    <div>
                                        <div class="text-sm font-black text-slate-900">{{ $followed->name }}</div>
                                        <div class="text-xs font-bold text-slate-500">Seviye {{ $followed->forum_level ?? 1 }}</div>
                                    </div>
                                    <span class="text-xs font-black text-red-700">Profil</span>
                                </a>
                            @empty
                                <div class="text-sm font-bold text-slate-500">Henuz kimse takip edilmiyor.</div>
                            @endforelse
                        </div>
                    </section>
                </aside>
            </div>
        </div>
    </div>
</section>

<script>
function profilePresence(config) {
    return {
        isOnline: config.initialOnline,
        lastSeen: config.initialLastSeen,
        init() {
            this.refresh();
            setInterval(() => this.refresh(), 15000);
        },
        refresh() {
            fetch(config.userUrl)
                .then(response => response.json())
                .then(data => {
                    this.isOnline = Boolean(data.is_online);
                    this.lastSeen = data.last_seen;
                })
                .catch(() => {});
        },
    }
}
</script>

@endsection
