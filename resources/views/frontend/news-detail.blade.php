@extends('frontend.layout')

@section('title', $news->title . ' | ' . ($siteSetting?->site_name ?? config('app.name')))

@section(
    'meta_description',
    $news->summary
        ? Str::limit(strip_tags($news->summary), 160)
        : Str::limit(strip_tags($news->content), 160)
)

@section(
    'meta_keywords',
    $news->title . ', haber, son dakika, gündem'
)

@section('canonical', url()->current())
@section('og_type', 'article')
@section('author', $siteSetting?->site_name ?? config('app.name'))
@section('news_meta')
    <meta property="article:published_time" content="{{ $news->created_at?->toAtomString() }}">
    <meta property="article:modified_time" content="{{ $news->updated_at?->toAtomString() }}">
    <meta property="article:author" content="{{ $siteSetting?->site_name ?? config('app.name') }}">
    <meta name="news_keywords" content="{{ $news->title }}, haber, son dakika, gündem">
@endsection

@section(
    'meta_image',
    $news->image
        ? asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image))
        : asset('default-og.jpg')
)

@section('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $news->title,
    'description' => $news->summary ?: Str::limit(strip_tags($news->content), 160),
    'image' => $news->image
        ? asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image))
        : asset('default-og.jpg'),
    'datePublished' => $news->created_at->toAtomString(),
    'dateModified' => $news->updated_at->toAtomString(),
    'author' => [
        '@type' => 'Organization',
        'name' => $siteSetting?->site_name ?? config('app.name'),
    ],
    'publisher' => [
        '@type' => 'Organization',
        'name' => $siteSetting?->site_name ?? config('app.name'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => asset('favicon.png'),
        ],
    ],
    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => url()->current(),
    ],
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
            'name' => 'Haberler',
            'item' => url('/haberler'),
        ],

        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $news->category->name ?? 'Kategori',
            'item' => url('/kategori/' . ($news->category->slug ?? 'kategori')),
        ],

        [
            '@type' => 'ListItem',
            'position' => 4,
            'name' => $news->title,
            'item' => url('/haber/' . $news->slug),
        ],

    ],

], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endsection

@section('content')

@php
    $imagePath = $news->image
        ? (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)
        : null;

    $contentAttachmentImages = $news->contentAttachments
        ->filter(fn ($asset) => str_starts_with((string) $asset->mime_type, 'image/'))
        ->values();
@endphp

<section class="max-w-[1600px] mx-auto px-3 mt-4 md:px-4 md:mt-6">

    <div class="grid grid-cols-12 gap-6">

        {{-- SOL REKLAM --}}
        <aside class="hidden 2xl:block col-span-2">
            @if(isset($leftAd) && $leftAd?->image)
                <a href="{{ $leftAd->url }}" target="_blank">
                    <img src="{{ asset('storage/' . $leftAd->image) }}" class="w-full shadow">
                </a>
            @else
                <img src="https://dummyimage.com/300x600/cccccc/000000&text=SOL+REKLAM" class="w-full shadow">
            @endif
        </aside>

        {{-- ORTA DETAY --}}
        <div class="col-span-12 2xl:col-span-8">

            <div class="grid grid-cols-12 gap-6 items-start">

                {{-- HABER ANA ALAN --}}
                <main class="col-span-12 lg:col-span-9">

                    {{-- ÜST REKLAM --}}
                    @if(isset($topAd) && $topAd?->image)
                        <div class="premium-ad-slot mb-4 flex justify-center p-2 md:mb-6 md:p-3">
                            <a href="{{ $topAd->url }}" target="_blank">
                                <img src="{{ asset('storage/' . $topAd->image) }}" class="w-full max-w-[970px]">
                            </a>
                        </div>
                    @endif

                    @if($news->category || ($headlineNews ?? collect())->isNotEmpty())
                        <div class="theme-card premium-card mb-4 overflow-hidden md:mb-6">
                            @if($news->category)
                                <div class="border-b border-slate-100 p-4 md:p-5">
                                    <div class="flex flex-wrap gap-2">
                                        <a
                                            href="{{ url('/kategori/' . $news->category->slug) }}"
                                            class="inline-flex max-w-full items-center rounded-full bg-blue-700 px-4 py-2 text-sm font-black text-white transition hover:bg-blue-800"
                                        >
                                            <span class="truncate">{{ $news->category->name }}</span>
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if(($headlineNews ?? collect())->isNotEmpty())
                                <div class="p-4 md:p-5">
                                    <div class="mb-3 text-sm font-black uppercase tracking-wide text-slate-500">
                                        Haber Manşet
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-2">
                                        @foreach($headlineNews as $headlineItem)
                                            <a href="/haber/{{ $headlineItem->slug }}" class="group flex min-w-0 gap-3 rounded-2xl border border-slate-100 bg-white p-3 transition hover:border-blue-200 hover:bg-blue-50/60">
                                                <div class="h-16 w-20 shrink-0 overflow-hidden rounded-xl bg-slate-100">
                                                    @if($headlineItem->image)
                                                        <img
                                                            src="{{ asset('storage/' . (str_contains($headlineItem->image, '/') ? $headlineItem->image : 'news/' . $headlineItem->image)) }}"
                                                            alt="{{ $headlineItem->title }}"
                                                            class="h-full w-full object-cover transition group-hover:scale-105"
                                                        >
                                                    @endif
                                                </div>

                                                <div class="min-w-0">
                                                    <h2 class="line-clamp-2 text-sm font-black leading-5 text-slate-900 group-hover:text-blue-700">
                                                        {{ $headlineItem->title }}
                                                    </h2>
                                                    <p class="mt-1 text-xs font-bold text-slate-400">
                                                        {{ $headlineItem->created_at->format('d.m.Y') }}
                                                    </p>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- HABER --}}
                    <article class="theme-card premium-article">

                        @if($imagePath)

                            <div class="relative">

                                <img src="{{ asset('storage/' . $imagePath) }}" class="h-[320px] w-full object-cover md:h-[520px]">

                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                                <div class="absolute bottom-6 left-4 right-4 md:bottom-8 md:left-8 md:right-8">

                                    <div class="text-sm text-white/80 mb-3">
                                        <a href="/" class="hover:underline">Anasayfa</a>
                                        /
                                        <a href="/haberler" class="hover:underline">Haberler</a>
                                    </div>

                                    <h1 class="text-3xl font-black leading-tight text-white md:text-5xl">
                                        {{ $news->title }}
                                    </h1>

                                </div>

                            </div>

                        @else

                            <div class="border-b p-5 md:p-8">
                                <div class="text-sm text-slate-500 mb-3">
                                    <a href="/" class="hover:underline">Anasayfa</a>
                                    /
                                    <a href="/haberler" class="hover:underline">Haberler</a>
                                </div>

                                <h1 class="text-3xl font-black leading-tight md:text-5xl">
                                    {{ $news->title }}
                                </h1>
                            </div>

                        @endif

                        <div class="p-5 md:p-8">

                            {{-- META --}}
                            <div class="mb-5 flex flex-wrap items-center gap-3 border-b border-slate-100 pb-5 text-xs font-bold text-slate-500 md:mb-6 md:gap-4 md:text-sm">
                                <span>📅 {{ $news->created_at->format('d.m.Y H:i') }}</span>
                                <span>👁 {{ $news->views }} okunma</span>
                                <span>✍ Editör</span>
                            </div>

                            {{-- SOSYAL --}}
                            <div class="mb-6 flex gap-2 overflow-x-auto pb-1 text-sm md:flex-wrap md:gap-3 md:overflow-visible md:pb-0">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                   target="_blank"
                                   class="shrink-0 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                                    Facebook
                                </a>

                                <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $news->title }}"
                                   target="_blank"
                                   class="shrink-0 rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                                    X
                                </a>

                                <a href="https://api.whatsapp.com/send?text={{ $news->title }} {{ url()->current() }}"
                                   target="_blank"
                                   class="shrink-0 rounded bg-green-600 px-4 py-2 text-sm font-semibold text-white">
                                    WhatsApp
                                </a>
                            </div>

                            {{-- ÖZET --}}
                            @if($news->summary)
                                <div class="mb-6 rounded-2xl border border-blue-100 border-l-4 border-l-blue-600 bg-blue-50 p-4 text-lg font-semibold text-slate-800 md:mb-8 md:p-5 md:text-xl">
                                    {{ $news->summary }}
                                </div>
                            @endif

                            @if($contentAttachmentImages->isNotEmpty())
                                <div class="mb-6 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 p-3 md:mb-8 md:p-4">
                                    <div class="mb-3 text-sm font-black uppercase tracking-wide text-slate-500">
                                        Haber Görselleri
                                    </div>

                                    <div class="flex gap-3 overflow-x-auto pb-1">
                                        @foreach($contentAttachmentImages as $asset)
                                            <a
                                                href="{{ $asset->url }}"
                                                data-content-lightbox-image
                                                class="block shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 transition hover:border-blue-300 hover:shadow-sm"
                                            >
                                                <img
                                                    src="{{ $asset->thumbnail_url ?? $asset->url }}"
                                                    alt="{{ $asset->original_name ?? $news->title }}"
                                                    class="h-20 w-28 rounded-xl object-cover sm:h-24 sm:w-32 md:h-28 md:w-40"
                                                >
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- İÇERİK --}}
                            <div class="premium-reading prose max-w-none md:prose-lg">
                                {!! \App\Support\ContentHtml::render($news->content) !!}
                            </div>
                            {{-- ALT BUTONLAR --}}
                            <div class="mt-8 flex flex-col gap-4 border-t pt-6 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">

                                <a href="/haberler"
                                   class="rounded-full bg-slate-800 px-5 py-3 font-black text-white hover:bg-slate-700">
                                    ← Tüm Haberlere Dön
                                </a>

                                <div class="flex gap-2 overflow-x-auto pb-1 md:gap-3 md:overflow-visible md:pb-0">

                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                       target="_blank"
                                       class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold">
                                        Facebook
                                    </a>

                                    <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $news->title }}"
                                       target="_blank"
                                       class="bg-slate-900 text-white px-4 py-2 rounded text-sm font-semibold">
                                        X
                                    </a>

                                    <a href="https://api.whatsapp.com/send?text={{ $news->title }} {{ url()->current() }}"
                                       target="_blank"
                                       class="bg-green-600 text-white px-4 py-2 rounded text-sm font-semibold">
                                        WhatsApp
                                    </a>

                                </div>

                            </div>

                            {{-- YORUM SİSTEMİ --}}
                            @if($news->comments_enabled)

                                <div class="mt-12 border-t pt-10">

                                    <div class="flex items-center justify-between mb-8">

                                        <div>
                                            <h2 class="text-3xl font-black text-slate-900">
                                                Yorumlar
                                            </h2>

                                            <p class="text-slate-500 mt-2">
                                                Topluluk kurallarına uygun yorum yapınız.
                                            </p>
                                        </div>

                                        <div class="bg-slate-100 px-4 py-2 rounded-xl text-sm font-bold text-slate-700">
                                            {{ $news->approvedComments->count() }} yorum
                                        </div>

                                    </div>

                                    {{-- YORUM FORMU --}}
                                    @auth

                                        @php
                                           $activePunishment = \App\Models\UserPunishment::where('user_id', auth()->id())
    ->where('is_active', true)
    ->whereIn('type', ['mute', 'temporary_ban', 'permanent_ban'])
    ->where(function ($query) {
        $query->whereNull('expires_at')
            ->orWhere('expires_at', '>', now());
    })
    ->latest()
    ->first();
                                        @endphp

                                        @if(
                                            $activePunishment &&
                                            (
                                                $activePunishment->isMute() ||
                                                $activePunishment->isTemporaryBan() ||
                                                $activePunishment->isPermanentBan()
                                            )
                                        )

                                            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">
                                                <h3 class="font-black text-red-700 text-lg">
                                                    Yorum Yetkiniz Kısıtlandı
                                                </h3>

                                                <p class="text-red-600 mt-3">
                                                    {{ $activePunishment->reason }}
                                                </p>

                                                @if($activePunishment->expires_at)
                                                    <p class="text-sm text-red-500 mt-2">
                                                        Bitiş: {{ $activePunishment->expires_at->format('d.m.Y H:i') }}
                                                    </p>
                                                @else
                                                    <p class="text-sm text-red-500 mt-2">
                                                        Süresiz ceza uygulanmış.
                                                    </p>
                                                @endif
                                            </div>

                                        @else

                                            <form method="POST"
                                                  action="{{ route('comments.news.store', $news) }}"
                                                  class="bg-slate-50 border border-slate-200 rounded-3xl p-6 mb-10">
                                                @csrf

                                                <div class="flex items-start gap-4">

                                                    <div class="w-14 h-14 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-xl font-black shrink-0">
                                                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                                                    </div>

                                                    <div class="flex-1">

                                                        <textarea
                                                            name="content"
                                                            rows="5"
                                                            class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                            placeholder="Yorumunuzu yazın..."
                                                            required
                                                        ></textarea>

                                                        <div class="flex flex-wrap items-center justify-between gap-4 mt-4">

                                                            <div class="text-sm text-slate-500">
                                                                Yorumunuz moderatör onayından sonra yayınlanacaktır.
                                                            </div>

                                                            <button type="submit"
                                                                    class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-black transition">
                                                                Yorumu Gönder
                                                            </button>

                                                        </div>

                                                    </div>

                                                </div>

                                            </form>

                                        @endif

                                    @else

                                        <div class="bg-slate-100 border border-slate-200 rounded-3xl p-8 text-center mb-10">

                                            <div class="text-5xl mb-4">💬</div>

                                            <h3 class="text-2xl font-black text-slate-900">
                                                Yorum Yapmak İçin Giriş Yapın
                                            </h3>

                                            <p class="text-slate-500 mt-3">
                                                Haber hakkında yorum yapmak için hesabınıza giriş yapmanız gerekiyor.
                                            </p>

                                            <div class="flex justify-center gap-4 mt-6">

                                                <a href="/login"
                                                   class="bg-blue-700 text-white px-6 py-3 rounded-xl font-black hover:bg-blue-800 transition">
                                                    Giriş Yap
                                                </a>

                                                <a href="/register"
                                                   class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black hover:bg-slate-800 transition">
                                                    Kayıt Ol
                                                </a>

                                            </div>

                                        </div>

                                    @endauth

                                    {{-- YORUM LİSTESİ --}}
                                    <div class="space-y-6">

                                        @forelse($news->approvedComments as $comment)

                                            <div class="bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">

                                                <div class="flex items-start gap-4">

                                                    <div class="w-14 h-14 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-xl font-black shrink-0">
                                                        {{ strtoupper(mb_substr($comment->user->name, 0, 1)) }}
                                                    </div>

                                                    <div class="flex-1">

                                                        <div class="flex flex-wrap items-center gap-3 mb-3">

                                                            <h4 class="font-black text-slate-900">
                                                                {{ $comment->user->name }}
                                                            </h4>

                                                            <span class="text-xs text-slate-400">
                                                                {{ $comment->created_at->diffForHumans() }}
                                                            </span>

                                                        </div>

                                                        <div class="text-slate-700 leading-8">
                                                            {{ $comment->content }}
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        @empty

                                            <div class="bg-slate-50 border border-dashed border-slate-300 rounded-3xl p-10 text-center">

                                                <div class="text-5xl mb-4">📝</div>

                                                <h3 class="text-2xl font-black text-slate-900">
                                                    Henüz yorum yapılmadı
                                                </h3>

                                                <p class="text-slate-500 mt-3">
                                                    İlk yorumu yapan kişi siz olun.
                                                </p>

                                            </div>

                                        @endforelse

                                    </div>

                                </div>

                            @endif

                        </div>

                    </article>

                </main>

                {{-- SAĞ SIDEBAR --}}
                <aside class="col-span-12 lg:col-span-3 space-y-6">

                    {{-- SON HABERLER --}}
                    <div class="theme-card premium-card overflow-hidden">

                        <div class="bg-slate-900 text-white px-5 py-4 font-black text-lg">
                            Son Haberler
                        </div>

                        <div class="p-5 space-y-5">

                            @foreach(($sidebarNews ?? collect())->take(5) as $sidebarItem)

                                <a href="/haber/{{ $sidebarItem->slug }}" class="flex gap-4 group">

                                    <div class="w-24 h-20 shrink-0 overflow-hidden rounded">

                                        @if($sidebarItem->image)

                                            <img
                                                src="{{ asset('storage/' . (str_contains($sidebarItem->image, '/') ? $sidebarItem->image : 'news/' . $sidebarItem->image)) }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                            >

                                        @else

                                            <div class="w-full h-full bg-slate-200"></div>

                                        @endif

                                    </div>

                                    <div>
                                        <h3 class="font-bold leading-6 text-slate-800 group-hover:text-blue-700 transition">
                                            {{ Str::limit($sidebarItem->title, 55) }}
                                        </h3>

                                        <div class="text-xs text-slate-400 mt-2">
                                            {{ $sidebarItem->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                </a>

                            @endforeach

                        </div>

                    </div>

                    {{-- TREND HABERLER --}}
                    <div class="theme-card premium-card overflow-hidden">

                        <div class="bg-red-600 text-white px-5 py-4 font-black text-lg">
                            Trend Haberler
                        </div>

                        <div class="p-5 space-y-4">

                            @foreach(($sidebarNews ?? collect())->sortByDesc('views')->take(5) as $trendNews)

                                <a href="/haber/{{ $trendNews->slug }}" class="flex items-start gap-4 group border-b pb-4 last:border-none">

                                    <div class="text-3xl font-black text-red-500">
                                        {{ $loop->iteration }}
                                    </div>

                                    <div>
                                        <h3 class="font-bold leading-6 text-slate-800 group-hover:text-red-600 transition">
                                            {{ Str::limit($trendNews->title, 60) }}
                                        </h3>

                                        <div class="text-xs text-slate-400 mt-2">
                                            👁 {{ $trendNews->views }} okunma
                                        </div>
                                    </div>

                                </a>

                            @endforeach

                        </div>

                    </div>
{{-- SON YORUMLAR --}}
<div class="theme-card premium-card overflow-hidden">

    <div class="bg-blue-700 text-white px-5 py-4 font-black text-lg">
        Son Yorumlar
    </div>

    <div class="p-5 space-y-5">

        @foreach(($latestComments ?? collect()) as $lastComment)

            @php
                $commentUrl = '#';
                $commentableSlug = $lastComment->commentable?->slug;

                if($lastComment->commentable_type === 'App\Models\News' && $commentableSlug) {
                    $commentUrl = '/haber/' . $commentableSlug;
                }

                if($lastComment->commentable_type === 'App\Models\Announcement' && $commentableSlug) {
                    $commentUrl = '/ilan/' . $commentableSlug;
                }
            @endphp

            <a
                href="{{ $commentUrl }}"
                class="block border-b pb-4 last:border-none group"
            >

                <div class="flex items-start gap-3">

                    <div class="w-11 h-11 rounded-xl bg-blue-700 text-white flex items-center justify-center font-black shrink-0">

                        {{ strtoupper(mb_substr($lastComment->user->name, 0, 1)) }}

                    </div>

                    <div class="flex-1">

                        <div class="flex items-center gap-2 mb-2">

                            <span class="font-black text-slate-900 text-sm">
                                {{ $lastComment->user->name }}
                            </span>

                            <span class="text-xs text-slate-400">
                                {{ $lastComment->created_at->diffForHumans() }}
                            </span>

                        </div>

                        <p class="text-sm text-slate-600 leading-6 group-hover:text-blue-700 transition">

                            {{ Str::limit($lastComment->content, 90) }}

                        </p>

                    </div>

                </div>

            </a>

        @endforeach

    </div>

</div>
                    {{-- REKLAM --}}
                    <div class="premium-ad-slot p-2 md:p-3">
                        <img src="https://dummyimage.com/336x280/cccccc/000000&text=REKLAM+ALANI" class="max-h-[160px] w-full object-cover md:max-h-none">
                    </div>

                </aside>

            </div>

        </div>

        {{-- SAĞ REKLAM --}}
        <aside class="hidden 2xl:block col-span-2">

            @if(isset($rightAd) && $rightAd?->image)

                <a href="{{ $rightAd->url }}" target="_blank">
                    <img src="{{ asset('storage/' . $rightAd->image) }}" class="w-full shadow">
                </a>

            @else

                <img src="https://dummyimage.com/300x600/cccccc/000000&text=SAG+REKLAM" class="w-full shadow">

            @endif

        </aside>

    </div>

</section>

@endsection




