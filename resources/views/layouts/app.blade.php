<!DOCTYPE html>
<html lang="tr">

@php
    $siteSetting = \App\Models\SiteSetting::first();
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kullanıcı Paneli | {{ $siteSetting?->site_name ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#f3f3f3] text-slate-900">

<div class="min-h-screen">

    {{-- ÜST MAVİ MENÜ --}}
    <div class="bg-[#0878c9] text-white">
        <div class="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between">

            <a href="/" class="text-3xl font-extrabold tracking-tight">
                {{ $siteSetting?->site_name ?? config('app.name') }}
            </a>

            <nav class="hidden md:flex items-center gap-5 text-sm font-bold">
                <a href="/" class="hover:text-slate-200">HABER</a>
                <a href="/ilanlar" class="hover:text-slate-200">İLAN</a>
                <a href="/haberler" class="hover:text-slate-200">HABERLER</a>
                <a href="#" class="hover:text-slate-200">KPSS</a>
                <a href="#" class="hover:text-slate-200">SINAV</a>
                <a href="#" class="hover:text-slate-200">MAAŞ</a>
                <a href="/dashboard" class="bg-slate-800 px-3 py-4">PANEL</a>
            </nav>

            <div class="hidden md:flex items-center gap-5 text-sm font-semibold">

                <a href="/dashboard#bildirimler" class="relative hover:text-slate-200 transition">
                    <span class="text-xl">🔔</span>

                    @auth
                        @php
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        @endphp

                        @if($unreadCount > 0)
                            <span class="absolute -top-2 -right-3 bg-red-600 text-white text-[10px] font-black min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    @endauth
                </a>

                <a href="/profil/{{ auth()->id() }}" class="hover:text-slate-200">
                    Profilim
                </a>

                <a href="/profile" class="hover:text-slate-200">
                    Ayarlar
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:text-slate-200">
                        Çıkış
                    </button>
                </form>

            </div>

        </div>
    </div>

    {{-- ALT MENÜ --}}
    <div class="bg-slate-800 text-white">
        <div class="max-w-7xl mx-auto px-4 h-10 flex items-center gap-5 text-sm font-semibold overflow-x-auto">
            <a href="/dashboard">Panel</a>
            <a href="/profile">Profil Ayarları</a>
            <a href="#">Haberlerim</a>
            <a href="#">İlanlarım</a>
            <a href="/dashboard#bildirimler">Bildirimler</a>
            <a href="/">Siteye Dön</a>
        </div>
    </div>

    {{-- SON DAKİKA --}}
    <div class="bg-red-600 text-white overflow-hidden border-b border-red-700">
        <div class="max-w-7xl mx-auto flex items-center h-10">
            <div class="bg-red-800 px-4 h-full flex items-center font-bold text-sm whitespace-nowrap">
                SON DAKİKA
            </div>

            <marquee behavior="scroll" direction="left" scrollamount="5" class="text-sm font-semibold px-4">
                🔥 Memur alımı ilanları güncellendi —
                🔥 KPSS tercih süreci başladı —
                🔥 Yeni personel alım ilanları yayımlandı —
                🔥 Akademik ilanlarda yeni kadrolar açıldı —
                🔥 Son dakika haberlerini takip edin
            </marquee>
        </div>
    </div>

    {{-- FİNANS + HAVA DURUMU --}}
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 h-9 flex items-center justify-between text-sm">

            <div class="flex items-center gap-6 overflow-x-auto text-sm font-semibold whitespace-nowrap">
                <span><b>Dolar:</b> {{ $market['dolar'] ?? '45.35' }} <span class="text-green-600">%0.24 ↑</span></span>
                <span><b>Euro:</b> {{ $market['euro'] ?? '53.52' }} <span class="text-green-600">%0.56 ↑</span></span>
                <span><b>Altın:</b> {{ $market['altin'] ?? '6875.62' }} <span class="text-green-600">%0.87 ↑</span></span>
                <span><b>BIST:</b> {{ $market['bist'] ?? '15062.65' }} <span class="text-green-600">%0.15 ↑</span></span>
                <span><b>BTC:</b> {{ $market['btc'] ?? '81256' }} <span class="text-green-600">%0.48 ↑</span></span>
            </div>

            <div class="hidden md:block whitespace-nowrap">
                {{ $weather['city'] ?? 'İstanbul' }},
                {{ $weather['status'] ?? 'Açık' }}
                •
                <b>{{ $weather['temp'] ?? 19 }}°</b>
            </div>

        </div>
    </div>

    {{-- HEADER --}}
    @isset($header)
        <header class="bg-white border-b">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- SAYFA --}}
    <main>
        {{ $slot }}
    </main>

</div>

</body>
</html>
