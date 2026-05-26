<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Bakım Modu | {{ $siteSetting?->site_name ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-3xl overflow-hidden rounded-2xl border border-white/10 bg-white shadow-2xl">
            <div class="bg-slate-900 px-6 py-8 text-white sm:px-10">
                <p class="text-xs font-black uppercase tracking-[0.22em] text-amber-300">Bakım Modu</p>
                <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">
                    {{ $siteSetting?->site_name ?? config('app.name') }}
                </h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 sm:text-base">
                    {{ $siteSetting?->maintenance_message ?: 'Sitemizde kısa süreli bakım çalışması yapıyoruz. Daha hızlı ve güvenli bir deneyim için birazdan yeniden yayında olacağız.' }}
                </p>
            </div>

            <div class="grid gap-5 px-6 py-7 text-slate-900 sm:px-10 md:grid-cols-[1fr_auto] md:items-center">
                <div>
                    <div class="text-sm font-black uppercase tracking-wide text-slate-500">Durum</div>
                    <div class="mt-1 text-xl font-black">Geçici olarak kapalı</div>

                    @if($siteSetting?->maintenance_ends_at)
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Tahmini bitiş:
                            <span class="font-black text-slate-950">
                                {{ $siteSetting->maintenance_ends_at->timezone(config('app.timezone'))->format('d.m.Y H:i') }}
                            </span>
                        </p>
                    @else
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Tahmini bitiş zamanı henüz paylaşılmadı.
                        </p>
                    @endif
                </div>

                <a href="{{ route('login') }}" class="inline-flex justify-center rounded-lg bg-amber-500 px-5 py-3 text-sm font-black text-slate-950 shadow hover:bg-amber-400">
                    Admin Girişi
                </a>
            </div>
        </section>
    </main>
</body>
</html>
