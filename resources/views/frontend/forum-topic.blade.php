@extends('frontend.layout')

@section('title', $topic->title . ' | Forum')

@section('meta_description', str($topic->content)->stripTags()->limit(155))

@section('meta_keywords', 'forum, ' . ($topic->category?->name ?? 'topluluk'))

@section('canonical', route('forum.topics.show', $topic->slug))

@section('og_type', 'article')

@section('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'DiscussionForumPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('forum.topics.show', $topic->slug),
            ],
            'headline' => $topic->title,
            'description' => (string) str($topic->content)->stripTags()->limit(180),
            'text' => \App\Support\ForumContent::plainText($topic->content),
            'url' => route('forum.topics.show', $topic->slug),
            'datePublished' => $topic->created_at?->toIso8601String(),
            'dateModified' => $topic->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $topic->user?->name ?? 'ilanhaber.net',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteSetting?->site_name ?? 'ilanhaber.net',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('favicon.png'),
                ],
            ],
            'articleSection' => $topic->category?->name,
            'keywords' => $topic->tags->pluck('name')->push($topic->category?->name)->filter()->implode(', '),
            'interactionStatistic' => [
                [
                    '@type' => 'InteractionCounter',
                    'interactionType' => ['@type' => 'ViewAction'],
                    'userInteractionCount' => (int) $topic->views,
                ],
                [
                    '@type' => 'InteractionCounter',
                    'interactionType' => ['@type' => 'LikeAction'],
                    'userInteractionCount' => (int) $topic->likes_count,
                ],
                [
                    '@type' => 'InteractionCounter',
                    'interactionType' => ['@type' => 'CommentAction'],
                    'userInteractionCount' => (int) $topic->approvedPosts->count(),
                ],
            ],
            'commentCount' => (int) $topic->approvedPosts->count(),
            'comment' => $topic->approvedPosts
                ->take(10)
                ->map(fn ($post) => [
                    '@type' => 'Comment',
                    'text' => \App\Support\ForumContent::plainText($post->content),
                    'datePublished' => $post->created_at?->toIso8601String(),
                    'author' => [
                        '@type' => 'Person',
                        'name' => $post->user?->name ?? 'ilanhaber.net',
                    ],
                ])
                ->values()
                ->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Anasayfa',
                    'item' => url('/'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Forum',
                    'item' => route('forum.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $topic->category?->name ?? 'Kategori',
                    'item' => $topic->category ? route('forum.categories.show', $topic->category->slug) : route('forum.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 4,
                    'name' => $topic->title,
                    'item' => route('forum.topics.show', $topic->slug),
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')

@php
    $forumMediaItems = collect();

    $topic->mediaAssets->each(function ($media) use ($forumMediaItems, $topic) {
        if ($media->url) {
            $forumMediaItems->push([
                'id' => 'topic-' . $media->id,
                'src' => $media->url,
                'thumb' => $media->thumbnail_url ?? $media->url,
                'label' => 'Konu medyasi',
                'meta' => trim(($topic->user?->name ?? 'Sistem') . ' - ' . ($topic->created_at?->format('d.m.Y H:i') ?? '')),
                'width' => $media->width,
                'height' => $media->height,
            ]);
        }
    });

    $topic->approvedPosts->each(function ($post) use ($forumMediaItems) {
        $post->mediaAssets->each(function ($media) use ($forumMediaItems, $post) {
            if ($media->url) {
                $forumMediaItems->push([
                    'id' => 'post-' . $media->id,
                    'src' => $media->url,
                    'thumb' => $media->thumbnail_url ?? $media->url,
                    'label' => 'Cevap medyasi',
                    'meta' => trim(($post->user?->name ?? 'Sistem') . ' - ' . ($post->created_at?->format('d.m.Y H:i') ?? '')),
                    'width' => $media->width,
                    'height' => $media->height,
                ]);
            }
        });
    });
@endphp

<style>
    .forum-rich-content iframe {
        aspect-ratio: 16 / 9;
        border: 0;
        border-radius: 0.75rem;
        display: block;
        max-width: 100%;
        width: 100%;
    }

    .forum-rich-content img {
        border-radius: 0.75rem;
        height: auto;
        max-width: 100%;
    }

    .forum-media-masonry {
        columns: 1;
        column-gap: 0.75rem;
    }

    @media (min-width: 640px) {
        .forum-media-masonry {
            columns: 2;
        }
    }

    @media (min-width: 1024px) {
        .forum-media-masonry {
            columns: 3;
        }
    }

    .forum-media-tile {
        break-inside: avoid;
        display: block;
        margin-bottom: 0.75rem;
    }

    .forum-media-skeleton {
        background:
            linear-gradient(90deg, rgba(226, 232, 240, 0.7), rgba(248, 250, 252, 0.95), rgba(226, 232, 240, 0.7));
        background-size: 220% 100%;
        animation: forum-media-shimmer 1.4s ease-in-out infinite;
    }

    .forum-media-tile img {
        filter: blur(12px);
        opacity: 0;
        transform: scale(1.02);
        transition: filter 220ms ease, opacity 220ms ease, transform 220ms ease;
    }

    .forum-media-tile img.is-loaded {
        filter: blur(0);
        opacity: 1;
        transform: scale(1);
    }

    @keyframes forum-media-shimmer {
        0% { background-position: 120% 0; }
        100% { background-position: -120% 0; }
    }

    [x-cloak] {
        display: none !important;
    }
</style>

<section class="bg-slate-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-10">
        <a href="{{ route('forum.index') }}" class="text-sm font-bold text-red-200 hover:text-white">
            Forum
        </a>
        @if($topic->category)
            <span class="mx-2 text-sm text-slate-500">/</span>
            <a href="{{ route('forum.categories.show', $topic->category->slug) }}" class="text-sm font-bold text-red-200 hover:text-white">
                {{ $topic->category->name }}
            </a>
        @endif

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

            @if($topic->replies_closed)
                <span class="rounded-full bg-orange-500/20 px-3 py-1 text-xs font-black text-orange-100">
                    Cevap Kapali
                </span>
            @endif

            @if($topic->slow_mode_seconds > 0)
                <span class="rounded-full bg-blue-500/20 px-3 py-1 text-xs font-black text-blue-100">
                    Yavas Mod
                </span>
            @endif
        </div>

        <h1 class="mt-4 max-w-4xl text-3xl font-black leading-tight md:text-5xl">
            {{ $topic->title }}
        </h1>

        <p class="mt-4 text-sm text-slate-300">
            {{ $topic->user?->name ?? 'Sistem' }} tarafindan {{ $topic->created_at?->diffForHumans() }} acildi
        </p>

        @if($topic->tags->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach($topic->tags as $tag)
                    <a href="{{ route('forum.tags.show', $tag->slug) }}" class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-slate-100 transition hover:bg-white/20">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-6 flex flex-wrap gap-4 text-sm font-bold text-slate-300">
            <span>{{ number_format($topic->views) }} görüntülenme</span>
            <span>{{ $topic->likes_count }} beğeni</span>
            <span>{{ $topic->bookmarks_count }} favori</span>
            <span>{{ $topic->approvedPosts->count() }} cevap</span>
            <span>Son cevaplayan: {{ $topic->lastPostUser?->name ?? $topic->user?->name ?? 'Sistem' }}</span>
        </div>

        <div
            x-data="topicPresence({
                usersUrl: '{{ route('forum.topics.presence', $topic) }}',
                touchUrl: '{{ route('forum.topics.presence.touch', $topic) }}',
                channel: 'forum.topic.{{ $topic->id }}',
                csrf: '{{ csrf_token() }}',
                canTouch: @js(auth()->check()),
            })"
            x-init="init()"
            class="mt-6 rounded-xl border border-white/10 bg-white/5 p-4"
        >
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-black text-white">Su an konuda aktif</div>
                    <div class="mt-1 text-xs font-bold text-slate-300" x-text="users.length ? users.length + ' kullanici aktif' : 'Aktif kullanici bekleniyor'"></div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <template x-for="user in users" :key="user.id">
                        <a :href="user.profile_url || '#'" class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-white transition hover:bg-white/20" x-text="user.name"></a>
                    </template>
                </div>
            </div>
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

                @if($topic->user_id !== auth()->id())
                    <details class="rounded-lg border border-white/20 px-4 py-2 text-sm font-black text-white">
                        <summary class="cursor-pointer list-none">Raporla</summary>
                        <form method="POST" action="{{ route('reports.forum-topics.store', $topic) }}" class="mt-3 grid gap-3 text-slate-950 sm:w-80">
                            @csrf
                            <select name="reason" required class="rounded-lg border-slate-300 text-sm">
                                <option value="">Sebep secin</option>
                                <option value="spam">Spam</option>
                                <option value="insult">Hakaret</option>
                                <option value="inappropriate">Uygunsuz icerik</option>
                                <option value="misinformation">Yanlis bilgi</option>
                                <option value="advertising">Reklam</option>
                                <option value="other">Diger</option>
                            </select>
                            <textarea name="details" rows="3" maxlength="1000" class="rounded-lg border-slate-300 text-sm" placeholder="Ek aciklama"></textarea>
                            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white transition hover:bg-red-700">
                                Rapor Gonder
                            </button>
                        </form>
                    </details>
                @endif
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
                    @if($topic->user)
                        <a href="{{ url('/profil/' . $topic->user->id) }}" class="font-black text-slate-950 transition hover:text-red-700">{{ $topic->user->name }}</a>
                    @else
                        <div class="font-black text-slate-950">Sistem</div>
                    @endif
                    <div class="text-xs font-bold text-slate-500">Konu sahibi</div>
                </div>
            </div>

            <div class="mt-4 space-y-2 text-xs font-bold text-slate-500">
                <div class="{{ $topic->user?->isOnline() ? 'text-green-600' : 'text-slate-400' }}">
                    {{ $topic->user?->isOnline() ? 'Online' : 'Offline' }}
                </div>
                <div>İtibar: {{ $topic->user?->forum_reputation ?? 0 }}</div>
                <div>Seviye: {{ $topic->user?->forum_level ?? 1 }} / {{ number_format($topic->user?->forum_xp ?? 0) }} XP</div>
                @if($topic->user)
                    <div>Takipci: {{ $topic->user->followers()->count() }}</div>
                @endif
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
            <div class="forum-rich-content prose max-w-none text-slate-700">
                {!! \App\Support\ForumContent::sanitize($topic->content) !!}
            </div>

            @if($topic->mediaAssets->isNotEmpty())
                <div class="mt-6 border-t border-slate-100 pt-5">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                        <div class="text-xs font-black uppercase text-slate-400">Konu medya galerisi</div>
                        <div class="text-xs font-bold text-slate-400">{{ $topic->mediaAssets->count() }} gorsel</div>
                    </div>
                    <div class="forum-media-masonry">
                        @foreach($topic->mediaAssets as $media)
                            @php($mediaIndex = $forumMediaItems->search(fn ($item) => $item['id'] === 'topic-' . $media->id))
                            @if($mediaIndex !== false)
                                <button
                                    type="button"
                                    data-forum-media-index="{{ $mediaIndex }}"
                                    class="forum-media-tile group w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50 text-left shadow-sm transition hover:border-red-200 hover:shadow-md"
                                >
                                    <span class="forum-media-skeleton block overflow-hidden">
                                        <img
                                            src="{{ $media->thumbnail_url ?? $media->url }}"
                                            alt=""
                                            loading="lazy"
                                            decoding="async"
                                            width="{{ $media->width ?? 800 }}"
                                            height="{{ $media->height ?? 600 }}"
                                            class="max-h-72 w-full object-cover transition group-hover:scale-[1.02]"
                                            onload="this.classList.add('is-loaded')"
                                        >
                                    </span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
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
                                @if($post->user)
                                    <a href="{{ url('/profil/' . $post->user->id) }}" class="font-black text-slate-950 transition hover:text-red-700">{{ $post->user->name }}</a>
                                @else
                                    <div class="font-black text-slate-950">Sistem</div>
                                @endif
                                <div class="text-xs font-bold text-slate-500">Yanıtlayan</div>
                            </div>
                        </div>

                        <div class="mt-4 text-xs font-bold text-slate-500">
                            <div class="{{ $post->user?->isOnline() ? 'text-green-600' : 'text-slate-400' }}">
                                {{ $post->user?->isOnline() ? 'Online' : 'Offline' }}
                            </div>
                            <div class="mt-1">İtibar: {{ $post->user?->forum_reputation ?? 0 }}</div>
                            <div class="mt-1">Seviye {{ $post->user?->forum_level ?? 1 }} · {{ number_format($post->user?->forum_xp ?? 0) }} XP</div>
                            {{ $post->created_at?->diffForHumans() }}
                        </div>
                    </aside>

                    <div>
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                            <div class="text-xs font-black uppercase text-slate-400">Cevap</div>
                            <div class="text-xs font-bold text-slate-400">{{ $post->created_at?->format('d.m.Y H:i') }}</div>
                        </div>

                        @if($post->parent)
                            <div class="mb-3 rounded-xl border border-blue-100 bg-blue-50 p-3 text-xs font-bold text-blue-800">
                                {{ $post->parent->user?->name ?? 'Sistem' }} mesajina cevap
                            </div>
                        @endif

                        @if($post->quotedPost)
                            <blockquote class="mb-4 rounded-xl border-l-4 border-red-500 bg-red-50 p-4">
                                <div class="text-xs font-black uppercase text-red-700">
                                    {{ $post->quotedPost->user?->name ?? 'Sistem' }} alintisi
                                </div>
                                <p class="mt-2 text-sm leading-6 text-slate-700">
                                    {{ \Illuminate\Support\Str::limit(\App\Support\ForumContent::plainText($post->quotedPost->content), 300) }}
                                </p>
                            </blockquote>
                        @endif

                        <div class="forum-rich-content prose max-w-none text-sm leading-7 text-slate-700">
                            {!! \App\Support\ForumContent::sanitize($post->content) !!}
                        </div>

                        @if($post->mediaAssets->isNotEmpty())
                            <div class="mt-5 border-t border-slate-100 pt-4">
                                <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
                                    <div class="text-xs font-black uppercase text-slate-400">Cevap medyasi</div>
                                    <div class="text-xs font-bold text-slate-400">{{ $post->mediaAssets->count() }} gorsel</div>
                                </div>
                                <div class="forum-media-masonry">
                                    @foreach($post->mediaAssets as $media)
                                        @php($mediaIndex = $forumMediaItems->search(fn ($item) => $item['id'] === 'post-' . $media->id))
                                        @if($mediaIndex !== false)
                                            <button
                                                type="button"
                                                data-forum-media-index="{{ $mediaIndex }}"
                                                class="forum-media-tile group w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-50 text-left shadow-sm transition hover:border-red-200 hover:shadow-md"
                                            >
                                                <span class="forum-media-skeleton block overflow-hidden">
                                                    <img
                                                        src="{{ $media->thumbnail_url ?? $media->url }}"
                                                        alt=""
                                                        loading="lazy"
                                                        decoding="async"
                                                        width="{{ $media->width ?? 800 }}"
                                                        height="{{ $media->height ?? 600 }}"
                                                        class="max-h-64 w-full object-cover transition group-hover:scale-[1.02]"
                                                        onload="this.classList.add('is-loaded')"
                                                    >
                                                </span>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @auth
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    data-reply-post="{{ $post->id }}"
                                    data-reply-user="{{ e($post->user?->name ?? 'Sistem') }}"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                                >
                                    Cevapla
                                </button>
                                <button
                                    type="button"
                                    data-quote-post="{{ $post->id }}"
                                    data-quote-user="{{ e($post->user?->name ?? 'Sistem') }}"
                                    data-quote-content="{{ e(\Illuminate\Support\Str::limit(\App\Support\ForumContent::plainText($post->content), 240)) }}"
                                    class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                                >
                                    Alintila
                                </button>
                                @if($post->user_id !== auth()->id())
                                    <details class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700">
                                        <summary class="cursor-pointer list-none">Raporla</summary>
                                        <form method="POST" action="{{ route('reports.forum-posts.store', $post) }}" class="mt-3 grid gap-3 sm:w-72">
                                            @csrf
                                            <select name="reason" required class="rounded-lg border-slate-300 text-sm">
                                                <option value="">Sebep secin</option>
                                                <option value="spam">Spam</option>
                                                <option value="insult">Hakaret</option>
                                                <option value="inappropriate">Uygunsuz icerik</option>
                                                <option value="misinformation">Yanlis bilgi</option>
                                                <option value="advertising">Reklam</option>
                                                <option value="other">Diger</option>
                                            </select>
                                            <textarea name="details" rows="3" maxlength="1000" class="rounded-lg border-slate-300 text-sm" placeholder="Ek aciklama"></textarea>
                                            <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white transition hover:bg-red-700">
                                                Gonder
                                            </button>
                                        </form>
                                    </details>
                                @endif
                            </div>
                        @endauth
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
        @if($siteSetting?->forum_enabled && $topic->acceptsReplies())
            @auth
                <div class="mb-5">
                    <h2 class="text-2xl font-black text-slate-950">Cevap Yaz</h2>
                    @if($topic->slow_mode_seconds > 0)
                        <p class="mt-2 rounded-xl border border-blue-100 bg-blue-50 p-3 text-xs font-bold text-blue-800">
                            Bu konuda yavas mod aktif: {{ $topic->slow_mode_seconds }} saniyede bir cevap yazilabilir.
                        </p>
                    @endif
                    <p class="mt-1 text-sm text-slate-600">Cevabınız moderatör onayından sonra yayınlanır.</p>
                </div>

                <form
                    id="forum-reply-form"
                    method="POST"
                    action="{{ route('forum.posts.store', $topic) }}"
                    class="space-y-4"
                    x-data="forumAssistant({ type: 'reply', topicId: {{ $topic->id }}, url: '{{ route('forum.assistant') }}', csrf: '{{ csrf_token() }}' })"
                >
                    @csrf

                    <input type="hidden" name="parent_id" id="forum-reply-parent-id" value="{{ old('parent_id') }}">
                    <input type="hidden" name="quoted_post_id" id="forum-reply-quoted-post-id" value="{{ old('quoted_post_id') }}">

                    <div id="forum-reply-context" class="hidden rounded-xl border border-red-100 bg-red-50 p-4 text-sm text-red-900">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <div class="text-xs font-black uppercase text-red-700" id="forum-reply-context-label">Cevap</div>
                                <div class="mt-1 font-bold" id="forum-reply-context-user"></div>
                            </div>
                            <button type="button" id="forum-reply-context-clear" class="rounded-lg bg-white px-3 py-2 text-xs font-black text-red-700 transition hover:bg-red-100">
                                Temizle
                            </button>
                        </div>
                        <p class="mt-2 hidden text-sm leading-6 text-red-800" id="forum-reply-context-text"></p>
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Cevap</label>
                        <div class="mt-2">
                            @include('frontend.partials.forum-rich-editor', [
                                'id' => 'forum-post-editor',
                                'name' => 'content',
                                'value' => old('content'),
                                'placeholder' => 'Cevabinizi yazin...',
                            ])
                        </div>
                        @error('content')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @include('frontend.partials.forum-ai-assistant-panel')

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
        @elseif($topic->replies_closed)
            <div class="rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm font-bold text-orange-800">
                Bu konuda yeni cevaplar moderasyon tarafindan kapatildi.
            </div>
        @else
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm font-bold text-slate-700">
                Forum şu anda panelden kapalı.
            </div>
        @endif
    </div>
</section>

@if($forumMediaItems->isNotEmpty())
    <div
        x-data="forumMediaViewer(@js($forumMediaItems->values()))"
        x-init="init()"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-[10000] bg-slate-950/95 text-white"
        @click.self="close()"
        @touchstart.passive="touchStart($event)"
        @touchend.passive="touchEnd($event)"
    >
        <div class="absolute inset-x-0 top-0 z-10 flex items-center justify-between gap-3 bg-gradient-to-b from-slate-950/90 to-transparent px-4 py-4">
            <div class="min-w-0">
                <div class="truncate text-sm font-black" x-text="current.label"></div>
                <div class="mt-1 truncate text-xs font-bold text-slate-300" x-text="current.meta"></div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="zoomOut()" class="rounded-lg bg-white/10 px-3 py-2 text-sm font-black transition hover:bg-white/20">-</button>
                <button type="button" @click="resetZoom()" class="rounded-lg bg-white/10 px-3 py-2 text-xs font-black transition hover:bg-white/20" x-text="Math.round(zoom * 100) + '%'"></button>
                <button type="button" @click="zoomIn()" class="rounded-lg bg-white/10 px-3 py-2 text-sm font-black transition hover:bg-white/20">+</button>
                <button type="button" @click="close()" class="rounded-lg bg-white px-3 py-2 text-sm font-black text-slate-950 transition hover:bg-red-50">Kapat</button>
            </div>
        </div>

        <button
            type="button"
            @click="prev()"
            class="absolute left-3 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl font-black transition hover:bg-white/20"
            x-show="items.length > 1"
        >‹</button>

        <button
            type="button"
            @click="next()"
            class="absolute right-3 top-1/2 z-10 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-2xl font-black transition hover:bg-white/20"
            x-show="items.length > 1"
        >›</button>

        <div class="flex h-full items-center justify-center px-4 py-20">
            <div class="forum-media-skeleton max-h-full max-w-full overflow-hidden rounded-xl bg-slate-900">
                <img
                    :src="current.src"
                    alt=""
                    class="max-h-[78vh] max-w-[92vw] object-contain transition duration-200"
                    :style="`transform: scale(${zoom}); cursor: ${zoom > 1 ? 'zoom-out' : 'zoom-in'};`"
                    @click="toggleZoom()"
                    @load="$event.target.classList.add('is-loaded')"
                >
            </div>
        </div>

        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/95 to-transparent px-4 pb-4 pt-10">
            <div class="mx-auto flex max-w-4xl items-center justify-between gap-4">
                <div class="text-xs font-black text-slate-300">
                    <span x-text="index + 1"></span>/<span x-text="items.length"></span>
                </div>
                <div class="flex min-w-0 flex-1 gap-2 overflow-x-auto">
                    <template x-for="(item, itemIndex) in items" :key="item.id">
                        <button
                            type="button"
                            @click="go(itemIndex)"
                            class="h-14 w-16 shrink-0 overflow-hidden rounded border transition"
                            :class="itemIndex === index ? 'border-white' : 'border-white/20 opacity-60 hover:opacity-100'"
                        >
                            <img :src="item.thumb" alt="" loading="lazy" class="h-full w-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
@endif

@auth
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('forum-reply-form');

            if (! form) {
                return;
            }

            const parentInput = document.getElementById('forum-reply-parent-id');
            const quotedInput = document.getElementById('forum-reply-quoted-post-id');
            const context = document.getElementById('forum-reply-context');
            const contextLabel = document.getElementById('forum-reply-context-label');
            const contextUser = document.getElementById('forum-reply-context-user');
            const contextText = document.getElementById('forum-reply-context-text');
            const contextClear = document.getElementById('forum-reply-context-clear');
            const editorWrapper = form.querySelector('.forum-rich-editor');

            const escapeHtml = (value) => String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const scrollToForm = () => {
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };

            const showContext = (label, user, text = '') => {
                contextLabel.textContent = label;
                contextUser.textContent = user;

                if (text) {
                    contextText.textContent = text;
                    contextText.classList.remove('hidden');
                } else {
                    contextText.textContent = '';
                    contextText.classList.add('hidden');
                }

                context.classList.remove('hidden');
            };

            document.querySelectorAll('[data-reply-post]').forEach((button) => {
                button.addEventListener('click', () => {
                    parentInput.value = button.dataset.replyPost;
                    quotedInput.value = '';
                    showContext('Cevap yaziliyor', `${button.dataset.replyUser} mesajina cevap`);
                    scrollToForm();
                });
            });

            document.querySelectorAll('[data-quote-post]').forEach((button) => {
                button.addEventListener('click', () => {
                    parentInput.value = button.dataset.quotePost;
                    quotedInput.value = button.dataset.quotePost;
                    showContext('Alinti yapiliyor', `${button.dataset.quoteUser} alintisi`, button.dataset.quoteContent);

                    editorWrapper?.dispatchEvent(new CustomEvent('forum-editor:append-html', {
                        detail: {
                            html: `<blockquote><p>${escapeHtml(button.dataset.quoteContent || '')}</p></blockquote><p></p>`,
                        },
                    }));

                    scrollToForm();
                });
            });

            contextClear?.addEventListener('click', () => {
                parentInput.value = '';
                quotedInput.value = '';
                context.classList.add('hidden');
                contextText.textContent = '';
                contextText.classList.add('hidden');
            });
        });
    </script>
@endauth

<script>
function forumMediaViewer(items) {
    return {
        items,
        open: false,
        index: 0,
        zoom: 1,
        touchX: null,
        get current() {
            return this.items[this.index] || {};
        },
        init() {
            document.querySelectorAll('[data-forum-media-index]').forEach((button) => {
                button.addEventListener('click', () => this.show(Number(button.dataset.forumMediaIndex || 0)));
            });

            window.addEventListener('keydown', (event) => {
                if (! this.open) {
                    return;
                }

                if (event.key === 'Escape') {
                    this.close();
                } else if (event.key === 'ArrowRight') {
                    this.next();
                } else if (event.key === 'ArrowLeft') {
                    this.prev();
                }
            });
        },
        show(index) {
            this.index = Math.max(0, Math.min(index, this.items.length - 1));
            this.zoom = 1;
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.open = false;
            this.zoom = 1;
            document.body.style.overflow = '';
        },
        go(index) {
            this.index = Math.max(0, Math.min(index, this.items.length - 1));
            this.zoom = 1;
        },
        next() {
            this.go((this.index + 1) % this.items.length);
        },
        prev() {
            this.go((this.index - 1 + this.items.length) % this.items.length);
        },
        zoomIn() {
            this.zoom = Math.min(3, Number((this.zoom + 0.25).toFixed(2)));
        },
        zoomOut() {
            this.zoom = Math.max(1, Number((this.zoom - 0.25).toFixed(2)));
        },
        resetZoom() {
            this.zoom = 1;
        },
        toggleZoom() {
            this.zoom = this.zoom > 1 ? 1 : 2;
        },
        touchStart(event) {
            this.touchX = event.changedTouches?.[0]?.clientX ?? null;
        },
        touchEnd(event) {
            if (this.touchX === null) {
                return;
            }

            const delta = (event.changedTouches?.[0]?.clientX ?? this.touchX) - this.touchX;
            this.touchX = null;

            if (Math.abs(delta) < 45 || this.items.length < 2) {
                return;
            }

            delta < 0 ? this.next() : this.prev();
        },
    };
}

function topicPresence(config) {
    return {
        users: [],
        init() {
            this.touch();
            this.fetchUsers();
            this.listen();
            setInterval(() => this.touch(), 45000);
            setInterval(() => this.fetchUsers(), 15000);
        },
        listen() {
            if (!window.Echo || !config.canTouch) {
                return;
            }

            window.Echo.join(config.channel)
                .here((users) => {
                    this.users = users;
                })
                .joining((user) => {
                    if (!this.users.some((item) => item.id === user.id)) {
                        this.users = [...this.users, user];
                    }
                })
                .leaving((user) => {
                    this.users = this.users.filter((item) => item.id !== user.id);
                });
        },
        touch() {
            if (!config.canTouch) {
                return;
            }

            fetch(config.touchUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': config.csrf,
                },
            }).catch(() => {});
        },
        fetchUsers() {
            fetch(config.usersUrl)
                .then(response => response.json())
                .then(data => {
                    this.users = data.users || [];
                })
                .catch(() => {});
        },
    }
}
</script>

@endsection
