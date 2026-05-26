@extends('frontend.layout')

@php
    $siteSetting = \App\Models\SiteSetting::first();

    $portal = $announcementPortal ?? [
        'headlines' => collect(),
        'categoryBlocks' => collect(),
        'popular' => collect(),
        'latest' => collect(),
        'categories' => collect(),
    ];

    $headlines = $portal['headlines'] ?? collect();
    $headline = $headlines->first() ?: $announcements->first();

    $listAnnouncements = $announcements->getCollection()
        ->when($headline, fn ($items) => $items->reject(fn ($item) => $item->is($headline)));

    $latestCompact = $listAnnouncements->isNotEmpty()
        ? $listAnnouncements
        : $announcements->getCollection();
@endphp

@section(
    'title',
    isset($category)
        ? $category->name . ' İlanları | ' . ($siteSetting?->site_name ?? config('app.name'))
        : 'İlanlar | Kamu İlanları ve Personel Alımları | ' . ($siteSetting?->site_name ?? config('app.name'))
)

@section(
    'meta_description',
    isset($category)
        ? ($category->description ?? $category->name . ' kategorisindeki güncel ilanlar, personel alımları ve başvuru duyuruları.')
        : 'Güncel kamu ilanları, personel alımı duyuruları, başvuru ilanları ve en yeni ilan haberleri.'
)

@section('canonical', isset($category) ? url('/kategori/' . $category->slug) : url('/ilanlar'))
@section('meta_image', asset('default-og.jpg'))

@section('content')
<section class="mx-auto max-w-6xl px-4 py-6">

    <div class="mb-6">
        <div class="mb-2 flex flex-wrap items-center gap-3">
            <span class="text-sm font-bold text-blue-700">TÜM İLANLAR</span>
            <span class="text-sm text-slate-500">Personel alımı, duyuru ve başvuru ilanları</span>
        </div>

        <h1 class="text-3xl font-black text-slate-950 md:text-4xl">
            {{ isset($category) ? $category->name . ' İlanları' : 'İlanlar' }}
        </h1>

        <p class="mt-2 text-sm text-slate-500 md:text-base">
            Güncel Tüm ilanlar, personel alımları ve duyurular
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="space-y-4 lg:col-span-8">
            @if($headline)
                <section class="theme-card overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                    <a href="/ilan/{{ $headline->slug }}" class="relative block h-[190px] overflow-hidden bg-slate-950 text-white sm:h-[220px] md:h-[300px]">
                        @if($headline->image)
                            <img
                                src="{{ asset('storage/' . (str_contains($headline->image, '/') ? $headline->image : 'announcements/' . $headline->image)) }}"
                                alt="{{ $headline->title }}"
                                class="absolute inset-0 h-full w-full object-cover"
                            >
                            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/45 to-black/10"></div>
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-800 via-slate-950 to-slate-900"></div>
                        @endif

                        <div class="absolute inset-x-0 bottom-0 p-5">
                            <span class="theme-primary-bg mb-3 inline-flex rounded-lg bg-blue-700 px-3 py-1 text-xs font-black">
                                İLAN MANŞET
                            </span>

                            <h2 class="max-w-3xl text-xl font-black leading-tight md:text-2xl">
                                {{ $headline->title }}
                            </h2>

                            @if($headline->summary)
                                <p class="mt-2 max-w-2xl text-sm font-semibold text-white/80">
                                    {{ Str::limit($headline->summary, 110) }}
                                </p>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2 text-xs font-bold text-white/80">
                                <span>{{ $headline->created_at->format('d.m.Y') }}</span>
                                <span>{{ $headline->views }} görüntülenme</span>
                                @if($headline->category)
                                    <span>{{ $headline->category->name }}</span>
                                @endif
                            </div>
                        </div>
                    </a>

                    @if($headlines->isNotEmpty())
                        <div class="theme-card flex gap-2 overflow-x-auto border-t border-slate-100 bg-white px-4 py-3">
                            @foreach($headlines->take(10) as $item)
                                <a
                                    href="/ilan/{{ $item->slug }}"
                                    class="flex h-8 min-w-8 items-center justify-center rounded-full text-xs font-black transition
                                    {{ $item->is($headline) ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
                                >
                                    {{ $loop->iteration }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            <div class="theme-card rounded-2xl border border-dashed border-slate-300 bg-white/70 py-4 text-center text-slate-400">
                İlan Liste Üst Reklam Alanı
            </div>

        </div>

        <aside class="max-w-[240px] space-y-3 lg:col-span-4">

            <div class="theme-card overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="bg-red-600 px-3 py-2 text-sm font-black text-white">Çok Görüntülenenler</div>

                <div class="divide-y divide-slate-100">
                    @forelse($portal['popular'] as $popular)
                        <a href="/ilan/{{ $popular->slug }}" class="flex gap-2 p-2.5 transition hover:bg-slate-50">
                            <div class="w-6 shrink-0 text-xl font-black text-red-500">{{ $loop->iteration }}</div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">{{ Str::limit($popular->title, 54) }}</h3>
                                <p class="mt-1 text-xs font-bold text-slate-400">{{ $popular->views }} görüntülenme</p>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-sm font-bold text-slate-400">Henüz veri yok.</div>
                    @endforelse
                </div>
            </div>

            <div class="theme-card overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="bg-blue-700 px-3 py-2 text-sm font-black text-white">Son İlanlar</div>

                <div class="space-y-2 p-2.5">
                    @forelse($portal['latest'] as $latest)
                        <a href="/ilan/{{ $latest->slug }}" class="flex gap-2">
                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                @if($latest->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($latest->image, '/') ? $latest->image : 'announcements/' . $latest->image)) }}"
                                        alt="{{ $latest->title }}"
                                        class="h-full w-full object-cover"
                                    >
                                @endif
                            </div>

                            <div>
                                <h3 class="text-sm font-black leading-5 text-slate-900">{{ Str::limit($latest->title, 50) }}</h3>
                                <p class="mt-1 text-xs text-slate-400">{{ $latest->created_at->format('d.m.Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="text-sm font-bold text-slate-400">Henüz ilan yok.</div>
                    @endforelse
                </div>
            </div>

            <div class="theme-card overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="bg-slate-950 px-3 py-2 text-sm font-black text-white">İlan Kategorileri</div>

                <div class="divide-y divide-slate-100">
                    @forelse($portal['categories'] as $announcementCategory)
                        <a href="/kategori/{{ $announcementCategory->slug }}" class="flex items-center justify-between gap-2 px-3 py-2 text-sm font-bold hover:bg-slate-50">
                            <span>{{ $announcementCategory->name }}</span>
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-500">{{ $announcementCategory->announcements_count }}</span>
                        </a>
                    @empty
                        <div class="p-4 text-sm font-bold text-slate-400">Kategori yok.</div>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-dashed border-slate-300 bg-white/60">
                <div class="bg-slate-900 px-4 py-2 text-xs font-bold text-white">SPONSORLU</div>
                <div class="flex h-[250px] items-center justify-center text-sm text-slate-400">
                    300x300 Reklam Alanı
                </div>
            </div>

        </aside>

        <main class="space-y-6 lg:col-span-8">
            @if(($portal['categoryBlocks'] ?? collect())->isNotEmpty())
                <section class="grid gap-2 md:grid-cols-2">
                    @foreach($portal['categoryBlocks'] as $block)
                        <div class="theme-card max-w-[260px] overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                            <div class="flex items-center justify-between border-b border-slate-100 px-3 py-2">
                                <div>
                                    <h2 class="font-black text-slate-950">{{ $block->name }}</h2>
                                    <p class="text-xs font-bold text-slate-400">Son 5 ilan</p>
                                </div>

                                <a href="/kategori/{{ $block->slug }}" class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-black text-blue-700">
                                    Tümü
                                </a>
                            </div>

                            <div class="divide-y divide-slate-100">
                                @foreach($block->announcements->take(5) as $item)
                                    <a href="/ilan/{{ $item->slug }}" class="flex gap-2 p-2 transition hover:bg-slate-50">
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                            @if($item->image)
                                                <img
                                                    src="{{ asset('storage/' . (str_contains($item->image, '/') ? $item->image : 'announcements/' . $item->image)) }}"
                                                    alt="{{ $item->title }}"
                                                    class="h-full w-full object-cover"
                                                >
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <h3 class="text-sm font-black leading-5 text-slate-900">
                                                {{ Str::limit($item->title, 64) }}
                                            </h3>
                                            <p class="mt-1 text-xs font-bold text-slate-400">
                                                {{ $item->created_at->format('d.m.Y') }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </section>
            @endif

            <section>
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-xl font-black text-slate-950">Son İlanlar</h2>
                    <span class="text-sm font-semibold text-slate-500">{{ $announcements->total() }} ilan</span>
                </div>

                <div class="grid gap-2 md:grid-cols-2">
                    @forelse($latestCompact as $item)
                        <a href="/ilan/{{ $item->slug }}" class="theme-card flex max-w-[260px] gap-2 rounded-2xl bg-white p-2 shadow-sm ring-1 ring-slate-200 transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                @if($item->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($item->image, '/') ? $item->image : 'announcements/' . $item->image)) }}"
                                        alt="{{ $item->title }}"
                                        class="h-full w-full object-cover"
                                    >
                                @endif
                            </div>

                            <div class="min-w-0">
                                <span class="text-[11px] font-black text-blue-700">İLAN</span>
                                <h3 class="mt-1 text-sm font-black leading-5 text-slate-950">
                                    {{ Str::limit($item->title, 72) }}
                                </h3>
                                <div class="mt-2 flex gap-2 text-xs font-bold text-slate-400">
                                    <span>{{ $item->created_at->format('d.m.Y') }}</span>
                                    <span>{{ $item->views }} görüntülenme</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="theme-card rounded-2xl bg-white p-4 text-center text-sm font-bold text-slate-400 ring-1 ring-slate-200 md:col-span-2">
                            Şu anda aktif ilan bulunmuyor.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $announcements->links() }}
                </div>
            </section>

            <div class="theme-card rounded-2xl border border-dashed border-slate-300 bg-white/60 px-4 py-6 text-center text-sm text-slate-400">
                İlan Liste Alt Reklam Alanı
            </div>
        </main>

        <aside class="hidden max-w-[240px] space-y-3 lg:col-span-4">

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="bg-red-600 px-3 py-2 text-sm font-black text-white">Çok Görüntülenenler</div>

                <div class="divide-y divide-slate-100">
                    @forelse($portal['popular'] as $popular)
                        <a href="/ilan/{{ $popular->slug }}" class="flex gap-2 p-2.5 transition hover:bg-slate-50">
                            <div class="w-6 shrink-0 text-xl font-black text-red-500">{{ $loop->iteration }}</div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900">{{ Str::limit($popular->title, 54) }}</h3>
                                <p class="mt-1 text-xs font-bold text-slate-400">{{ $popular->views }} görüntülenme</p>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-sm font-bold text-slate-400">Henüz veri yok.</div>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="bg-blue-700 px-3 py-2 text-sm font-black text-white">Son İlanlar</div>

                <div class="space-y-2 p-2.5">
                    @forelse($portal['latest'] as $latest)
                        <a href="/ilan/{{ $latest->slug }}" class="flex gap-2">
                            <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                @if($latest->image)
                                    <img
                                        src="{{ asset('storage/' . (str_contains($latest->image, '/') ? $latest->image : 'announcements/' . $latest->image)) }}"
                                        alt="{{ $latest->title }}"
                                        class="h-full w-full object-cover"
                                    >
                                @endif
                            </div>

                            <div>
                                <h3 class="text-sm font-black leading-5 text-slate-900">{{ Str::limit($latest->title, 50) }}</h3>
                                <p class="mt-1 text-xs text-slate-400">{{ $latest->created_at->format('d.m.Y') }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="text-sm font-bold text-slate-400">Henüz ilan yok.</div>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="bg-slate-950 px-3 py-2 text-sm font-black text-white">İlan Kategorileri</div>

                <div class="divide-y divide-slate-100">
                    @forelse($portal['categories'] as $announcementCategory)
                        <a href="/kategori/{{ $announcementCategory->slug }}" class="flex items-center justify-between gap-2 px-3 py-2 text-sm font-bold hover:bg-slate-50">
                            <span>{{ $announcementCategory->name }}</span>
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-500">{{ $announcementCategory->announcements_count }}</span>
                        </a>
                    @empty
                        <div class="p-4 text-sm font-bold text-slate-400">Kategori yok.</div>
                    @endforelse
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-dashed border-slate-300 bg-white/60">
                <div class="bg-slate-900 px-4 py-2 text-xs font-bold text-white">SPONSORLU</div>
                <div class="flex h-[250px] items-center justify-center text-sm text-slate-400">
                    300x300 Reklam Alanı
                </div>
            </div>

        </aside>
    </div>
</section>
@endsection




