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

<section class="max-w-[1600px] mx-auto px-4 mt-6">

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

            {{-- MANŞET SLIDER --}}
            @if($headlineNews->count() > 0)

                <div
                    x-data="{ active: 0, total: {{ $headlineNews->count() }} }"
                    x-init="setInterval(() => active = (active + 1) % total, 5000)"
                    class="relative overflow-hidden rounded shadow bg-white"
                >

                    @foreach($headlineNews as $index => $news)

                        <a href="/haber/{{ $news->slug }}"
                           x-show="active === {{ $index }}"
                           class="block relative">

                            @if($news->image)
                                <img
                                    src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                    class="w-full h-[480px] object-cover"
                                >
                            @endif

                            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                            <div class="absolute bottom-8 left-8 right-8">
                                <span class="bg-red-600 text-white px-4 py-1 text-sm font-bold rounded">
                                    MANŞET
                                </span>

                                <h1 class="text-white text-5xl font-extrabold leading-tight mt-4">
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
            <div class="bg-white p-3 shadow mt-6 flex justify-center">
                @if(isset($ads['top_banner'][0]))
                    <a href="{{ $ads['top_banner'][0]->url }}" target="_blank">
                        <img src="{{ asset('storage/' . $ads['top_banner'][0]->image) }}" class="w-full max-w-[970px]">
                    </a>
                @else
                    <img src="https://dummyimage.com/970x90/cccccc/000000&text=REKLAM+ALANI" class="w-full max-w-[970px]">
                @endif
            </div>

            {{-- TREND + ÇOK OKUNAN --}}
            <div class="grid lg:grid-cols-2 gap-6 mt-6">

                <div class="bg-white shadow">
                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">🔥 Trend Haberler</h2>
                        <span class="text-xs bg-red-600 text-white px-3 py-1 rounded-full font-bold">
                            SON 24 SAAT
                        </span>
                    </div>

                    <div class="divide-y">
                        @forelse($trendingNews->take(6) as $news)
                            <a href="/haber/{{ $news->slug }}" class="flex gap-4 p-4 hover:bg-slate-50 transition">
                                @if($news->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                        class="w-32 h-20 object-cover"
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

                <div class="bg-white shadow">
                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">👁 Çok Okunanlar</h2>
                        <span class="text-xs bg-slate-900 text-white px-3 py-1 rounded-full font-bold">
                            POPÜLER
                        </span>
                    </div>

                    <div class="divide-y">
                        @forelse($mostReadNews->take(6) as $index => $news)
                            <a href="/haber/{{ $news->slug }}" class="flex gap-4 p-4 hover:bg-slate-50 transition">
                                <div class="w-9 h-9 flex items-center justify-center bg-blue-600 text-white rounded-full font-black">
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
            <div class="grid lg:grid-cols-3 gap-6 mt-6">

                {{-- SON HABERLER --}}
                <div class="lg:col-span-2 bg-white shadow">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Son Haberler</h2>
                        <a href="/haberler" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="divide-y">

                        @foreach($latestNews->take(8) as $news)

                            <a href="/haber/{{ $news->slug }}" class="flex gap-4 p-4 hover:bg-slate-50 transition">

                                @if($news->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($news->image, '/') ? $news->image : 'news/' . $news->image)) }}"
                                        class="w-44 h-28 object-cover"
                                    >
                                @endif

                                <div>
                                    <h3 class="font-bold text-xl hover:text-blue-600">
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
                <div class="bg-white shadow">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">Son İlanlar</h2>
                        <a href="/ilanlar" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="divide-y">

                        @foreach($latestAnnouncements->take(8) as $announcement)

                            <a href="/ilan/{{ $announcement->slug }}" class="block p-4 hover:bg-slate-50 transition">
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
                <div class="bg-white shadow mt-6">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">🎥 Son Videolar</h2>
                        <a href="{{ route('videos.index') }}" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid md:grid-cols-3 gap-5 p-5">
                        @foreach($latestVideos->take(6) as $video)
                            <a href="{{ route('videos.show', $video->slug) }}" class="group">
                                <div class="relative overflow-hidden rounded bg-slate-200">
                                    @if($video->thumbnail)
                                        <img
                                            src="{{ asset('storage/' . $video->thumbnail) }}"
                                            class="w-full h-40 object-cover group-hover:scale-105 transition"
                                        >
                                    @else
                                        <div class="h-40 flex items-center justify-center text-5xl">
                                            🎬
                                        </div>
                                    @endif

                                    <span class="absolute bottom-2 left-2 bg-red-600 text-white px-2 py-1 text-xs font-bold rounded">
                                        VIDEO
                                    </span>
                                </div>

                                <h3 class="font-bold mt-3 line-clamp-2 group-hover:text-blue-600">
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
                <div class="bg-white shadow mt-6">

                    <div class="border-b px-5 py-4 flex justify-between items-center">
                        <h2 class="text-2xl font-bold">🖼️ Son Galeriler</h2>
                        <a href="{{ route('galleries.index') }}" class="text-blue-600 font-semibold">Tümü</a>
                    </div>

                    <div class="grid md:grid-cols-3 gap-5 p-5">
                        @foreach($latestGalleries->take(6) as $gallery)
                            <a href="{{ route('galleries.show', $gallery->slug) }}" class="group">
                                <div class="relative overflow-hidden rounded bg-slate-200">
                                    @if($gallery->cover_image)
                                        <img
                                            src="{{ asset('storage/' . $gallery->cover_image) }}"
                                            class="w-full h-44 object-cover group-hover:scale-105 transition"
                                        >
                                    @else
                                        <div class="h-44 flex items-center justify-center text-5xl">
                                            🖼️
                                        </div>
                                    @endif

                                    <span class="absolute bottom-2 left-2 bg-black/75 text-white px-2 py-1 text-xs font-bold rounded">
                                        GALERİ
                                    </span>
                                </div>

                                <h3 class="font-bold mt-3 line-clamp-2 group-hover:text-blue-600">
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
            <div class="bg-white shadow mt-6">

                <div class="border-b px-5 py-4">
                    <h2 class="text-2xl font-bold">İlan Kategorileri</h2>
                </div>

                <div class="grid md:grid-cols-4 gap-4 p-5">

                    @foreach($announcementCategories as $category)

                        <a href="/kategori/{{ $category->slug }}"
                           class="border rounded p-4 hover:bg-blue-50 hover:border-blue-500 transition">
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
            <div class="bg-white p-3 shadow mt-6 flex justify-center">
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