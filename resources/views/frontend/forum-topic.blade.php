@extends('frontend.layout')

@section('title', $topic->title . ' | Forum')

@section('meta_description', str($topic->content)->stripTags()->limit(155))

@section('meta_keywords', 'forum, ' . ($topic->category?->name ?? 'topluluk'))

@section('canonical', route('forum.topics.show', $topic->slug))

@section('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'DiscussionForumPosting',
            'headline' => $topic->title,
            'text' => strip_tags($topic->content),
            'url' => route('forum.topics.show', $topic->slug),
            'datePublished' => $topic->created_at?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $topic->user?->name ?? 'ilanhaber.net',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')

<section class="bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <a href="{{ route('forum.index') }}" class="text-sm font-bold text-red-200 hover:text-white">
            Forum
        </a>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-slate-200">
                {{ $topic->category?->name }}
            </span>

            @if($topic->is_pinned)
                <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-black text-white">
                    Sabit
                </span>
            @endif

            @if($topic->is_solved)
                <span class="rounded-full bg-green-500/20 px-3 py-1 text-xs font-black text-green-100">
                    Çözüldü
                </span>
            @endif

            @if($topic->is_locked)
                <span class="rounded-full bg-yellow-500/20 px-3 py-1 text-xs font-black text-yellow-100">
                    Kilitli
                </span>
            @endif
        </div>

        <h1 class="mt-4 max-w-4xl text-3xl font-black leading-tight md:text-5xl">
            {{ $topic->title }}
        </h1>

        <p class="mt-4 text-sm text-slate-300">
            {{ $topic->user?->name ?? 'Sistem' }} tarafindan {{ $topic->created_at?->diffForHumans() }} acildi
        </p>

        <div class="mt-6 flex flex-wrap gap-4 text-sm font-bold text-slate-300">
            <span>{{ number_format($topic->views) }} görüntülenme</span>
            <span>{{ $topic->likes_count }} beğeni</span>
            <span>{{ $topic->bookmarks_count }} favori</span>
            <span>{{ $topic->approvedPosts->count() }} cevap</span>
            <span>Son cevaplayan: {{ $topic->lastPostUser?->name ?? $topic->user?->name ?? 'Sistem' }}</span>
        </div>

        @auth
            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('forum.topics.like', $topic) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-white px-4 py-2 text-sm font-black text-slate-950 transition hover:bg-red-50">
                        {{ $topic->likedBy(auth()->user()) ? 'Beğenildi' : 'Beğen' }}
                    </button>
                </form>

                <form method="POST" action="{{ route('forum.topics.bookmark', $topic) }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-white/20 px-4 py-2 text-sm font-black text-white transition hover:bg-white/10">
                        {{ $topic->bookmarkedBy(auth()->user()) ? 'Favoride' : 'Favoriye Al' }}
                    </button>
                </form>
            </div>
        @endauth
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10">
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

    <article class="grid gap-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:grid-cols-[220px_1fr]">
        <aside class="rounded-xl bg-slate-50 p-4">
            <div class="flex items-center gap-3 lg:block">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-950 text-lg font-black text-white">
                    {{ str($topic->user?->name ?? 'S')->substr(0, 1)->upper() }}
                </div>

                <div class="lg:mt-3">
                    <div class="font-black text-slate-950">{{ $topic->user?->name ?? 'Sistem' }}</div>
                    <div class="text-xs font-bold text-slate-500">Konu sahibi</div>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-xs font-bold text-slate-500">
                <div class="{{ $topic->user?->isOnline() ? 'text-green-600' : 'text-slate-400' }}">
                    {{ $topic->user?->isOnline() ? 'Online' : 'Offline' }}
                </div>
                <div>İtibar: {{ $topic->user?->forum_reputation ?? 0 }}</div>
                <div>Katılım: {{ $topic->user?->created_at?->format('d.m.Y') ?? '-' }}</div>
                <div>Konu tarihi: {{ $topic->created_at?->format('d.m.Y H:i') }}</div>
            </div>

            @if($topic->user?->forumBadges?->count())
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($topic->user->forumBadges as $badge)
                        <span class="rounded-full bg-white px-2.5 py-1 text-[11px] font-black text-slate-700">{{ $badge->name }}</span>
                    @endforeach
                </div>
            @endif
        </aside>

        <div>
            <div class="prose max-w-none text-slate-700">
                {!! nl2br(e($topic->content)) !!}
            </div>
        </div>
    </article>

    <div class="mt-8">
        <h2 class="mb-4 text-2xl font-black text-slate-950">
            Cevaplar
        </h2>

        <div class="space-y-4">
            @forelse($topic->approvedPosts as $post)
                <article class="grid gap-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-[200px_1fr]">
                    <aside class="rounded-xl bg-slate-50 p-4">
                        <div class="flex items-center gap-3 lg:block">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-600 text-sm font-black text-white">
                                {{ str($post->user?->name ?? 'S')->substr(0, 1)->upper() }}
                            </div>

                            <div class="lg:mt-3">
                                <div class="font-black text-slate-950">{{ $post->user?->name ?? 'Sistem' }}</div>
                                <div class="text-xs font-bold text-slate-500">Yanıtlayan</div>
                            </div>
                        </div>

                        <div class="mt-4 text-xs font-bold text-slate-500">
                            <div class="{{ $post->user?->isOnline() ? 'text-green-600' : 'text-slate-400' }}">
                                {{ $post->user?->isOnline() ? 'Online' : 'Offline' }}
                            </div>
                            <div class="mt-1">İtibar: {{ $post->user?->forum_reputation ?? 0 }}</div>
                            {{ $post->created_at?->diffForHumans() }}
                        </div>
                    </aside>

                    <div>
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-xs font-black uppercase text-slate-400">Cevap</div>
                            <div class="text-xs font-bold text-slate-400">{{ $post->created_at?->format('d.m.Y H:i') }}</div>
                        </div>

                        <div class="text-sm leading-7 text-slate-700">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                    <div class="text-lg font-black text-slate-950">Henuz cevap yok</div>
                    <p class="mt-2 text-sm text-slate-600">Cevap yazma sistemi sonraki adimda aktif edilecek.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @if($siteSetting?->forum_enabled && ! $topic->is_locked)
            @auth
                <div class="mb-5">
                    <h2 class="text-2xl font-black text-slate-950">Cevap Yaz</h2>
                    <p class="mt-1 text-sm text-slate-600">Cevabınız moderatör onayından sonra yayınlanır.</p>
                </div>

                <form method="POST" action="{{ route('forum.posts.store', $topic) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="text-sm font-black text-slate-700">Cevap</label>
                        <textarea
                            name="content"
                            rows="5"
                            required
                            class="mt-2 w-full rounded-lg border-slate-300 text-sm"
                            placeholder="Cevabınızı yazın..."
                        >{{ old('content') }}</textarea>
                        @error('content')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                        Moderasyona Gönder
                    </button>
                </form>
            @else
                <h2 class="text-xl font-black text-slate-950">Cevap yazmak için giriş yapın</h2>
                <p class="mt-2 text-sm text-slate-600">Forum cevapları yalnızca üyeler tarafından gönderilebilir.</p>
            @endauth
        @elseif($topic->is_locked)
            <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm font-bold text-yellow-800">
                Bu konu cevaplara kapalı.
            </div>
        @else
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm font-bold text-slate-700">
                Forum şu anda panelden kapalı.
            </div>
        @endif
    </div>
</section>

@endsection
