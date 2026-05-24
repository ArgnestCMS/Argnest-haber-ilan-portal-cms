<x-guest-layout>
    @php
        $siteSetting = \App\Models\SiteSetting::first();

        $latestNews = \App\Models\News::latest()->take(3)->get();

        $latestAnnouncements = \App\Models\Announcement::latest()->take(3)->get();
    @endphp

    <div class="min-h-screen bg-slate-100">

        <div class="bg-[#0878c9] text-white">
            <div class="mx-auto flex min-h-14 max-w-[980px] flex-wrap items-center justify-between gap-3 px-4 py-2.5">
                <a href="/" class="min-w-0 truncate text-2xl font-black">
                    {{ $siteSetting?->site_name ?? config('app.name') }}
                </a>

                <div class="flex flex-wrap items-center justify-end gap-2 text-xs font-bold sm:gap-3 sm:text-sm">
                    <a href="/" class="hover:text-slate-200">Anasayfa</a>
                    <a href="/haberler" class="hover:text-slate-200">Haberler</a>
                    <a href="/ilanlar" class="hover:text-slate-200">İlanlar</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 leading-none">
                        Üye Ol
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-red-600 text-white">
            <div class="mx-auto flex h-9 max-w-[980px] items-center px-4 text-xs font-bold sm:text-sm">
                <span class="mr-3 flex h-full shrink-0 items-center bg-red-800 px-3">SON DAKİKA</span>
                <marquee scrollamount="5">
                    🔥 Memur alımı ilanları güncellendi — 🔥 KPSS tercih süreci başladı — 🔥 Yeni ilanlar yayında
                </marquee>
            </div>
        </div>

        <div class="mx-auto max-w-5xl px-4 py-12 lg:py-14">

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <div class="order-2 h-auto flex flex-col justify-between rounded-3xl bg-slate-950 p-6 text-white shadow-xl lg:order-1 lg:p-7">
                    <div>
                        <span class="mb-4 inline-flex rounded-full border border-blue-400/30 bg-blue-500/20 px-3 py-1 text-xs font-bold text-blue-200">
                            Güvenli Üye Girişi
                        </span>

                        <h1 class="text-2xl font-black leading-tight sm:text-3xl">
                            Haberleri, ilanları ve bildirimleri tek panelden takip edin.
                        </h1>

                        <p class="mt-4 text-sm leading-6 text-slate-300">
                            Üye olarak yorumlarınızı, bildirimlerinizi ve profil bilgilerinizi yönetebilirsiniz.
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-2.5">
                        <div class="min-w-0 rounded-2xl bg-white/10 p-3">
                            <div class="mb-1 text-xl">📰</div>
                            <div class="text-sm font-black">Haberler</div>
                            <div class="mt-1 text-[11px] leading-4 text-slate-300">Güncel gelişmeler</div>
                        </div>

                        <div class="min-w-0 rounded-2xl bg-white/10 p-3">
                            <div class="mb-1 text-xl">📢</div>
                            <div class="text-sm font-black">İlanlar</div>
                            <div class="mt-1 text-[11px] leading-4 text-slate-300">Kamu ilanları</div>
                        </div>

                        <div class="min-w-0 rounded-2xl bg-white/10 p-3">
                            <div class="mb-1 text-xl">🔔</div>
                            <div class="text-sm font-black">Bildirim</div>
                            <div class="mt-1 text-[11px] leading-4 text-slate-300">Hesap akışı</div>
                        </div>
                    </div>
                </div>

                <div class="order-1 h-auto overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl lg:order-2">
                    <div class="bg-slate-950 px-6 py-5 text-white">
                        <h2 class="text-2xl font-black">Üye Girişi</h2>
                        <p class="mt-1 text-sm text-slate-300">
                            Hesabınıza güvenli giriş yapın.
                        </p>
                    </div>

                    <div class="p-6 sm:p-7">
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div>
                                <x-input-label for="email" value="E-posta Adresi" />
                                <x-text-input id="email" class="mt-2 block h-11 w-full rounded-xl px-4" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ornek@mail.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" value="Şifre" />
                                <x-text-input id="password" class="mt-2 block h-11 w-full rounded-xl px-4" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">
                                <label for="remember_me" class="inline-flex items-center gap-2">
                                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-500" name="remember">
                                    <span class="text-slate-600">Beni hatırla</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-sm font-bold text-blue-700 hover:underline" href="{{ route('password.request') }}">
                                        Şifremi unuttum
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="w-full rounded-xl bg-blue-700 py-3 font-black text-white transition hover:bg-blue-800">
                                Giriş Yap
                            </button>
                        </form>

                        <div class="mt-6 text-center text-sm text-slate-600">
                            Hesabınız yok mu?
                            <a href="{{ route('register') }}" class="font-black text-blue-700 hover:underline">
                                Hemen üye olun
                            </a>
                        </div>

                        <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4 text-center text-xs text-slate-500">
                            Güvenli giriş sistemi aktif • IP ve cihaz kayıtları tutulur.
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2">

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b px-5 py-3.5 text-lg font-black">
                        Son Haberler
                    </div>

                    <div class="divide-y">
                        @forelse($latestNews as $news)
                            <a href="/haber/{{ $news->slug }}" class="block p-4 hover:bg-slate-50">
                                <h3 class="font-black text-slate-900">{{ $news->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $news->summary }}</p>
                            </a>
                        @empty
                            <div class="p-4 text-slate-500">Henüz haber bulunmuyor.</div>
                        @endforelse
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b px-5 py-3.5 text-lg font-black">
                        Son İlanlar
                    </div>

                    <div class="divide-y">
                        @forelse($latestAnnouncements as $announcement)
                            <a href="/ilan/{{ $announcement->slug }}" class="block p-4 hover:bg-slate-50">
                                <h3 class="font-black text-slate-900">{{ $announcement->title }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $announcement->summary }}</p>
                            </a>
                        @empty
                            <div class="p-4 text-slate-500">Henüz ilan bulunmuyor.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-guest-layout>
