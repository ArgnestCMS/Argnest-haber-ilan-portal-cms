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

@section('content')

@php
    $listNews = $news;
    $activeCategorySlug = isset($category) ? $category->slug : null;
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
           class="premium-chip {{ $activeCategorySlug ? '' : 'premium-chip-active' }}">
            Tümü
        </a>

        @foreach(($newsCategories ?? collect()) as $newsCategory)
            <a href="/kategori/{{ $newsCategory->slug }}"
               class="premium-chip {{ $activeCategorySlug === $newsCategory->slug ? 'premium-chip-active' : '' }}">
                {{ $newsCategory->name }}
            </a>
        @endforeach

    </div>

</div>
            {{-- HABER MANŞET --}}
            @if(($headlineNews ?? collect())->isNotEmpty())
                <div class="theme-card premium-card mb-6 overflow-hidden md:mb-8">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h2 class="premium-section-heading">Haber Manşet</h2>
                    </div>

                    <div class="grid gap-4 p-3 md:grid-cols-2 md:p-5">
                        @foreach($headlineNews as $headlineItem)
                            @php
                                $headlineImage = $headlineItem->image
                                    ? (str_contains($headlineItem->image, '/') ? $headlineItem->image : 'news/' . $headlineItem->image)
                                    : null;
                            @endphp

                            <a href="/haber/{{ $headlineItem->slug }}" class="group flex min-w-0 gap-3 rounded-2xl border border-slate-100 bg-white p-3 transition hover:border-blue-200 hover:bg-blue-50/60">
                                <div class="h-20 w-24 shrink-0 overflow-hidden rounded-xl bg-slate-100 md:h-24 md:w-32">
                                    @if($headlineImage)
                                        <img
                                            src="{{ asset('storage/' . $headlineImage) }}"
                                            alt="{{ $headlineItem->title }}"
                                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                        >
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <div class="text-[11px] font-black uppercase text-blue-700">Manşet</div>
                                    <h3 class="mt-1 line-clamp-2 text-base font-black leading-6 text-slate-950 group-hover:text-blue-700">
                                        {{ $headlineItem->title }}
                                    </h3>
                                    <p class="mt-2 text-xs font-bold text-slate-400">
                                        {{ $headlineItem->created_at->format('d.m.Y') }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
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
                       class="theme-card premium-card premium-card-hover group overflow-hidden">

                        <div class="premium-media relative h-48 md:h-52">
                            @if($imagePath)
                                <img src="{{ asset('storage/' . $imagePath) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                            @endif

                            <div class="theme-primary-bg absolute top-3 left-3 rounded-full bg-blue-700 px-3 py-1 text-xs font-black text-white shadow">
                                HABER
                            </div>

                            <div class="absolute bottom-3 right-3 rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-slate-700 shadow-sm backdrop-blur">
                                👁 {{ $item->views }}
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
        <div class="theme-card premium-card overflow-hidden">

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
                                👁 {{ $trend->views }} okunma
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
        <div class="theme-card premium-card overflow-hidden">

            <div class="bg-blue-700 text-white px-5 py-4 font-black text-lg">
                👁 Çok Okunanlar
            </div>

            <div class="p-4 space-y-4">

                @foreach($news->sortByDesc('views')->take(5) as $popular)

                    <a href="/haber/{{ $popular->slug }}"
                       class="block border-b border-slate-100 pb-4 last:border-0">

                        <h3 class="font-bold text-slate-900 leading-6 hover:text-blue-700 transition">
                            {{ Str::limit($popular->title, 70) }}
                        </h3>

                        <div class="text-xs text-slate-500 mt-2 flex gap-3">
                            <span>👁 {{ $popular->views }}</span>
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




