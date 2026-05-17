<x-guest-layout>
    @php
        $siteSetting = \App\Models\SiteSetting::first();

        $latestNews = \App\Models\News::latest()->take(3)->get();

        $latestAnnouncements = \App\Models\Announcement::latest()->take(3)->get();
    @endphp

    <div class="min-h-screen bg-slate-100">

        <div class="bg-[#0878c9] text-white">
            <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
                <a href="/" class="text-3xl font-black">
                    {{ $siteSetting?->site_name ?? 'ilanhaber.net' }}
                </a>

                <div class="flex items-center gap-5 text-sm font-bold">
                    <a href="/" class="hover:text-slate-200">Anasayfa</a>
                    <a href="/haberler" class="hover:text-slate-200">Haberler</a>
                    <a href="/ilanlar" class="hover:text-slate-200">İlanlar</a>
                    <a href="{{ route('register') }}" class="bg-slate-900 px-4 py-2 rounded-lg">
                        Üye Ol
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-red-600 text-white">
            <div class="max-w-7xl mx-auto px-4 h-10 flex items-center text-sm font-bold">
                <span class="bg-red-800 px-4 h-full flex items-center mr-4">SON DAKİKA</span>
                <marquee scrollamount="5">
                    🔥 Memur alımı ilanları güncellendi — 🔥 KPSS tercih süreci başladı — 🔥 Yeni ilanlar yayında
                </marquee>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-10">

            <div class="grid lg:grid-cols-2 gap-8 items-stretch">

                <div class="bg-slate-950 text-white rounded-3xl shadow-xl p-8 flex flex-col justify-between">
                    <div>
                        <span class="inline-flex bg-blue-500/20 text-blue-200 border border-blue-400/30 px-3 py-1 rounded-full text-xs font-bold mb-5">
                            Güvenli Üye Girişi
                        </span>

                        <h1 class="text-4xl font-black leading-tight">
                            Haberleri, ilanları ve bildirimleri tek panelden takip edin.
                        </h1>

                        <p class="text-slate-300 mt-4 leading-7">
                            Üye olarak yorumlarınızı, bildirimlerinizi ve profil bilgilerinizi yönetebilirsiniz.
                        </p>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-4 mt-8">
                        <div class="bg-white/10 rounded-2xl p-4">
                            <div class="text-3xl mb-2">📰</div>
                            <div class="font-black">Haberler</div>
                            <div class="text-xs text-slate-300 mt-1">Güncel gelişmeler</div>
                        </div>

                        <div class="bg-white/10 rounded-2xl p-4">
                            <div class="text-3xl mb-2">📢</div>
                            <div class="font-black">İlanlar</div>
                            <div class="text-xs text-slate-300 mt-1">Kamu ve personel ilanları</div>
                        </div>

                        <div class="bg-white/10 rounded-2xl p-4">
                            <div class="text-3xl mb-2">🔔</div>
                            <div class="font-black">Bildirimler</div>
                            <div class="text-xs text-slate-300 mt-1">Hesap hareketleri</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
                    <div class="bg-slate-950 text-white px-8 py-6">
                        <h2 class="text-2xl font-black">Üye Girişi</h2>
                        <p class="text-sm text-slate-300 mt-1">
                            Hesabınıza güvenli giriş yapın.
                        </p>
                    </div>

                    <div class="p-8">
                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div>
                                <x-input-label for="email" value="E-posta Adresi" />
                                <x-text-input id="email" class="block mt-2 w-full rounded-xl" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="ornek@mail.com" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" value="Şifre" />
                                <x-text-input id="password" class="block mt-2 w-full rounded-xl" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-between">
                                <label for="remember_me" class="inline-flex items-center">
                                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-700 shadow-sm focus:ring-blue-500" name="remember">
                                    <span class="ms-2 text-sm text-slate-600">Beni hatırla</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-sm font-bold text-blue-700 hover:underline" href="{{ route('password.request') }}">
                                        Şifremi unuttum
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-xl font-black transition">
                                Giriş Yap
                            </button>
                        </form>

                        <div class="mt-6 text-center text-sm text-slate-600">
                            Hesabınız yok mu?
                            <a href="{{ route('register') }}" class="font-black text-blue-700 hover:underline">
                                Hemen üye olun
                            </a>
                        </div>

                        <div class="mt-5 bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs text-slate-500 text-center">
                            Güvenli giriş sistemi aktif • IP ve cihaz kayıtları tutulur.
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid lg:grid-cols-2 gap-8 mt-8">

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b font-black text-xl">
                        Son Haberler
                    </div>

                    <div class="divide-y">
                        @forelse($latestNews as $news)
                            <a href="/haber/{{ $news->slug }}" class="block p-5 hover:bg-slate-50">
                                <h3 class="font-black text-slate-900">{{ $news->title }}</h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $news->summary }}</p>
                            </a>
                        @empty
                            <div class="p-5 text-slate-500">Henüz haber bulunmuyor.</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b font-black text-xl">
                        Son İlanlar
                    </div>

                    <div class="divide-y">
                        @forelse($latestAnnouncements as $announcement)
                            <a href="/ilan/{{ $announcement->slug }}" class="block p-5 hover:bg-slate-50">
                                <h3 class="font-black text-slate-900">{{ $announcement->title }}</h3>
                                <p class="text-sm text-slate-500 mt-1">{{ $announcement->summary }}</p>
                            </a>
                        @empty
                            <div class="p-5 text-slate-500">Henüz ilan bulunmuyor.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-guest-layout>