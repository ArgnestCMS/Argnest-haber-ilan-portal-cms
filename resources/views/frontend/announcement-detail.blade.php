@extends('frontend.layout')

 @php
    $siteSetting = \App\Models\SiteSetting::first();
 @endphp

@section('title', $announcement->title . ' | ilanhaber.net')

@section(
    'meta_description',
    $announcement->summary
        ? Str::limit(strip_tags($announcement->summary), 160)
        : Str::limit(strip_tags($announcement->content), 160)
)

@section(
    'meta_keywords',
    $announcement->title . ', ilan, personel alımı, kamu ilanı, ilanhaber.net'
)

@section('canonical', url()->current())

@section(
    'meta_image',
    asset('default-og.jpg')
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
        ? asset('storage/' . $announcement->image)
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

<section class="max-w-[1600px] mx-auto px-4 mt-6">

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

<section class="max-w-7xl mx-auto px-4 mt-6">

    <div class="grid grid-cols-12 gap-6 items-start">

        <main class="col-span-12 lg:col-span-8">

            {{-- ÜST REKLAM --}}
            @if(isset($topAd) && $topAd?->image)

                <div class="bg-white p-3 shadow mb-6 flex justify-center">
                    <a href="{{ $topAd->url }}" target="_blank">
                        <img
                            src="{{ asset('storage/' . $topAd->image) }}"
                            class="w-full max-w-[970px]"
                        >
                    </a>
                </div>

            @endif

            <article class="bg-white shadow overflow-hidden">

                <div class="bg-gradient-to-r from-blue-700 to-slate-900 text-white p-8">

                    <div class="text-sm text-white/80 mb-4">
                        <a href="/" class="hover:underline">Anasayfa</a> /
                        <a href="/ilanlar" class="hover:underline">İlanlar</a>
                    </div>

                    <span class="bg-white text-blue-700 px-4 py-1 rounded text-sm font-bold">
                        İLAN
                    </span>

                    <h1 class="text-5xl font-extrabold leading-tight mt-5">
                        {{ $announcement->title }}
                    </h1>

                    <div class="flex flex-wrap gap-4 text-sm text-white/80 mt-5">
                        <span>📅 {{ $announcement->created_at->format('d.m.Y H:i') }}</span>
                        <span>👁️ {{ $announcement->views }} görüntülenme</span>
                    </div>

                </div>

                <div class="p-8">

                    @if($announcement->summary)

                        <div class="bg-blue-50 border-l-4 border-blue-600 p-5 mb-8 text-xl font-semibold">
                            {{ $announcement->summary }}
                        </div>

                    @endif

                    <div class="prose prose-lg max-w-none leading-relaxed text-slate-800">
                        {!! $announcement->content !!}
                    </div>

                    <div class="border-t mt-8 pt-6 flex flex-wrap justify-between gap-4">

                        <a href="/ilanlar"
                           class="bg-slate-800 text-white px-5 py-3 rounded font-semibold hover:bg-slate-700">
                            ← Tüm İlanlara Dön
                        </a>

                        <div class="flex gap-3">

                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                               target="_blank"
                               class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold">
                                Facebook
                            </a>

                            <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $announcement->title }}"
                               target="_blank"
                               class="bg-slate-900 text-white px-4 py-2 rounded text-sm font-semibold">
                                X
                            </a>

                            <a href="https://api.whatsapp.com/send?text={{ $announcement->title }} {{ url()->current() }}"
                               target="_blank"
                               class="bg-green-600 text-white px-4 py-2 rounded text-sm font-semibold">
                                WhatsApp
                            </a>

                        </div>

                    </div>

                </div>

            </article>
{{-- YORUM SİSTEMİ --}}
@if($announcement->comments_enabled)

    <div class="bg-white shadow mt-6 p-8">

        <div class="flex items-center justify-between mb-8">

            <div>

                <h2 class="text-3xl font-black text-slate-900">
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
                    class="bg-slate-50 border border-slate-200 rounded-3xl p-6 mb-10"
                >
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
            <div class="bg-white p-3 shadow mt-6 flex justify-center">

                @if(isset($topAd) && $topAd?->image)

                    <a href="{{ $topAd->url }}" target="_blank">
                        <img
                            src="{{ asset('storage/' . $topAd->image) }}"
                            class="w-full max-w-[970px]"
                        >
                    </a>

                @else

                    <img
                        src="https://dummyimage.com/970x90/cccccc/000000&text=ILAN+ALTI+REKLAM"
                        class="w-full max-w-[970px]"
                    >

                @endif

            </div>

        </main>

        <aside class="col-span-12 lg:col-span-4">

            <div class="sticky top-32 space-y-6">

                {{-- ÇOK GÖRÜNTÜLENEN İLANLAR --}}
                <div class="bg-white shadow">

                    <div class="border-b px-5 py-4">
                        <h2 class="text-2xl font-bold">Çok Görüntülenen İlanlar</h2>
                    </div>

                    <div class="divide-y">

                        @foreach(($sidebarAnnouncements ?? collect())->sortByDesc('views')->take(5) as $index => $item)

                            <a href="/ilan/{{ $item->slug }}"
                               class="flex gap-3 p-4 hover:bg-slate-50">

                                <div class="w-8 h-8 bg-blue-600 text-white flex items-center justify-center font-bold rounded">
                                    {{ $index + 1 }}
                                </div>

                                <div>
                                    <h3 class="font-bold hover:text-blue-600 leading-6">
                                        {{ $item->title }}
                                    </h3>

                                    <p class="text-xs text-slate-500 mt-1">
                                        {{ $item->views }} görüntülenme
                                    </p>
                                </div>

                            </a>

                        @endforeach

                    </div>

                </div>

                {{-- SAĞ REKLAM --}}
                @if(isset($sidebarAd) && $sidebarAd?->image)

                    <div class="bg-white p-3 shadow">

                        <a href="{{ $sidebarAd->url }}" target="_blank">
                            <img
                                src="{{ asset('storage/' . $sidebarAd->image) }}"
                                class="w-full"
                            >
                        </a>

                    </div>

                @endif

                {{-- SON İLANLAR --}}
                <div class="bg-white shadow">

                    <div class="border-b px-5 py-4">
                        <h2 class="text-2xl font-bold">Son İlanlar</h2>
                    </div>

                    <div class="divide-y">

                        @foreach($sidebarAnnouncements ?? [] as $item)

                            <a href="/ilan/{{ $item->slug }}"
                               class="block p-4 hover:bg-slate-50">

                                <h3 class="font-bold hover:text-blue-600">
                                    {{ $item->title }}
                                </h3>

                                <p class="text-sm text-slate-500 mt-2">
                                    {{ $item->created_at->format('d.m.Y') }}
                                </p>

                            </a>

                        @endforeach

                    </div>

                </div>

            </div>

        </aside>

    </div>

</section>

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