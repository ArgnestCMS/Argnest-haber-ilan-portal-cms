@extends('frontend.layout')
@section(
    'title',
    isset($category)
        ? $category->name . ' İlanları | ' . ($siteSetting?->site_name ?? config('app.name'))
        : 'İlanlar | Kamu İlanları ve Personel Alımları | ' . ($siteSetting?->site_name ?? config('app.name'))
)

@section(
    'meta_description',
    isset($category)
        ? ($category->description
            ?? $category->name . ' kategorisindeki güncel ilanlar, personel alımları ve başvuru duyuruları.')
        : 'Güncel kamu ilanları, personel alımı duyuruları, başvuru ilanları ve en yeni ilan haberleri.'
)

@section(
    'meta_keywords',
    isset($category)
        ? $category->name . ', ilanlar, personel alımı, kamu ilanları'
        : 'ilanlar, kamu ilanları, personel alımı, memur alımı, işçi alımı'
)

@section(
    'canonical',
    isset($category)
        ? url('/kategori/' . $category->slug)
        : url('/ilanlar')
)

@section('meta_image', asset('default-og.jpg'))

@section('content')

@php
    $featuredAnnouncement = $announcements->first();

    
    $listAnnouncements = $announcements;
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
                    Sol İlan Reklam Alanı
                </div>

            </div>

        </aside>

        {{-- ORTA ALAN --}}
        <div class="col-span-12 2xl:col-span-8">

            {{-- BAŞLIK --}}
            <div class="bg-white border border-slate-200 shadow-sm p-7 mb-6">

                <div class="flex items-center gap-3 mb-3">

                    <span class="bg-blue-700 text-white text-xs font-bold px-3 py-1 rounded">
                        KAMU İLANLARI
                    </span>

                    <span class="text-sm text-slate-500">
                        Personel alımı, duyuru ve başvuru ilanları
                    </span>

                </div>

                <h1 class="text-4xl font-black text-slate-950">
                    İlanlar
                </h1>

                <p class="text-slate-500 mt-2">
                    Güncel kamu ilanları, personel alımları ve duyurular
                </p>

            </div>

            {{-- ÖNE ÇIKAN İLAN --}}
            @if($featuredAnnouncement)

                <a href="/ilan/{{ $featuredAnnouncement->slug }}"
                   class="block bg-white border border-slate-200 shadow-sm hover:shadow-xl transition mb-8 overflow-hidden">

                    <div class="h-2 bg-gradient-to-r from-blue-700 via-sky-500 to-blue-700"></div>

                    <div class="p-8">

                        <div class="flex flex-wrap items-center gap-3 mb-5">

                            <span class="bg-blue-700 text-white text-xs font-black px-4 py-2 rounded">
                                ÖNE ÇIKAN İLAN
                            </span>

                            <span class="bg-green-50 text-green-700 text-xs font-bold px-3 py-2 rounded border border-green-100">
                                Güncel Başvuru
                            </span>

                        </div>

                        <h2 class="text-4xl font-black leading-tight text-slate-950 hover:text-blue-700 transition">
                            {{ $featuredAnnouncement->title }}
                        </h2>

                        @if($featuredAnnouncement->summary ?? false)

                            <p class="text-slate-600 mt-5 leading-8 text-lg">
                                {{ Str::limit($featuredAnnouncement->summary, 220) }}
                            </p>

                        @endif

                        <div class="mt-7 grid md:grid-cols-3 gap-4">

                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">

                                <div class="text-xs text-slate-500 mb-2">
                                    Yayın Tarihi
                                </div>

                                <div class="font-black text-slate-900 text-lg">
                                    {{ $featuredAnnouncement->created_at->format('d.m.Y') }}
                                </div>

                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">

                                <div class="text-xs text-slate-500 mb-2">
                                    Görüntülenme
                                </div>

                                <div class="font-black text-slate-900 text-lg">
                                    {{ $featuredAnnouncement->views }}
                                </div>

                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-5">

                                <div class="text-xs text-slate-500 mb-2">
                                    Durum
                                </div>

                                <div class="font-black text-green-600 text-lg">
                                    Aktif
                                </div>

                            </div>

                        </div>

                        <div class="mt-7">

                            <span class="inline-flex items-center gap-2 bg-blue-700 text-white px-6 py-3 font-black text-sm rounded-xl">
                                İlan Detayını Gör
                                <span>→</span>
                            </span>

                        </div>

                    </div>

                </a>

            @endif

            {{-- ÜST REKLAM --}}
            <div class="bg-white border border-dashed border-slate-300 shadow-sm mb-8">

                <div class="h-24 flex items-center justify-center text-slate-400 text-sm">
                    İlan Liste Üst Reklam Alanı
                </div>

            </div>

            {{-- BAŞLIK --}}
            <div class="flex items-center justify-between mb-5">

                <h2 class="text-3xl font-black text-slate-950">
                    Son İlanlar
                </h2>

                <span class="text-sm text-slate-500 font-semibold">
                    {{ $announcements->total() }} ilan
                </span>

            </div>

            {{-- GRID --}}
            <div class="grid md:grid-cols-2 gap-6">

                @foreach ($listAnnouncements as $item)

                    <a href="/ilan/{{ $item->slug }}"
                       class="group relative bg-white border border-slate-200 shadow-sm hover:shadow-xl transition overflow-hidden">

                        {{-- ÜST ŞERİT --}}
                        <div class="h-1.5 bg-gradient-to-r from-blue-700 via-sky-500 to-blue-700"></div>

                        <div class="p-5">

                            {{-- BADGE --}}
                            <div class="flex items-center justify-between mb-4">

                                <div class="flex flex-wrap items-center gap-2">

                                    <span class="bg-blue-700 text-white px-3 py-1 text-xs font-black rounded">
                                        İLAN
                                    </span>

                                    <span class="bg-green-50 text-green-700 px-3 py-1 text-xs font-bold rounded border border-green-100">
                                        Güncel
                                    </span>

                                </div>

                                <span class="text-xs text-slate-400 font-semibold">
                                    #{{ $loop->iteration }}
                                </span>

                            </div>

                            {{-- BAŞLIK --}}
                            <h3 class="text-2xl font-black leading-8 text-slate-950 group-hover:text-blue-700 transition">
                                {{ $item->title }}
                            </h3>

                            {{-- ÖZET --}}
                            @if($item->summary ?? false)

                                <p class="text-slate-600 text-sm mt-3 leading-6">
                                    {{ Str::limit($item->summary, 120) }}
                                </p>

                            @else

                                <p class="text-slate-500 text-sm mt-3 leading-6">
                                    Bu ilana ait detayları görüntülemek için ilan sayfasını ziyaret edin.
                                </p>

                            @endif

                            {{-- BİLGİLER --}}
                            <div class="mt-5 grid grid-cols-2 gap-3">

                                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">

                                    <div class="text-xs text-slate-500 mb-1">
                                        Yayın Tarihi
                                    </div>

                                    <div class="font-black text-slate-900">
                                        {{ $item->created_at->format('d.m.Y') }}
                                    </div>

                                </div>

                                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">

                                    <div class="text-xs text-slate-500 mb-1">
                                        Görüntülenme
                                    </div>

                                    <div class="font-black text-slate-900">
                                        {{ $item->views }}
                                    </div>

                                </div>

                            </div>

                            {{-- ALT --}}
                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">

                                <span class="inline-flex items-center gap-2 text-blue-700 font-black text-sm group-hover:gap-3 transition-all">
                                    Detayları İncele
                                    <span>→</span>
                                </span>

                                <span class="text-xs text-slate-400 font-semibold">
                                    ilanhaber.net
                                </span>

                            </div>

                        </div>

                    </a>

                @endforeach

            </div>

            {{-- ALT REKLAM --}}
            <div class="bg-white border border-dashed border-slate-300 shadow-sm mt-8">

                <div class="h-28 flex items-center justify-center text-slate-400 text-sm">
                    İlan Liste Alt Reklam Alanı
                </div>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-8">
                {{ $announcements->links() }}
            </div>

        </div>

{{-- SAĞ SIDEBAR --}}
<aside class="hidden xl:block xl:col-span-4 2xl:col-span-2">

    <div class="sticky top-6 space-y-6">

        {{-- POPÜLER İLANLAR --}}
        <div class="bg-white border border-slate-200 shadow-sm">

            <div class="bg-slate-950 text-white px-5 py-4 font-black text-lg">
                🚀 Popüler İlanlar
            </div>

            <div class="divide-y divide-slate-100">

                @foreach($announcements->take(5) as $popular)

                    <a href="/ilan/{{ $popular->slug }}"
                       class="flex gap-4 p-4 hover:bg-slate-50 transition">

                        <div class="text-blue-700 font-black text-2xl w-8">
                            {{ $loop->iteration }}
                        </div>

                        <div>

                            <h3 class="font-bold text-slate-900 leading-6 hover:text-blue-700 transition">
                                {{ Str::limit($popular->title, 65) }}
                            </h3>

                            <div class="text-xs text-slate-500 mt-2">
                                👁️ {{ $popular->views }} görüntülenme
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

        {{-- SON EKLENENLER --}}
        <div class="bg-white border border-slate-200 shadow-sm">

            <div class="bg-blue-700 text-white px-5 py-4 font-black text-lg">
                📌 Son Eklenenler
            </div>

            <div class="p-4 space-y-4">

                @foreach($announcements->take(5) as $latest)

                    <a href="/ilan/{{ $latest->slug }}"
                       class="block border-b border-slate-100 pb-4 last:border-0">

                        <h3 class="font-bold text-slate-900 leading-6 hover:text-blue-700 transition">
                            {{ Str::limit($latest->title, 70) }}
                        </h3>

                        <div class="text-xs text-slate-500 mt-2 flex gap-3">
                            <span>📅 {{ $latest->created_at->format('d.m.Y') }}</span>
                            <span>👁️ {{ $latest->views }}</span>
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