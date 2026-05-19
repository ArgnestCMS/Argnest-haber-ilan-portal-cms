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
                    <a href="{{ $ads['left_sidebar'][0]->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $ads['left_sidebar'][0]->image) }}" class="w-full shadow">
                    </a>
                @else
                    <img src="https://dummyimage.com/300x600/cccccc/000000&text=SOL+REKLAM" class="w-full shadow">
                @endif

                @if(isset($ads['left_sidebar'][1]))
                    <a href="{{ $ads['left_sidebar'][1]->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $ads['left_sidebar'][1]->image) }}" class="w-full shadow">
                    </a>
                @else
                    <img src="https://dummyimage.com/300x250/cccccc/000000&text=REKLAM" class="w-full shadow">
                @endif

            </div>
        </aside>

        {{-- ORTA İÇERİK --}}
        <div class="col-span-12 2xl:col-span-8">

            <div class="mb-4 rounded-2xl bg-white p-3 shadow-sm md:hidden">
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
                    <a href="/haberler" class="shrink-0 rounded-full bg-slate-950 px-4 py-2 text-white">Haberler</a>
                    <a href="/ilanlar" class="shrink-0 rounded-full bg-blue-50 px-4 py-2 text-blue-700">İlanlar</a>
                    <a href="{{ route('videos.index') }}" class="shrink-0 rounded-full bg-red-50 px-4 py-2 text-red-700">Videolar</a>
                    <a href="{{ route('galleries.index') }}" class="shrink-0 rounded-full bg-slate-100 px-4 py-2 text-slate-700">Galeriler</a>
                </div>
            </div>

            {{-- MANŞET SLIDER --}}
            @if($headlineNews->count() > 0)

                <div
                    x-data="{ active: 0, total: {{ $headlineNews->count() }} }"
                    x-init="setInterval(() => active = (active + 1) % total, 5000)"
                    class="home-mobile-section relative overflow-hidden rounded bg-white shadow md:rounded-none"
                >

                    @foreach($headlineNews as $index => $news)

                        <a href="/haber/{{ $news->slug }}"
                           x-show="active === {{ $index }}"
                           class="block relative">

                            @if($news->image)
                                <img
                                    src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                    class="h-[260px] w-full object-cover md:h-[480px]"
                                >
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                            <div class="absolute bottom-6 left-4 right-4 md:bottom-8 md:left-8 md:right-8">
                                <span class="rounded bg-red-600 px-3 py-1 text-xs font-bold text-white md:px-4 md:text-sm">
                                    MANŞET
                                </span>

                                <h1 class="mt-3 text-2xl font-extrabold leading-tight text-white md:mt-4 md:text-5xl">
                                    {{ $news->title }}
                                </h1>
                            </div>

                        </a>

                    @endforeach

                    <div class="absolute bottom-4 right-4 flex gap-2">
                        @foreach($headlineNews as $index => $news)
                            <button
                                @click="active = {{ $index }}"
                                class="w-3 h-3 rounded-full"
                                :class="active === {{ $index }} ? 'bg-red-600' : 'bg-white/70'"
                            ></button>
                        @endforeach
                    </div>

                </div>

            @endif

            {{-- ÜST REKLAM --}}
            <div class="home-mobile-ad mt-4 flex justify-center rounded-2xl bg-white p-2 shadow-sm md:mt-6 md:rounded-none md:p-3 md:shadow">
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

                <div class="home-mobile-section bg-white shadow">
                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">🔥 Trend Haberler</h2>
                        <span class="text-xs bg-red-600 text-white px-3 py-1 rounded-full font-bold">
                            SON 24 SAAT
                        </span>
                    </div>

                    <div class="divide-y">
                        @forelse($trendingNews->take(6) as $news)
                            <a href="/haber/{{ $news->slug }}" class="flex gap-3 p-3 transition hover:bg-slate-50 md:gap-4 md:p-4">
                                @if($news->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                        class="h-20 w-24 rounded-xl object-cover md:w-32 md:rounded-none"
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

                <div class="home-mobile-section bg-white shadow">
                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">👁 Çok Okunanlar</h2>
                        <span class="text-xs bg-slate-900 text-white px-3 py-1 rounded-full font-bold">
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
            <div class="mt-4 grid gap-4 lg:grid-cols-3 lg:gap-6 md:mt-6">

                {{-- SON HABERLER --}}
                <div class="home-mobile-section bg-white shadow lg:col-span-2">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Son Haberler</h2>
                        <a href="/haberler" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="divide-y">

                        @foreach($latestNews->take(8) as $news)

                            <a href="/haber/{{ $news->slug }}" class="flex flex-col gap-3 p-3 transition hover:bg-slate-50 sm:flex-row md:gap-4 md:p-4">

                                @if($news->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                        class="h-44 w-full rounded-xl object-cover sm:h-28 sm:w-44 md:rounded-none"
                                    >
                                @endif

                                <div>
                                    <h3 class="text-lg font-bold hover:text-blue-600 md:text-xl">
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
                <div class="home-mobile-section bg-white shadow">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Son İlanlar</h2>
                        <a href="/ilanlar" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="divide-y">

                        @foreach($latestAnnouncements->take(8) as $announcement)

                            <a href="/ilan/{{ $announcement->slug }}" class="home-mobile-card m-3 block border border-slate-100 bg-slate-50 p-4 transition hover:bg-slate-50 md:m-0 md:border-0 md:bg-white">
                                <h3 class="font-bold hover:text-blue-600">
                                    {{ $announcement->title }}
                                </h3>

                                <p class="text-slate-500 mt-2 text-sm">
                                    {{ $announcement->created_at->format('d.m.Y') }}
                                </p>
                            </a>

                        @endforeach

                    </div>

                </div>

            </div>

            {{-- VİDEOLAR --}}
            @if(isset($latestVideos) && $latestVideos->count())
                <div class="home-mobile-section mt-4 bg-white shadow md:mt-6">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">🎥 Son Videolar</h2>
                        <a href="{{ route('videos.index') }}" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3 md:grid-cols-3 md:gap-5 md:p-5">
                        @foreach($latestVideos->take(6) as $video)
                            <a href="{{ route('videos.show', $video->slug) }}" class="group">
                                <div class="relative overflow-hidden rounded bg-slate-200">
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

                                    <span class="absolute bottom-2 left-2 bg-red-600 text-white px-2 py-1 text-xs font-bold rounded">
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
                <div class="home-mobile-section mt-4 bg-white shadow md:mt-6">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">🖼️ Son Galeriler</h2>
                        <a href="{{ route('galleries.index') }}" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 p-3 md:grid-cols-3 md:gap-5 md:p-5">
                        @foreach($latestGalleries->take(6) as $gallery)
                            <a href="{{ route('galleries.show', $gallery->slug) }}" class="group">
                                <div class="relative overflow-hidden rounded bg-slate-200">
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

                                    <span class="absolute bottom-2 left-2 bg-black/75 text-white px-2 py-1 text-xs font-bold rounded">
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

            {{-- İLAN KATEGORİLERİ --}}
            <div class="home-mobile-section mt-4 bg-white shadow md:mt-6">

                <div class="border-b px-5 py-4">
                    <h2 class="text-2xl font-bold">İlan Kategorileri</h2>
                </div>

                <div class="grid grid-cols-2 gap-3 p-3 md:grid-cols-4 md:gap-4 md:p-5">

                    @foreach($announcementCategories as $category)

                        <a href="/kategori/{{ $category->slug }}"
                           class="rounded-xl border p-4 transition hover:border-blue-500 hover:bg-blue-50">
                            <h3 class="font-bold text-blue-700">
                                {{ $category->name }}
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Güncel ilanları görüntüle
                            </p>
                        </a>

                    @endforeach

                </div>

            </div>

            {{-- ALT REKLAM --}}
            <div class="home-mobile-ad mt-4 flex justify-center rounded-2xl bg-white p-2 shadow-sm md:mt-6 md:rounded-none md:p-3 md:shadow">
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
                    <a href="{{ $ads['right_sidebar'][0]->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $ads['right_sidebar'][0]->image) }}" class="w-full shadow">
                    </a>
                @else
                    <img src="https://dummyimage.com/300x600/cccccc/000000&text=SAĞ+REKLAM" class="w-full shadow">
                @endif

                @if(isset($ads['right_sidebar'][1]))
                    <a href="{{ $ads['right_sidebar'][1]->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $ads['right_sidebar'][1]->image) }}" class="w-full shadow">
                    </a>
                @else
                    <img src="https://dummyimage.com/300x250/cccccc/000000&text=REKLAM" class="w-full shadow">
                @endif

            </div>
        </aside>

    </div>

</section>

@endsection
