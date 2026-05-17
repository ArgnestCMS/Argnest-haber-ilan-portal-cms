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

@section('content')

@php
    $featuredNews = $news->first();
    $listNews = $news->skip(1);
@endphp

<section class="max-w-7xl mx-auto px-4 mt-6">

    <div class="grid grid-cols-12 gap-6 items-start">

        {{-- SOL REKLAM --}}
        <aside class="hidden 2xl:block 2xl:col-span-2">
            <div class="sticky top-6 bg-white border border-slate-200 shadow-sm">
                <div class="bg-slate-900 text-white text-xs font-bold px-3 py-2">
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
<div class="bg-white border border-slate-200 shadow-sm p-4 mb-6 overflow-x-auto">

    <div class="flex flex-wrap items-center gap-3">

        <a href="/haberler"
           class="bg-slate-950 text-white px-5 py-2 rounded-full text-sm font-black hover:bg-slate-800 transition">
            Tümü
        </a>

        <a href="/kategori/gundem"
           class="bg-slate-100 hover:bg-red-600 hover:text-white text-slate-700 px-5 py-2 rounded-full text-sm font-bold transition">
            Gündem
        </a>

        <a href="/kategori/ekonomi"
           class="bg-slate-100 hover:bg-red-600 hover:text-white text-slate-700 px-5 py-2 rounded-full text-sm font-bold transition">
            Ekonomi
        </a>

        <a href="/kategori/teknoloji"
           class="bg-slate-100 hover:bg-red-600 hover:text-white text-slate-700 px-5 py-2 rounded-full text-sm font-bold transition">
            Teknoloji
        </a>

        <a href="/kategori/spor"
           class="bg-slate-100 hover:bg-red-600 hover:text-white text-slate-700 px-5 py-2 rounded-full text-sm font-bold transition">
            Spor
        </a>

        <a href="/kategori/dunya"
           class="bg-slate-100 hover:bg-red-600 hover:text-white text-slate-700 px-5 py-2 rounded-full text-sm font-bold transition">
            Dünya
        </a>

        <a href="/kategori/kamu"
           class="bg-slate-100 hover:bg-red-600 hover:text-white text-slate-700 px-5 py-2 rounded-full text-sm font-bold transition">
            Kamu
        </a>

        <a href="/kategori/son-dakika"
           class="bg-red-600 text-white px-5 py-2 rounded-full text-sm font-black hover:bg-red-700 transition">
            Son Dakika
        </a>

    </div>

</div>
{{-- FLASH HABERLER --}}
<div class="bg-slate-950 text-white border border-slate-800 shadow-sm mb-6 overflow-hidden">

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
                   class="block bg-white border border-slate-200 shadow-sm hover:shadow-xl transition mb-8 overflow-hidden">

                    <div class="grid lg:grid-cols-2">

                        <div class="relative h-72 bg-slate-200 overflow-hidden">
                            @if($featuredImage)
                                <img src="{{ asset('storage/' . $featuredImage) }}"
                                     class="w-full h-full object-cover hover:scale-105 transition duration-700">
                            @endif

                            <div class="absolute top-4 left-4 bg-red-600 text-white text-xs font-black px-4 py-2 rounded">
                                MANŞET
                            </div>
                        </div>

                        <div class="p-7 flex flex-col justify-center">

                            <div class="text-sm text-slate-500 flex gap-4 mb-4">
                                <span>📅 {{ $featuredNews->created_at->format('d.m.Y') }}</span>
                                <span>👁️ {{ $featuredNews->views }} okunma</span>
                            </div>

                            <h2 class="text-3xl font-black leading-tight text-slate-950 hover:text-blue-700 transition">
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
            <div class="bg-white border border-dashed border-slate-300 shadow-sm mb-8">
                <div class="h-24 flex items-center justify-center text-slate-400 text-sm">
                    Haber Liste Üst Reklam Alanı
                </div>
            </div>

            {{-- HABER LİSTESİ BAŞLIK --}}
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-black text-slate-950">
                    Son Haberler
                </h2>

                <span class="text-sm text-slate-500">
                    {{ $news->total() }} haber
                </span>
            </div>

            {{-- HABER GRID --}}
            <div class="grid md:grid-cols-2 gap-6">

                @foreach ($listNews as $item)

                    @php
                        $imagePath = $item->image
                            ? (str_contains($item->image, '/') ? $item->image : 'news/' . $item->image)
                            : null;
                    @endphp

                    <a href="/haber/{{ $item->slug }}"
                       class="group bg-white border border-slate-200 shadow-sm hover:shadow-xl transition overflow-hidden">

                        <div class="relative h-52 bg-slate-200 overflow-hidden">
                            @if($imagePath)
                                <img src="{{ asset('storage/' . $imagePath) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                            @endif

                            <div class="absolute top-3 left-3 bg-blue-700 text-white text-xs font-bold px-3 py-1 rounded">
                                HABER
                            </div>

                            <div class="absolute bottom-3 right-3 bg-white/90 text-slate-700 text-xs font-bold px-3 py-1 rounded">
                                👁️ {{ $item->views }}
                            </div>
                        </div>

                        <div class="p-5">

                            <h3 class="text-xl font-black leading-7 text-slate-950 group-hover:text-blue-700 transition">
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
            <div class="bg-white border border-dashed border-slate-300 shadow-sm mt-8">
                <div class="h-28 flex items-center justify-center text-slate-400 text-sm">
                    Haber Liste Alt Reklam Alanı
                </div>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-8">
                {{ $news->links() }}
            </div>

        </div>

        {{-- SAĞ SIDEBAR --}}
<aside class="hidden xl:block xl:col-span-4 2xl:col-span-2">

    <div class="sticky top-6 space-y-6">

        {{-- TREND HABERLER --}}
        <div class="bg-white border border-slate-200 shadow-sm">

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
        <div class="bg-white border border-slate-200 shadow-sm">

            <div class="bg-slate-900 text-white text-xs font-bold px-3 py-2">
                SPONSORLU
            </div>

            <div class="h-[320px] flex items-center justify-center bg-slate-100 text-slate-400 text-sm">
                300x300 Reklam Alanı
            </div>

        </div>

        {{-- ÇOK OKUNAN --}}
        <div class="bg-white border border-slate-200 shadow-sm">

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