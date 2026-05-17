@extends('frontend.layout')

@section('title', 'Canli Sohbet | ' . ($siteSetting?->site_name ?? 'ilanhaber.net'))

@section(
    'meta_description',
    'ilanhaber.net canli sohbet alani ile uyeler gundem, ilanlar ve haberler hakkinda anlik etkilesim kurabilir.'
)

@section('meta_keywords', 'canli sohbet, topluluk sohbeti, ilan sohbet, haber sohbet')

@section('canonical', route('live-chat.index'))

@section('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Canli Sohbet',
            'description' => 'ilanhaber.net canli sohbet alani ile uyeler anlik etkilesim kurabilir.',
            'url' => route('live-chat.index'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')

<section class="mx-auto grid max-w-7xl gap-6 px-4 py-10 lg:grid-cols-[1fr_340px]">
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-950 px-6 py-5 text-white">
            <div>
                <h1 class="text-2xl font-black">Canli Sohbet</h1>
                <p class="mt-1 text-sm text-slate-300">Topluluk icin anlik sohbet iskeleti</p>
            </div>

            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black">
                {{ $siteSetting?->live_chat_enabled ? 'Aktif' : 'Kapali' }}
            </span>
        </div>

        <div class="space-y-4 bg-slate-50 p-6">
            @foreach([
                ['name' => 'Moderator', 'text' => 'Canli sohbet alani hazir. Mesaj altyapisi sonraki adimda baglanabilir.'],
                ['name' => 'Sistem', 'text' => 'Forum ve canli aktivite sayfalari ile entegre calisacak sekilde tasarlandi.'],
                ['name' => 'Topluluk', 'text' => 'Uyeler burada ilan, haber ve gundem basliklarini anlik konusabilir.'],
            ] as $message)
                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div class="font-black text-slate-950">{{ $message['name'] }}</div>
                        <div class="text-xs font-bold text-slate-400">Simdi</div>
                    </div>

                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $message['text'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="border-t border-slate-200 bg-white p-4">
            @if($siteSetting?->live_chat_enabled)
                <div class="flex gap-3">
                    <input type="text" disabled placeholder="Mesaj yazma alani sonraki adimda aktif edilecek" class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm text-slate-500">
                    <button type="button" disabled class="rounded-lg bg-red-600 px-5 py-2 text-sm font-black text-white opacity-60">
                        Gonder
                    </button>
                </div>
            @else
                <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm font-bold text-yellow-800">
                    Canli sohbet panelden kapali. Sayfa iskeleti hazir, aktif edilince mesaj alani baglanabilir.
                </div>
            @endif
        </div>
    </div>

    <aside class="space-y-5">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Sohbet Kurallari</h2>
            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                <p>Saygili dil kullanimi, spam yapmama ve konu disina cikmama temel kuraldir.</p>
                <p>Moderasyon sistemi ilerleyen adimda mevcut yorum ve yetki yapisiyla uyumlu baglanabilir.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-red-100 bg-red-50 p-6 shadow-sm">
            <h2 class="text-lg font-black text-red-800">Canli Aktivite</h2>
            <p class="mt-3 text-sm leading-6 text-red-700/80">Yayin, duyuru ve forum akislarini tek merkezden takip edin.</p>
            <a href="{{ route('live-activity.index') }}" class="mt-5 inline-flex rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white transition hover:bg-red-700">
                Merkeze Don
            </a>
        </div>
    </aside>
</section>

@endsection
