@extends('frontend.layout')

@section(
    'title',
    ($siteSetting?->site_name ?? config('app.name')) . ' - Güncel Haberler ve Kamu İlanları'
)

@section(
    'meta_description',
    'Güncel haberler, kamu ilanları, personel alımları, KPSS gelişmeleri, memur haberleri ve son dakika içerikleri ' . ($siteSetting?->site_name ?? config('app.name')) . '\'te.'
)

@section('meta_keywords', 'haberler, kamu ilanları, personel alımı, memur alımı, KPSS, son dakika haberleri')

@section('canonical', url('/'))

@section('content')

<style>
    @media (max-width: 767px) {
        .home-mobile-section {
            border-radius: 18px;
            overflow: hidden;
        }

        .home-mobile-ad img {
            max-height: 92px;
            object-fit: cover;
        }

        .home-mobile-card {
            border-radius: 16px;
        }
    }
</style>

<section class="max-w-[1600px] mx-auto px-3 mt-4 md:px-4 md:mt-6">

    <div class="grid grid-cols-12 gap-6">

        {{-- SOL REKLAM --}}
        <aside class="hidden 2xl:block col-span-2">
            <div class="sticky top-32 space-y-4">

                @if(isset($ads['left_sidebar'][0]))
                    <a href="{{ $ads['left_sidebar'][0]->url }}" target="_blank" class="premium-ad-slot block p-2">
                        <img src="{{ asset('storage/' . $ads['left_sidebar'][0]->image) }}" class="w-full rounded-2xl">
                    </a>
                @else
                    <div class="premium-ad-slot">
                        <div class="premium-ad-label">REKLAM</div>
                        <img src="https://dummyimage.com/300x600/cccccc/000000&text=SOL+REKLAM" class="w-full">
                    </div>
                @endif

                @if(isset($ads['left_sidebar'][1]))
                    <a href="{{ $ads['left_sidebar'][1]->url }}" target="_blank" class="premium-ad-slot block p-2">
                        <img src="{{ asset('storage/' . $ads['left_sidebar'][1]->image) }}" class="w-full rounded-2xl">
                    </a>
                @else
                    <div class="premium-ad-slot">
                        <div class="premium-ad-label">SPONSORLU</div>
                        <img src="https://dummyimage.com/300x250/cccccc/000000&text=REKLAM" class="w-full">
                    </div>
                @endif

            </div>
        </aside>

        {{-- ORTA İÇERİK --}}
        <div class="col-span-12 2xl:col-span-8">

            <div class="premium-page-shell mb-4 p-3 md:hidden">
                <form action="{{ route('search') }}" method="GET" class="flex">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Haber, ilan veya video ara"
                        class="min-w-0 flex-1 rounded-l-xl border border-slate-200 px-4 py-3 text-base font-bold outline-none focus:border-blue-500"
                    >
                    <button class="rounded-r-xl bg-blue-700 px-4 text-sm font-black text-white">
                        Ara
                    </button>
                </form>

                <div class="mt-3 flex gap-2 overflow-x-auto pb-1 text-xs font-black">
                    <a href="/haberler" class="premium-chip premium-chip-active">Haberler</a>
                    <a href="/ilanlar" class="premium-chip border-blue-100 bg-blue-50 text-blue-700 hover:border-blue-200 hover:bg-blue-100 hover:text-blue-800">İlanlar</a>
                    <a href="{{ route('videos.index') }}" class="premium-chip border-red-100 bg-red-50 text-red-700 hover:border-red-200 hover:bg-red-100 hover:text-red-800">Videolar</a>
                    <a href="{{ route('galleries.index') }}" class="premium-chip">Galeriler</a>
                </div>
            </div>

            {{-- MANŞET SLIDER --}}
            @if($headlines->count() > 0)

                <div
                    x-data="{ active: 0, total: {{ $headlines->count() }} }"
                    x-init="setInterval(() => active = (active + 1) % total, 5000)"
                    class="premium-card relative overflow-hidden"
                >

                    @foreach($headlines as $index => $item)

                        <a href="{{ $item['url'] }}"
                           x-show="active === {{ $index }}"
                           class="block relative">

                            @if($item['image'])
                                <img
                                    src="{{ $item['image'] }}"
                                    class="h-[260px] w-full object-cover md:h-[480px]"
                                >
                            @else
                                <div class="h-[260px] w-full bg-slate-800 md:h-[480px]"></div>
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                            <div class="absolute bottom-6 left-4 right-4 md:bottom-8 md:left-8 md:right-8">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-900 shadow-lg md:px-4 md:text-sm">
                                        #{{ $index + 1 }}
                                    </span>

                                    <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-black text-white shadow-lg md:px-4 md:text-sm">
                                        {{ $item['badge'] }}
                                    </span>
                                </div>

                                <h1 class="mt-3 text-2xl font-black leading-tight text-white md:mt-4 md:text-5xl">
                                    {{ $item['title'] }}
                                </h1>
                            </div>

                        </a>

                    @endforeach

                    <div class="absolute bottom-4 right-4 flex gap-2">
                        @foreach($headlines as $index => $item)
                            <button
                                @click="active = {{ $index }}"
                                class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-black shadow"
                                :class="active === {{ $index }} ? 'bg-red-600 text-white' : 'bg-white/80 text-slate-900'"
                            >
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                </div>

            @endif

            {{-- ÜST REKLAM --}}
            <div class="home-mobile-ad premium-ad-slot mt-4 flex justify-center p-2 md:mt-6 md:p-3">
                @if(isset($ads['top_banner'][0]))
                    <a href="{{ $ads['top_banner'][0]->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $ads['top_banner'][0]->image) }}" class="w-full max-w-[970px]">
                    </a>
                @else
                    <img src="https://dummyimage.com/970x90/cccccc/000000&text=REKLAM+ALANI" class="w-full max-w-[970px]">
                @endif
            </div>

            {{-- TREND + ÇOK OKUNAN --}}
            <div class="mt-4 grid gap-4 lg:grid-cols-2 lg:gap-6 md:mt-6">

                <div class="premium-card overflow-hidden">
                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="premium-section-heading">🔥 Trend Haberler</h2>
                        <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-black text-white">
                            SON 24 SAAT
                        </span>
                    </div>

                    <div class="divide-y">
                        @forelse($trendingNews->take(6) as $news)
                            <a href="/haber/{{ $news->slug }}" class="flex gap-3 p-3 transition hover:bg-slate-50 md:gap-4 md:p-4">
                                @if($news->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                        class="h-20 w-24 rounded-2xl object-cover md:w-32"
                                    >
                                @endif

                                <div>
                                    <h3 class="font-bold hover:text-red-600 line-clamp-2">
                                        {{ $news->title }}
                                    </h3>

                                    <p class="text-xs text-slate-500 mt-2">
                                        👁 {{ number_format($news->views) }} · Skor: {{ $news->trend_score }}
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="p-5 text-slate-500 text-sm">
                                Henüz trend haber oluşmadı.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="premium-card overflow-hidden">
                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="premium-section-heading">👁 Çok Okunanlar</h2>
                        <span class="rounded-full bg-slate-900 px-3 py-1 text-xs font-black text-white">
                            POPÜLER
                        </span>
                    </div>

                    <div class="divide-y">
                        @forelse($mostReadNews->take(6) as $index => $news)
                            <a href="/haber/{{ $news->slug }}" class="flex gap-3 p-3 transition hover:bg-slate-50 md:gap-4 md:p-4">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-black text-white md:h-9 md:w-9">
                                    {{ $index + 1 }}
                                </div>

                                <div>
                                    <h3 class="font-bold hover:text-blue-600 line-clamp-2">
                                        {{ $news->title }}
                                    </h3>

                                    <p class="text-xs text-slate-500 mt-2">
                                        👁 {{ number_format($news->views) }} okunma
                                    </p>
                                </div>
                            </a>
                        @empty
                            <div class="p-5 text-slate-500 text-sm">
                                Henüz çok okunan haber yok.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- HABER + İLAN --}}
            <div class="mt-4 grid gap-4 lg:grid-cols-2 lg:gap-6 md:mt-6">

                {{-- SON HABERLER --}}
                <div class="premium-card overflow-hidden">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="premium-section-heading">Son Haberler</h2>
                        <a href="/haberler" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid gap-3 p-3 sm:grid-cols-2 md:gap-4 md:p-4">

                        @foreach($latestNews->take(8) as $news)

                            <a href="/haber/{{ $news->slug }}" class="home-mobile-card overflow-hidden rounded-2xl border border-slate-100 bg-white transition hover:border-blue-100 hover:bg-blue-50/60">

                                @if($news->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                        class="h-36 w-full object-cover"
                                    >
                                @endif

                                <div class="p-4">
                                    <h3 class="line-clamp-2 text-base font-bold hover:text-blue-600">
                                        {{ $news->title }}
                                    </h3>

                                    <p class="text-slate-500 mt-2 text-sm">
                                        {{ $news->created_at->format('d.m.Y') }}
                                    </p>
                                </div>

                            </a>

                        @endforeach

                    </div>

                </div>

                {{-- SON İLANLAR --}}
                <div class="premium-card overflow-hidden">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="premium-section-heading">Son İlanlar</h2>
                        <a href="/ilanlar" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid gap-3 p-3 sm:grid-cols-2 md:gap-4 md:p-4">

                        @foreach($latestAnnouncements->take(8) as $announcement)

                            <a href="/ilan/{{ $announcement->slug }}" class="home-mobile-card overflow-hidden rounded-2xl border border-slate-100 bg-white transition hover:border-blue-100 hover:bg-blue-50/60">
                                @if($announcement->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($announcement->image, '/') ? $announcement->image : 'announcements/' . $announcement->image)) }}"
                                        class="h-36 w-full object-cover"
                                    >
                                @endif

                                <div class="p-4">
                                    <h3 class="line-clamp-2 text-base font-bold hover:text-blue-600">
                                        {{ $announcement->title }}
                                    </h3>

                                    <p class="text-slate-500 mt-2 text-sm">
                                        {{ $announcement->created_at->format('d.m.Y') }}
                                    </p>
                                </div>
                            </a>

                        @endforeach

                    </div>

                </div>

            </div>

            {{-- VİDEOLAR --}}
            @if(isset($latestVideos) && $latestVideos->count())
                <div class="premium-card mt-4 overflow-hidden md:mt-6">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="premium-section-heading">🎥 Son Videolar</h2>
                        <a href="{{ route('videos.index') }}" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3 md:grid-cols-3 md:gap-5 md:p-5">
                        @foreach($latestVideos->take(6) as $video)
                            <a href="{{ route('videos.show', $video->slug) }}" class="group">
                                <div class="premium-media relative rounded-2xl">
                                    @if($video->thumbnail)
                                        <img
                                            src="{{ asset('storage/' . $video->thumbnail) }}"
                                            class="h-28 w-full object-cover transition group-hover:scale-105 md:h-40"
                                        >
                                    @else
                                        <div class="flex h-28 items-center justify-center text-4xl md:h-40 md:text-5xl">
                                            🎬
                                        </div>
                                    @endif

                                    <span class="absolute bottom-2 left-2 rounded-full bg-red-600 px-3 py-1 text-xs font-black text-white shadow">
                                        VIDEO
                                    </span>
                                </div>

                                <h3 class="mt-2 line-clamp-2 text-sm font-bold group-hover:text-blue-600 md:mt-3 md:text-base">
                                    {{ $video->title }}
                                </h3>

                                <p class="text-xs text-slate-500 mt-1">
                                    👁 {{ number_format($video->views) }}
                                </p>
                            </a>
                        @endforeach
                    </div>

                </div>
            @endif

            {{-- GALERİLER --}}
            @if(isset($latestGalleries) && $latestGalleries->count())
                <div class="premium-card mt-4 overflow-hidden md:mt-6">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="premium-section-heading">🖼️ Son Galeriler</h2>
                        <a href="{{ route('galleries.index') }}" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3 md:grid-cols-3 md:gap-5 md:p-5">
                        @foreach($latestGalleries->take(6) as $gallery)
                            <a href="{{ route('galleries.show', $gallery->slug) }}" class="group">
                                <div class="premium-media relative rounded-2xl">
                                    @if($gallery->cover_image)
                                        <img
                                            src="{{ asset('storage/' . $gallery->cover_image) }}"
                                            class="h-32 w-full object-cover transition group-hover:scale-105 md:h-44"
                                        >
                                    @else
                                        <div class="flex h-32 items-center justify-center text-4xl md:h-44 md:text-5xl">
                                            🖼️
                                        </div>
                                    @endif

                                    <span class="absolute bottom-2 left-2 rounded-full bg-black/75 px-3 py-1 text-xs font-black text-white shadow">
                                        GALERİ
                                    </span>
                                </div>

                                <h3 class="mt-2 line-clamp-2 text-sm font-bold group-hover:text-blue-600 md:mt-3 md:text-base">
                                    {{ $gallery->title }}
                                </h3>

                                <p class="text-xs text-slate-500 mt-1">
                                    👁 {{ number_format($gallery->views) }}
                                </p>
                            </a>
                        @endforeach
                    </div>

                </div>
            @endif

            {{-- KATEGORİLER --}}
            <div class="premium-card mt-4 overflow-hidden md:mt-6">

                <div class="border-b px-5 py-4">
                    <h2 class="premium-section-heading">Kategoriler</h2>
                </div>

                <div class="grid gap-5 p-3 md:grid-cols-2 md:p-5">
                    <div>
                        <h3 class="mb-3 text-sm font-black uppercase tracking-wide text-slate-500">
                            Haber Kategorileri
                        </h3>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach($newsCategories as $category)
                                <a href="/kategori/{{ $category->slug }}"
                                   class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 transition hover:border-blue-200 hover:bg-blue-50 hover:shadow-sm">
                                    <h4 class="font-bold text-blue-700">
                                        {{ $category->name }}
                                    </h4>

                                    <p class="text-sm text-slate-500 mt-1">
                                        Güncel haberleri görüntüle
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-3 text-sm font-black uppercase tracking-wide text-slate-500">
                            İlan Kategorileri
                        </h3>

                        <div class="grid grid-cols-2 gap-3">
                            @foreach($announcementCategories as $category)
                                <a href="/kategori/{{ $category->slug }}"
                                   class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 transition hover:border-blue-200 hover:bg-blue-50 hover:shadow-sm">
                                    <h4 class="font-bold text-blue-700">
                                        {{ $category->name }}
                                    </h4>

                                    <p class="text-sm text-slate-500 mt-1">
                                        Güncel ilanları görüntüle
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>

            {{-- ALT REKLAM --}}
            <div class="home-mobile-ad premium-ad-slot mt-4 flex justify-center p-2 md:mt-6 md:p-3">
                @if(isset($ads['bottom_banner'][0]))
                    <a href="{{ $ads['bottom_banner'][0]->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $ads['bottom_banner'][0]->image) }}" class="w-full max-w-[970px]">
                    </a>
                @else
                    <img src="https://dummyimage.com/970x90/cccccc/000000&text=ALT+REKLAM+ALANI" class="w-full max-w-[970px]">
                @endif
            </div>

        </div>

        {{-- SAĞ REKLAM --}}
        <aside class="hidden 2xl:block col-span-2">
            <div class="sticky top-32 space-y-4">

                @if(isset($ads['right_sidebar'][0]))
                    <a href="{{ $ads['right_sidebar'][0]->url }}" target="_blank" class="premium-ad-slot block p-2">
                        <img src="{{ asset('storage/' . $ads['right_sidebar'][0]->image) }}" class="w-full rounded-2xl">
                    </a>
                @else
                    <div class="premium-ad-slot">
                        <div class="premium-ad-label">REKLAM</div>
                        <img src="https://dummyimage.com/300x600/cccccc/000000&text=SAĞ+REKLAM" class="w-full">
                    </div>
                @endif

                @if(isset($ads['right_sidebar'][1]))
                    <a href="{{ $ads['right_sidebar'][1]->url }}" target="_blank" class="premium-ad-slot block p-2">
                        <img src="{{ asset('storage/' . $ads['right_sidebar'][1]->image) }}" class="w-full rounded-2xl">
                    </a>
                @else
                    <div class="premium-ad-slot">
                        <div class="premium-ad-label">SPONSORLU</div>
                        <img src="https://dummyimage.com/300x250/cccccc/000000&text=REKLAM" class="w-full">
                    </div>
                @endif

            </div>
        </aside>

    </div>

</section>

@if(isset($popupPoll) && $popupPoll)
    <div
        x-data="homePollPopup({{ $popupPoll->id }}, {{ (int) $popupPoll->popup_cooldown_minutes }})"
        x-init="init()"
        x-show="open"
        x-transition
        class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4"
        style="display:none;"
    >
        <div class="max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-3xl bg-white shadow-2xl">
            @if($popupPoll->image)
                <img src="{{ asset('storage/' . $popupPoll->image) }}" alt="{{ $popupPoll->title }}" class="h-48 w-full rounded-t-3xl object-cover">
            @endif

            <div class="p-5 md:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="text-xs font-black uppercase text-blue-700">{{ $popupPoll->topic ?: 'Anket' }}</div>
                        <h2 class="mt-2 text-2xl font-black leading-tight text-slate-950">{{ $popupPoll->title }}</h2>
                        @if($popupPoll->subtitle)
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $popupPoll->subtitle }}</p>
                        @endif
                    </div>

                    <button type="button" @click="dismiss()" class="rounded-full bg-slate-100 px-3 py-1 text-xl font-black text-slate-600">
                        ×
                    </button>
                </div>

                <form method="POST" action="{{ route('polls.vote', $popupPoll) }}" class="mt-5 space-y-3">
                    @csrf
                    @foreach($popupPoll->activeOptions as $option)
                        <label class="flex cursor-pointer gap-3 rounded-2xl border border-slate-200 p-3 hover:border-blue-300">
                            <input
                                type="{{ $popupPoll->allow_multiple ? 'checkbox' : 'radio' }}"
                                name="{{ $popupPoll->allow_multiple ? 'option_ids[]' : 'option_id' }}"
                                value="{{ $option->id }}"
                                class="mt-1"
                                {{ $popupPoll->allow_multiple ? '' : 'required' }}
                            >
                            @if($option->image)
                                <img src="{{ asset('storage/' . $option->image) }}" alt="{{ $option->title }}" class="h-14 w-16 rounded-xl object-cover">
                            @endif
                            <span>
                                <span class="block font-black text-slate-950">{{ $option->title }}</span>
                                @if($option->description)
                                    <span class="mt-1 block text-xs leading-5 text-slate-500">{{ $option->description }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                        <a href="{{ route('polls.show', $popupPoll->slug) }}" class="text-sm font-black text-blue-700">
                            Detayları gör
                        </a>
                        <button class="rounded-full bg-blue-700 px-5 py-2.5 font-black text-white hover:bg-blue-800">
                            Oy Ver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function homePollPopup(pollId, cooldownMinutes) {
            return {
                open: false,
                key: 'poll-popup-dismissed-' + pollId,
                init() {
                    const dismissedUntil = Number(localStorage.getItem(this.key) || 0);
                    this.open = Date.now() > dismissedUntil;
                },
                dismiss() {
                    const minutes = Math.max(Number(cooldownMinutes || 1440), 1);
                    localStorage.setItem(this.key, String(Date.now() + minutes * 60 * 1000));
                    this.open = false;
                },
            };
        }
    </script>
@endif

@endsection
