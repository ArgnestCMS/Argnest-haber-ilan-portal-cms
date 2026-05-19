@extends('frontend.layout')

@section(
    'title',
    isset($category)
        ? $category->name . ' Haberleri | ' . ($siteSetting?->site_name ?? config('app.name'))
        : 'Haberler | Güncel Haberler ve Son Dakika | ' . ($siteSetting?->site_name ?? config('app.name'))
)

@section(
    'meta_description',
    isset($category)
        ? ($category->description
            ?? $category->name . ' kategorisindeki güncel haberler, son dakika gelişmeleri ve tüm içerikler.')
        : 'Türkiye ve dünyadan güncel haberler, son dakika gelişmeleri, kamu gündemi ve en yeni haber başlıkları.'
)

@section(
    'meta_keywords',
    isset($category)
        ? $category->name . ', haberler, son dakika, gündem'
        : 'haberler, son dakika haberleri, güncel haberler, kamu haberleri, gündem'
)

@section(
    'canonical',
    isset($category)
        ? url('/kategori/' . $category->slug)
        : url('/haberler')
)

@section('meta_image', asset('default-og.jpg'))

{{-- Category-aware meta sections above are the canonical SEO source for this listing page.
@section(
    'meta_description',
    'Türkiye ve dünyadan güncel haberler, son dakika gelişmeleri, kamu gündemi ve en yeni haber başlıkları ilanhaber.net üzerinde.'
)

@section(
    'meta_keywords',
    'haberler, son dakika haberleri, güncel haberler, kamu haberleri, gündem, ilanhaber.net'
)

@section('canonical', url('/haberler'))

@section('meta_image', asset('default-og.jpg'))
--}}

@section('content')

@php
    $featuredNews = $news->first();
    $listNews = $news->skip(1);
@endphp

<section class="max-w-7xl mx-auto px-3 mt-4 md:px-4 md:mt-6">

    <div class="grid grid-cols-12 gap-6 items-start">

        {{-- SOL REKLAM --}}
        <aside class="hidden 2xl:block 2xl:col-span-2">
            <div class="premium-ad-slot sticky top-6">
                <div class="premium-ad-label">
                    REKLAM
                </div>

                <div class="h-[520px] flex items-center justify-center bg-slate-100 text-slate-400 text-sm text-center p-4">
                    Sol Reklam Alanı
                </div>
            </div>
        </aside>

        {{-- ORTA İÇERİK --}}
        <div class="col-span-12 xl:col-span-8 2xl:col-span-8">

{{-- KATEGORİ BAR --}}
<div class="premium-page-shell mb-4 p-3 md:mb-6 md:p-4">

    <form action="{{ route('search') }}" method="GET" class="mb-3 flex md:hidden">
        <input
            type="search"
            name="q"
            placeholder="Haberlerde ara"
            class="min-w-0 flex-1 rounded-l-xl border border-slate-200 px-4 py-3 text-base font-bold outline-none focus:border-blue-500"
        >
        <button class="rounded-r-xl bg-blue-700 px-4 text-sm font-black text-white">Ara</button>
    </form>

    <div class="flex items-center gap-2 overflow-x-auto pb-1 md:flex-wrap md:gap-3 md:overflow-visible md:pb-0">

        <a href="/haberler"
           class="premium-chip premium-chip-active">
            Tümü
        </a>

        <a href="/kategori/gundem"
           class="premium-chip">
            Gündem
        </a>

        <a href="/kategori/ekonomi"
           class="premium-chip">
            Ekonomi
        </a>

        <a href="/kategori/teknoloji"
           class="premium-chip">
            Teknoloji
        </a>

        <a href="/kategori/spor"
           class="premium-chip">
            Spor
        </a>

        <a href="/kategori/dunya"
           class="premium-chip">
            Dünya
        </a>

        <a href="/kategori/kamu"
           class="premium-chip">
            Kamu
        </a>

        <a href="/kategori/son-dakika"
           class="premium-chip border-red-600 bg-red-600 text-white hover:border-red-700 hover:bg-red-700 hover:text-white">
            Son Dakika
        </a>

    </div>

</div>
{{-- FLASH HABERLER --}}
<div class="mb-4 overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 text-white shadow-sm md:mb-6 md:rounded-none">

    <div class="flex items-center h-12">

        <div class="bg-red-600 h-full px-5 flex items-center font-black text-sm whitespace-nowrap">
            SON DAKİKA
        </div>

        <marquee
            behavior="scroll"
            direction="left"
            scrollamount="5"
            class="text-sm font-semibold px-4"
        >
            @foreach($news->take(8) as $flash)
                🔥 {{ $flash->title }} —
            @endforeach
        </marquee>

    </div>

</div>
            {{-- MANŞET --}}
            @if($featuredNews)

                @php
                    $featuredImage = $featuredNews->image
                        ? (str_contains($featuredNews->image, '/') ? $featuredNews->image : 'news/' . $featuredNews->image)
                        : null;
                @endphp

                <a href="/haber/{{ $featuredNews->slug }}"
                   class="premium-card premium-card-hover mb-6 block overflow-hidden md:mb-8">

                    <div class="grid lg:grid-cols-2">

                        <div class="premium-media relative h-56 md:h-72">
                            @if($featuredImage)
                                <img src="{{ asset('storage/' . $featuredImage) }}"
                                     class="w-full h-full object-cover hover:scale-105 transition duration-700">
                            @endif

                            <div class="absolute top-4 left-4 rounded-full bg-red-600 px-4 py-2 text-xs font-black text-white shadow-lg">
                                MANŞET
                            </div>
                        </div>

                        <div class="flex flex-col justify-center p-5 md:p-7">

                            <div class="text-sm text-slate-500 flex gap-4 mb-4">
                                <span>📅 {{ $featuredNews->created_at->format('d.m.Y') }}</span>
                                <span>👁️ {{ $featuredNews->views }} okunma</span>
                            </div>

                            <h2 class="text-2xl font-black leading-tight text-slate-950 transition hover:text-blue-700 md:text-3xl">
                                {{ $featuredNews->title }}
                            </h2>

                            @if($featuredNews->summary ?? false)
                                <p class="text-slate-600 mt-4 leading-7">
                                    {{ Str::limit($featuredNews->summary, 180) }}
                                </p>
                            @endif

                            <div class="mt-6">
                                <span class="inline-block bg-blue-700 text-white px-5 py-2 font-bold text-sm rounded">
                                    Haberi Oku →
                                </span>
                            </div>

                        </div>

                    </div>

                </a>

            @endif

            {{-- ÜST/ARA REKLAM --}}
            <div class="premium-ad-slot mb-6 md:mb-8">
                <div class="flex h-20 items-center justify-center px-4 text-center text-sm text-slate-400 md:h-24">
                    Haber Liste Üst Reklam Alanı
                </div>
            </div>

            {{-- HABER LİSTESİ BAŞLIK --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="premium-section-heading">
                    Son Haberler
                </h2>

                <span class="text-sm text-slate-500">
                    {{ $news->total() }} haber
                </span>
            </div>

            {{-- HABER GRID --}}
            <div class="grid gap-4 md:grid-cols-2 md:gap-6">

                @foreach ($listNews as $item)

                    @php
                        $imagePath = $item->image
                            ? (str_contains($item->image, '/') ? $item->image : 'news/' . $item->image)
                            : null;
                    @endphp

                    <a href="/haber/{{ $item->slug }}"
                       class="premium-card premium-card-hover group overflow-hidden">

                        <div class="premium-media relative h-48 md:h-52">
                            @if($imagePath)
                                <img src="{{ asset('storage/' . $imagePath) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                            @endif

                            <div class="absolute top-3 left-3 rounded-full bg-blue-700 px-3 py-1 text-xs font-black text-white shadow">
                                HABER
                            </div>

                            <div class="absolute bottom-3 right-3 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-slate-700 shadow-sm backdrop-blur">
                                👁️ {{ $item->views }}
                            </div>
                        </div>

                        <div class="p-4 md:p-5">

                            <h3 class="text-lg font-black leading-7 text-slate-950 transition group-hover:text-blue-700 md:text-xl">
                                {{ $item->title }}
                            </h3>

                            @if($item->summary ?? false)
                                <p class="text-slate-600 text-sm mt-3 leading-6">
                                    {{ Str::limit($item->summary, 100) }}
                                </p>
                            @endif

                            <div class="text-sm text-slate-500 mt-4 flex gap-3">
                                <span>📅 {{ $item->created_at->format('d.m.Y') }}</span>
                                <span>Devamını Oku →</span>
                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

            {{-- ALT REKLAM --}}
            <div class="premium-ad-slot mt-6 md:mt-8">
                <div class="flex h-20 items-center justify-center px-4 text-center text-sm text-slate-400 md:h-28">
                    Haber Liste Alt Reklam Alanı
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="premium-pagination mt-8">
                {{ $news->links() }}
            </div>

        </div>

        {{-- SAĞ SIDEBAR --}}
<aside class="hidden xl:block xl:col-span-4 2xl:col-span-2">

    <div class="sticky top-6 space-y-6">

        {{-- TREND HABERLER --}}
        <div class="premium-card overflow-hidden">

            <div class="bg-slate-950 text-white px-5 py-4 font-black text-lg">
                🔥 Trend Haberler
            </div>

            <div class="divide-y divide-slate-100">

                @foreach($news->take(5) as $trend)

                    <a href="/haber/{{ $trend->slug }}"
                       class="flex gap-4 p-4 hover:bg-slate-50 transition">

                        <div class="text-red-600 font-black text-2xl w-8">
                            {{ $loop->iteration }}
                        </div>

                        <div>

                            <h3 class="font-bold text-slate-900 leading-6 hover:text-blue-700 transition">
                                {{ Str::limit($trend->title, 65) }}
                            </h3>

                            <div class="text-xs text-slate-500 mt-2">
                                👁️ {{ $trend->views }} okunma
                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

        </div>

        {{-- REKLAM --}}
        <div class="premium-ad-slot">

            <div class="bg-slate-900 text-white text-xs font-bold px-3 py-2">
                SPONSORLU
            </div>

            <div class="h-[320px] flex items-center justify-center bg-slate-100 text-slate-400 text-sm">
                300x300 Reklam Alanı
            </div>

        </div>

        {{-- ÇOK OKUNAN --}}
        <div class="premium-card overflow-hidden">

            <div class="bg-blue-700 text-white px-5 py-4 font-black text-lg">
                👁️ Çok Okunanlar
            </div>

            <div class="p-4 space-y-4">

                @foreach($news->sortByDesc('views')->take(5) as $popular)

                    <a href="/haber/{{ $popular->slug }}"
                       class="block border-b border-slate-100 pb-4 last:border-0">

                        <h3 class="font-bold text-slate-900 leading-6 hover:text-blue-700 transition">
                            {{ Str::limit($popular->title, 70) }}
                        </h3>

                        <div class="text-xs text-slate-500 mt-2 flex gap-3">
                            <span>👁️ {{ $popular->views }}</span>
                            <span>📅 {{ $popular->created_at->format('d.m.Y') }}</span>
                        </div>

                    </a>

                @endforeach

            </div>

        </div>

    </div>

</aside>

    </div>

</section>

@endsection
