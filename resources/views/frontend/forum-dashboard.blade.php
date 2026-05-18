@extends('frontend.layout')

@section('title', 'Forum Panelim | ' . ($siteSetting?->site_name ?? 'ilanhaber.net'))

@section('content')

<section class="bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <a href="{{ route('forum.index') }}" class="text-sm font-black text-red-200 transition hover:text-white">
                    Forum
                </a>

                <h1 class="mt-3 text-4xl font-black leading-tight md:text-5xl">
                    Forum Panelim
                </h1>

                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">
                    Konularinizi, cevaplarinizi, favorilerinizi ve moderasyon durumlarini tek yerden takip edin.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/profil/' . $user->id) }}" class="rounded-lg border border-white/20 px-5 py-3 text-sm font-black text-white transition hover:bg-white/10">
                    Profilim
                </a>

                <a href="{{ route('forum.index') }}#forum-kategorileri" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                    Yeni Konu Ac
                </a>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs font-bold uppercase text-slate-300">Reputation</div>
                <div class="mt-2 text-3xl font-black">{{ $user->forum_reputation ?? 0 }}</div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs font-bold uppercase text-slate-300">Konularim</div>
                <div class="mt-2 text-3xl font-black">{{ $stats['topics'] }}</div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs font-bold uppercase text-slate-300">Cevaplarim</div>
                <div class="mt-2 text-3xl font-black">{{ $stats['posts'] }}</div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs font-bold uppercase text-slate-300">Favorilerim</div>
                <div class="mt-2 text-3xl font-black">{{ $stats['favorites'] }}</div>
            </div>

            <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                <div class="text-xs font-bold uppercase text-slate-300">Begendiklerim</div>
                <div class="mt-2 text-3xl font-black">{{ $stats['likes'] }}</div>
            </div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10">
    <div class="grid gap-6 lg:grid-cols-[1.35fr_0.65fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-950">Konularim</h2>
                        <p class="mt-1 text-sm text-slate-600">Tum konu kayitlariniz ve mevcut moderasyon durumlari.</p>
                    </div>
                    <div class="text-sm font-black text-slate-500">{{ $stats['published_topics'] }} yayinlandi</div>
                </div>

                <div class="mt-5 divide-y divide-slate-100">
                    @forelse($myTopics as $topic)
                        <div class="py-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-black uppercase {{ $topic->status === 'published' ? 'bg-green-50 text-green-700' : ($topic->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : 'bg-slate-100 text-slate-600') }}">
                                            {{ $topic->status }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-500">{{ $topic->category?->name }}</span>
                                    </div>

                                    @if($topic->status === 'published')
                                        <a href="{{ route('forum.topics.show', $topic->slug) }}" class="mt-2 block text-lg font-black text-slate-950 transition hover:text-red-700">
                                            {{ $topic->title }}
                                        </a>
                                    @else
                                        <div class="mt-2 text-lg font-black text-slate-950">{{ $topic->title }}</div>
                                    @endif

                                    @if($topic->tags->isNotEmpty())
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @foreach($topic->tags as $tag)
                                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-black text-slate-700">#{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <div class="flex shrink-0 flex-wrap gap-3 text-xs font-bold text-slate-500 md:justify-end">
                                    <span>{{ $topic->posts_count }} cevap</span>
                                    <span>{{ $topic->likes_count }} begeni</span>
                                    <span>{{ number_format($topic->views) }} goruntulenme</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm font-bold text-slate-500">Henuz konu acmadiniz.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $myTopics->links() }}</div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-950">Cevaplarim</h2>
                        <p class="mt-1 text-sm text-slate-600">Yazdiginiz cevaplar ve onay bekleyen gonderiler.</p>
                    </div>
                    <div class="text-sm font-black text-slate-500">{{ $stats['approved_posts'] }} onaylandi</div>
                </div>

                <div class="mt-5 divide-y divide-slate-100">
                    @forelse($myPosts as $post)
                        <div class="py-4">
                            <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full px-2.5 py-1 text-[11px] font-black uppercase {{ $post->status === 'approved' ? 'bg-green-50 text-green-700' : ($post->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }}">
                                            {{ $post->status }}
                                        </span>
                                        <span class="text-xs font-bold text-slate-500">{{ $post->created_at?->diffForHumans() }}</span>
                                    </div>

                                    @if($post->topic?->status === 'published')
                                        <a href="{{ route('forum.topics.show', $post->topic->slug) }}" class="mt-2 block font-black text-slate-950 transition hover:text-red-700">
                                            {{ $post->topic->title }}
                                        </a>
                                    @else
                                        <div class="mt-2 font-black text-slate-950">{{ $post->topic?->title ?? 'Silinmis konu' }}</div>
                                    @endif

                                    <div class="mt-2 text-sm leading-6 text-slate-600">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 180) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm font-bold text-slate-500">Henuz cevap yazmadiniz.</div>
                    @endforelse
                </div>

                <div class="mt-5">{{ $myPosts->links() }}</div>
            </div>

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Favorilerim</h2>

                    <div class="mt-5 divide-y divide-slate-100">
                        @forelse($favoriteTopics as $topic)
                            <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block py-3">
                                <div class="font-black text-slate-900">{{ $topic->title }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-500">{{ $topic->category?->name }} - {{ $topic->bookmarks_count }} favori</div>
                            </a>
                        @empty
                            <div class="py-8 text-sm font-bold text-slate-500">Favori konunuz yok.</div>
                        @endforelse
                    </div>

                    <div class="mt-5">{{ $favoriteTopics->links() }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-2xl font-black text-slate-950">Begendiklerim</h2>

                    <div class="mt-5 divide-y divide-slate-100">
                        @forelse($likedTopics as $topic)
                            <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block py-3">
                                <div class="font-black text-slate-900">{{ $topic->title }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-500">{{ $topic->category?->name }} - {{ $topic->likes_count }} begeni</div>
                            </a>
                        @empty
                            <div class="py-8 text-sm font-bold text-slate-500">Begenilen konunuz yok.</div>
                        @endforelse
                    </div>

                    <div class="mt-5">{{ $likedTopics->links() }}</div>
                </div>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-950 text-xl font-black text-white">
                        {{ str($user->name)->substr(0, 1)->upper() }}
                    </div>

                    <div>
                        <div class="font-black text-slate-950">{{ $user->name }}</div>
                        <div class="text-xs font-bold text-slate-500">{{ $user->forum_reputation ?? 0 }} reputation puani</div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="text-xs font-black uppercase text-slate-400">Rozetlerim</div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @forelse($user->forumBadges as $badge)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">{{ $badge->name }}</span>
                        @empty
                            <span class="text-sm font-bold text-slate-500">Henuz rozet kazanilmadi.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black text-slate-950">Forum Istatistiklerim</h2>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-2xl font-black text-slate-950">{{ $stats['received_likes'] }}</div>
                        <div class="mt-1 text-xs font-bold text-slate-500">Alinan begeni</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-2xl font-black text-slate-950">{{ number_format($stats['views']) }}</div>
                        <div class="mt-1 text-xs font-bold text-slate-500">Goruntulenme</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-2xl font-black text-slate-950">{{ $stats['pending_topics'] + $stats['pending_posts'] }}</div>
                        <div class="mt-1 text-xs font-bold text-slate-500">Bekleyen</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-2xl font-black text-slate-950">{{ $stats['published_topics'] + $stats['approved_posts'] }}</div>
                        <div class="mt-1 text-xs font-bold text-slate-500">Onaylanan</div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-6 shadow-sm">
                <h2 class="text-xl font-black text-yellow-900">Bekleyen Gonderilerim</h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <div class="mb-2 text-xs font-black uppercase text-yellow-700/70">Konular</div>
                        @forelse($pendingTopics as $topic)
                            <div class="border-b border-yellow-100 py-2 last:border-b-0">
                                <div class="text-sm font-black text-yellow-950">{{ $topic->title }}</div>
                                <div class="mt-1 text-xs font-bold text-yellow-800/70">{{ $topic->created_at?->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="text-sm font-bold text-yellow-800/70">Bekleyen konu yok.</div>
                        @endforelse
                    </div>

                    <div>
                        <div class="mb-2 text-xs font-black uppercase text-yellow-700/70">Cevaplar</div>
                        @forelse($pendingPosts as $post)
                            <div class="border-b border-yellow-100 py-2 last:border-b-0">
                                <div class="text-sm font-black text-yellow-950">{{ $post->topic?->title ?? 'Silinmis konu' }}</div>
                                <div class="mt-1 text-xs font-bold text-yellow-800/70">{{ $post->created_at?->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="text-sm font-bold text-yellow-800/70">Bekleyen cevap yok.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-green-200 bg-green-50 p-6 shadow-sm">
                <h2 class="text-xl font-black text-green-900">Onaylanan Gonderilerim</h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <div class="mb-2 text-xs font-black uppercase text-green-700/70">Konular</div>
                        @forelse($approvedTopics as $topic)
                            <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block border-b border-green-100 py-2 last:border-b-0">
                                <div class="text-sm font-black text-green-950">{{ $topic->title }}</div>
                                <div class="mt-1 text-xs font-bold text-green-800/70">{{ $topic->created_at?->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div class="text-sm font-bold text-green-800/70">Onaylanan konu yok.</div>
                        @endforelse
                    </div>

                    <div>
                        <div class="mb-2 text-xs font-black uppercase text-green-700/70">Cevaplar</div>
                        @forelse($approvedPosts as $post)
                            <a href="{{ $post->topic?->status === 'published' ? route('forum.topics.show', $post->topic->slug) : '#' }}" class="block border-b border-green-100 py-2 last:border-b-0">
                                <div class="text-sm font-black text-green-950">{{ $post->topic?->title ?? 'Silinmis konu' }}</div>
                                <div class="mt-1 text-xs font-bold text-green-800/70">{{ $post->created_at?->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div class="text-sm font-bold text-green-800/70">Onaylanan cevap yok.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </aside>
    </div>
</section>

@endsection
