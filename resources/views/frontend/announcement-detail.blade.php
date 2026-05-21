@extends('frontend.layout')

 @php
    $siteSetting = \App\Models\SiteSetting::first();
 @endphp

@section('title', $announcement->title)

@section(
    'meta_description',
    $announcement->summary
        ? Str::limit(strip_tags($announcement->summary), 160)
        : Str::limit(strip_tags($announcement->content), 160)
)

@section(
    'meta_keywords',
    $announcement->title . ', ilan, personel alımı, kamu ilanı'
)

@section('canonical', url()->current())

@section(
    'meta_image',
    $announcement->image
        ? asset('storage/' . (str_contains($announcement->image, '/') ? $announcement->image : 'announcements/' . $announcement->image))
        : asset('default-og.jpg')
)

@section('schema')

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',

    'headline' => $announcement->title,

    'description' => \Illuminate\Support\Str::limit(
        strip_tags($announcement->description ?? $announcement->content ?? $announcement->title),
        160
    ),

    'image' => $announcement->image
        ? asset('storage/' . (str_contains($announcement->image, '/') ? $announcement->image : 'announcements/' . $announcement->image))
        : asset('default-og.jpg'),

    'datePublished' => $announcement->created_at?->toAtomString(),

    'dateModified' => $announcement->updated_at?->toAtomString(),

    'author' => [
        '@type' => 'Organization',
        'name' => $siteSetting?->site_name ?? config('app.name'),
    ],

    'publisher' => [
        '@type' => 'Organization',

        'name' => $siteSetting?->site_name ?? config('app.name'),

        'logo' => [
            '@type' => 'ImageObject',
            'url' => $siteSetting?->logo
                ? asset('storage/' . $siteSetting->logo)
                : asset('favicon.png'),
        ],
    ],

    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => url('/ilan/' . $announcement->slug),
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
            'name' => 'İlanlar',
            'item' => url('/ilanlar'),
        ],

        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $announcement->category->name ?? 'Kategori',
            'item' => url('/kategori/' . ($announcement->category->slug ?? 'kategori')),
        ],

        [
            '@type' => 'ListItem',
            'position' => 4,
            'name' => $announcement->title,
            'item' => url('/ilan/' . $announcement->slug),
        ],

    ],

], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

@endsection

@section('content')

@php
    $imagePath = $announcement->image
        ? (str_contains($announcement->image, '/') ? $announcement->image : 'announcements/' . $announcement->image)
        : null;

    $contentAttachmentImages = $announcement->contentAttachments
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

        {{-- ORTA DETAY ALANI --}}
        <div class="col-span-12 2xl:col-span-8">

            <div class="grid grid-cols-12 gap-6 items-start">

        <main class="col-span-12 min-w-0 lg:col-span-9">

            {{-- ÜST REKLAM --}}
            @if(isset($topAd) && $topAd?->image)

                <div class="premium-ad-slot mb-4 flex justify-center p-2 md:mb-6 md:p-3">
                    <a href="{{ $topAd->url }}" target="_blank">
                        <img
                            src="{{ asset('storage/' . $topAd->image) }}"
                            class="w-full max-w-[970px]"
                        >
                    </a>
                </div>

            @endif

            <article class="theme-card premium-article overflow-hidden">

                @if($imagePath)
                    <div class="relative">
                        <img
                            src="{{ asset('storage/' . $imagePath) }}"
                            class="h-[320px] w-full object-cover md:h-[520px]"
                            alt="{{ $announcement->title }}"
                        >

                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/35 to-black/10"></div>

                        <div class="absolute bottom-5 left-4 right-4 md:bottom-8 md:left-8 md:right-8">
                            <div class="mb-3 text-xs font-bold text-white/80 sm:text-sm">
                                <a href="/" class="hover:underline">Anasayfa</a>
                                /
                                <a href="/ilanlar" class="hover:underline">İlanlar</a>
                            </div>

                            <div class="max-w-4xl">
                                <span class="theme-card theme-primary-text inline-flex rounded-full bg-white px-4 py-1 text-xs font-black text-blue-700 shadow-sm sm:text-sm">
                                    İLAN
                                </span>

                                <h1 class="mt-3 text-2xl font-black leading-tight text-white sm:text-3xl md:mt-5 md:text-5xl">
                                    {{ $announcement->title }}
                                </h1>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gradient-to-r from-blue-700 via-slate-900 to-slate-950 p-5 text-white sm:p-6 md:p-8">
                        <div class="mb-4 text-xs font-bold text-white/80 sm:text-sm">
                            <a href="/" class="hover:underline">Anasayfa</a>
                            /
                            <a href="/ilanlar" class="hover:underline">İlanlar</a>
                        </div>

                        <div class="max-w-4xl">
                            <span class="theme-card theme-primary-text inline-flex rounded-full bg-white px-4 py-1 text-xs font-black text-blue-700 shadow-sm sm:text-sm">
                                İLAN
                            </span>

                            <h1 class="mt-3 text-2xl font-black leading-tight sm:text-3xl md:mt-5 md:text-5xl">
                                {{ $announcement->title }}
                            </h1>
                        </div>
                    </div>
                @endif

                <div class="p-4 sm:p-5 md:p-8">

                    {{-- META --}}
                    <div class="mb-5 flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-slate-100 pb-5 text-xs font-bold text-slate-500 md:mb-6 md:text-sm">
                        <span>📅 {{ $announcement->created_at->format('d.m.Y H:i') }}</span>
                        <span>👁️ {{ $announcement->views }} görüntülenme</span>
                        <span>✍️ {{ $siteSetting?->site_name ?? config('app.name') }}</span>
                    </div>

                    {{-- SOSYAL --}}
                    <div class="mb-6 flex gap-2 overflow-x-auto pb-1 text-sm md:flex-wrap md:gap-3 md:overflow-visible md:pb-0">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                           target="_blank"
                           class="shrink-0 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                            Facebook
                        </a>

                        <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $announcement->title }}"
                           target="_blank"
                           class="shrink-0 rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                            X
                        </a>

                        <a href="https://api.whatsapp.com/send?text={{ $announcement->title }} {{ url()->current() }}"
                           target="_blank"
                           class="shrink-0 rounded bg-green-600 px-4 py-2 text-sm font-semibold text-white">
                            WhatsApp
                        </a>
                    </div>

                    @if($announcement->summary)

                        <div class="theme-card mb-6 rounded-2xl border border-blue-100 border-l-4 border-l-blue-600 bg-blue-50 p-4 text-lg font-semibold md:mb-8 md:p-5 md:text-xl">
                            {{ $announcement->summary }}
                        </div>

                    @endif

                    @if($contentAttachmentImages->isNotEmpty())
                        <div class="theme-card mb-6 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 p-3 md:mb-8 md:p-4">
                            <div class="mb-3 text-sm font-black uppercase tracking-wide text-slate-500">
                                İlan Görselleri
                            </div>

                            <div class="flex gap-3 overflow-x-auto pb-1">
                                @foreach($contentAttachmentImages as $asset)
                                    <a
                                        href="{{ $asset->url }}"
                                        data-content-lightbox-image
                                        class="theme-card block shrink-0 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 transition hover:border-blue-300 hover:shadow-sm"
                                    >
                                        <img
                                            src="{{ $asset->thumbnail_url ?? $asset->url }}"
                                            alt="{{ $asset->original_name ?? $announcement->title }}"
                                            class="h-20 w-28 rounded-xl object-cover sm:h-24 sm:w-32 md:h-28 md:w-40"
                                        >
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="premium-reading prose max-w-none overflow-hidden md:prose-lg prose-img:h-auto prose-img:max-w-full prose-img:rounded-2xl prose-table:block prose-table:w-full prose-table:overflow-x-auto [&_iframe]:max-w-full [&_video]:max-w-full">
                        {!! \App\Support\ContentHtml::render($announcement->content) !!}
                    </div>
                    <div class="mt-8 flex flex-col gap-4 border-t pt-6 sm:flex-row sm:flex-wrap sm:justify-between">

                        <a href="/ilanlar"
                           class="rounded-full bg-slate-800 px-5 py-3 font-black text-white hover:bg-slate-700">
                            ← Tüm İlanlara Dön
                        </a>

                        <div class="flex gap-2 overflow-x-auto pb-1 md:gap-3 md:overflow-visible md:pb-0">

                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                               target="_blank"
                               class="shrink-0 rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white">
                                Facebook
                            </a>

                            <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $announcement->title }}"
                               target="_blank"
                               class="shrink-0 rounded bg-slate-900 px-4 py-2 text-sm font-semibold text-white">
                                X
                            </a>

                            <a href="https://api.whatsapp.com/send?text={{ $announcement->title }} {{ url()->current() }}"
                               target="_blank"
                               class="shrink-0 rounded bg-green-600 px-4 py-2 text-sm font-semibold text-white">
                                WhatsApp
                            </a>

                        </div>

                    </div>

                </div>

            </article>
{{-- YORUM SİSTEMİ --}}
@if($announcement->comments_enabled)

    <div class="theme-card premium-card mt-4 p-5 md:mt-6 md:p-8">

        <div class="flex items-center justify-between mb-8">

            <div>

                <h2 class="text-2xl font-black text-slate-900 md:text-3xl">
                    Yorumlar
                </h2>

                <p class="text-slate-500 mt-2">
                    İlan hakkında görüşlerinizi paylaşabilirsiniz.
                </p>

            </div>

            <div class="bg-slate-100 px-4 py-2 rounded-xl text-sm font-bold text-slate-700">

                {{ $announcement->approvedComments->count() }} yorum

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

            @if($activePunishment)

                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-8">

                    <h3 class="font-black text-red-700 text-lg">
                        Yorum Yetkiniz Kısıtlandı
                    </h3>

                    <p class="text-red-600 mt-3">
                        {{ $activePunishment->reason }}
                    </p>

                    @if($activePunishment->expires_at)

                        <p class="text-sm text-red-500 mt-2">
                            Bitiş:
                            {{ $activePunishment->expires_at->format('d.m.Y H:i') }}
                        </p>

                    @else

                        <p class="text-sm text-red-500 mt-2">
                            Süresiz ceza uygulanmış.
                        </p>

                    @endif

                </div>

            @else

                <form
                    method="POST"
                    action="{{ route('comments.announcement.store', $announcement) }}"
                    class="mb-8 rounded-2xl border border-slate-200 bg-slate-50 p-4 md:mb-10 md:rounded-3xl md:p-6"
                >
                    @csrf

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">

                        <div class="w-14 h-14 rounded-2xl bg-blue-700 text-white flex items-center justify-center text-xl font-black shrink-0">

                            {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}

                        </div>

                        <div class="flex-1">

                            <textarea
                                name="content"
                                rows="5"
                                class="w-full rounded-2xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="İlan hakkında yorumunuzu yazın..."
                                required
                            ></textarea>

                            <div class="flex flex-wrap items-center justify-between gap-4 mt-4">

                                <div class="text-sm text-slate-500">
                                    Yorumunuz moderatör onayından sonra yayınlanacaktır.
                                </div>

                                <button
                                    type="submit"
                                    class="bg-blue-700 hover:bg-blue-800 text-white px-6 py-3 rounded-xl font-black transition"
                                >
                                    Yorumu Gönder
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            @endif

        @else

            <div class="bg-slate-100 border border-slate-200 rounded-3xl p-8 text-center mb-10">

                <div class="text-5xl mb-4">
                    💬
                </div>

                <h3 class="text-2xl font-black text-slate-900">
                    Yorum Yapmak İçin Giriş Yapın
                </h3>

                <p class="text-slate-500 mt-3">
                    İlan hakkında yorum yapmak için hesabınıza giriş yapmanız gerekiyor.
                </p>

                <div class="flex justify-center gap-4 mt-6">

                    <a
                        href="/login"
                        class="bg-blue-700 text-white px-6 py-3 rounded-xl font-black hover:bg-blue-800 transition"
                    >
                        Giriş Yap
                    </a>

                    <a
                        href="/register"
                        class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black hover:bg-slate-800 transition"
                    >
                        Kayıt Ol
                    </a>

                </div>

            </div>

        @endauth

        {{-- YORUM LİSTESİ --}}
        <div class="space-y-6">

            @forelse($announcement->approvedComments as $comment)

                <div class="theme-card bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">

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

                    <div class="text-5xl mb-4">
                        📝
                    </div>

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
            {{-- İLAN ALTI REKLAM --}}
            <div class="premium-ad-slot mt-4 flex justify-center p-2 md:mt-6 md:p-3">

                @if(isset($bottomAd) && $bottomAd?->image)

                    <a href="{{ $bottomAd->url }}" target="_blank">
                        <img
                            src="{{ asset('storage/' . $bottomAd->image) }}"
                            class="max-h-[120px] w-full max-w-[970px] object-cover md:max-h-none"
                        >
                    </a>

                @else

                    <img
                        src="https://dummyimage.com/970x90/cccccc/000000&text=ILAN+ALTI+REKLAM"
                        class="max-h-[120px] w-full max-w-[970px] object-cover md:max-h-none"
                    >

                @endif

            </div>

        </main>

        <aside class="col-span-12 min-w-0 space-y-6 lg:col-span-3">

            <div class="space-y-6 lg:sticky lg:top-32">

                {{-- ÇOK GÖRÜNTÜLENEN İLANLAR --}}
                <div class="theme-card premium-card overflow-hidden">

                    <div class="bg-red-600 text-white px-5 py-4 font-black text-lg">
                        Çok Görüntülenen İlanlar
                    </div>

                    <div class="p-5 space-y-4">

                        @foreach(($sidebarAnnouncements ?? collect())->sortByDesc('views')->take(5) as $index => $item)

                            <a href="/ilan/{{ $item->slug }}"
                               class="flex items-start gap-4 group border-b pb-4 last:border-none">

                                <div class="text-3xl font-black text-red-500">
                                    {{ $index + 1 }}
                                </div>

                                <div class="min-w-0">
                                    <h3 class="font-bold leading-6 text-slate-800 group-hover:text-red-600 transition">
                                        {{ Str::limit($item->title, 60) }}
                                    </h3>

                                    <p class="text-xs text-slate-500 mt-1">
                                        👁️ {{ $item->views }} görüntülenme
                                    </p>
                                </div>

                            </a>

                        @endforeach

                    </div>

                </div>

                {{-- SAĞ REKLAM --}}
                @if(isset($sidebarAd) && $sidebarAd?->image)

                    <div class="premium-ad-slot p-2 md:p-3">

                        <a href="{{ $sidebarAd->url }}" target="_blank">
                            <img
                                src="{{ asset('storage/' . $sidebarAd->image) }}"
                                class="max-h-[280px] w-full object-cover md:max-h-none"
                            >
                        </a>

                    </div>

                @endif

                {{-- SON İLANLAR --}}
                <div class="theme-card premium-card overflow-hidden">

                    <div class="theme-secondary-bg bg-slate-900 text-white px-5 py-4 font-black text-lg">
                        Son İlanlar
                    </div>

                    <div class="p-5 space-y-5">

                        @foreach($sidebarAnnouncements ?? [] as $item)

                            <a href="/ilan/{{ $item->slug }}"
                               class="flex gap-4 group">

                                <div class="w-24 h-20 shrink-0 overflow-hidden rounded">
                                    @if($item->image)
                                        <img
                                            src="{{ asset('storage/' . (str_contains($item->image, '/') ? $item->image : 'announcements/' . $item->image)) }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                        >
                                    @else
                                        <div class="w-full h-full bg-slate-200"></div>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <h3 class="font-bold leading-6 text-slate-800 group-hover:text-blue-700 transition">
                                        {{ Str::limit($item->title, 55) }}
                                    </h3>

                                    <p class="text-xs text-slate-400 mt-2">
                                        {{ $item->created_at->diffForHumans() }}
                                    </p>
                                </div>

                            </a>

                        @endforeach

                    </div>

                </div>

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
                <img src="https://dummyimage.com/300x600/cccccc/000000&text=SAĞ+REKLAM" class="w-full shadow">
            @endif
        </aside>

    </div>

</section>
@endsection
