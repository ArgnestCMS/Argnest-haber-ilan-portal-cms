@extends('frontend.layout')

@section('title', 'Canli Aktivite | ' . ($siteSetting?->site_name ?? 'ilanhaber.net'))

@section(
    'meta_description',
    'Canli sohbet, canli yayin, duyurular ve forum baglantilari icin ilanhaber.net canli aktivite merkezi.'
)

@section('meta_keywords', 'canli aktivite, canli sohbet, canli yayin, canli duyuru, forum')

@section('canonical', route('live-activity.index'))

@section('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Canli Aktivite',
            'description' => 'Canli sohbet, canli yayin, duyurular ve forum baglantilari icin canli aktivite merkezi.',
            'url' => route('live-activity.index'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')

<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-12">
        <div class="mb-8">
            <div class="mb-3 inline-flex rounded-full bg-red-50 px-4 py-2 text-xs font-black uppercase text-red-700">
                Canli Merkez
            </div>

            <h1 class="text-4xl font-black text-slate-950 md:text-5xl">
                Canli Aktivite
            </h1>

            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                Sohbet, yayin, duyuru ve forum akislarini tek ekranda toplayan premium topluluk merkezi.
            </p>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <a href="{{ route('live-chat.index') }}" class="rounded-2xl border border-red-100 bg-red-50 p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-red-800">Canli Sohbet</h2>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-red-700">
                        {{ $siteSetting?->live_chat_enabled ? 'Aktif' : 'Kapali' }}
                    </span>
                </div>

                <p class="mt-4 text-sm leading-6 text-red-700/80">
                    Uyeler icin anlik sohbet ve topluluk etkilesimi alani.
                </p>
            </a>

            <div class="rounded-2xl border border-slate-200 bg-slate-950 p-6 text-white shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black">Canli Yayin</h2>
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-slate-200">
                        {{ $siteSetting?->live_stream_enabled ? 'Aktif' : 'Kapali' }}
                    </span>
                </div>

                <p class="mt-4 text-sm leading-6 text-slate-300">
                    {{ $siteSetting?->live_stream_description ?: 'Aktif yayin basladiginda bu alanda gosterilecek.' }}
                </p>

                @if($siteSetting?->live_stream_url)
                    <a href="{{ $siteSetting->live_stream_url }}" target="_blank" rel="noopener" class="mt-5 inline-flex rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white transition hover:bg-red-700">
                        Yayina Git
                    </a>
                @endif
            </div>

            <div class="rounded-2xl border border-yellow-100 bg-yellow-50 p-6 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-yellow-800">Canli Duyuru</h2>
                    <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-yellow-700">
                        {{ $siteSetting?->live_announcement_enabled ? 'Aktif' : 'Kapali' }}
                    </span>
                </div>

                <p class="mt-4 text-sm leading-6 text-yellow-800/80">
                    {{ $siteSetting?->live_announcement_text ?: 'Guncel duyurular panelden tanimlandiginda burada yer alacak.' }}
                </p>
            </div>
        </div>

        <div class="mt-8 rounded-2xl border border-blue-100 bg-blue-50 p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-black text-blue-900">Forum Baglantisi</h2>
                    <p class="mt-2 text-sm text-blue-800/80">Topluluk basliklari ve tartismalara forum alanindan devam edin.</p>
                </div>

                <a href="{{ route('forum.index') }}" class="rounded-lg bg-blue-700 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-blue-800">
                    Foruma Git
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
