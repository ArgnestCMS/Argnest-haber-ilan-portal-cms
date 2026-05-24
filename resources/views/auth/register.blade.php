<x-guest-layout>
    @php
        $siteSetting = \App\Models\SiteSetting::first();
        $siteName = $siteSetting?->site_name ?? config('app.name');
        $registrationDisabled = $registrationDisabled ?? false;
        $recaptchaEnabled = $recaptchaEnabled ?? ((bool) config('services.recaptcha.enabled', true) && (bool) config('security.captcha_required', true) && filled(config('services.recaptcha.site_key')) && filled(config('services.recaptcha.secret_key')));
    @endphp

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <div class="min-h-screen bg-slate-100">
        <div class="bg-[#0878c9] text-white">
            <div class="mx-auto flex min-h-14 max-w-[980px] flex-wrap items-center justify-between gap-3 px-4 py-2.5">
                <a href="/" class="flex min-w-0 items-center gap-3 truncate text-2xl font-black">
                    @if($siteSetting?->logo)
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white p-1.5">
                            <img src="{{ asset('storage/' . $siteSetting->logo) }}" alt="{{ $siteName }}" class="max-h-7 max-w-7 object-contain">
                        </span>
                    @endif
                    <span class="truncate">{{ $siteName }}</span>
                </a>

                <div class="flex flex-wrap items-center justify-end gap-2 text-xs font-bold sm:gap-3 sm:text-sm">
                    <a href="/" class="hover:text-slate-200">Anasayfa</a>
                    <a href="/haberler" class="hover:text-slate-200">Haberler</a>
                    <a href="/ilanlar" class="hover:text-slate-200">İlanlar</a>
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 leading-none">
                        Giriş Yap
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-red-600 text-white">
            <div class="mx-auto flex h-9 max-w-[980px] items-center px-4 text-xs font-bold sm:text-sm">
                <span class="mr-3 flex h-full shrink-0 items-center bg-red-800 px-3">ÜYELİK</span>
                <marquee scrollamount="5">
                    Yeni üyelik, bildirimler, yorumlar ve kişisel panel özellikleri için hesap oluşturun
                </marquee>
            </div>
        </div>

        <div class="mx-auto max-w-5xl px-4 py-12 lg:py-14">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="order-2 flex h-auto flex-col justify-between rounded-3xl bg-slate-950 p-6 text-white shadow-xl lg:order-1 lg:p-7">
                    <div>
                        <span class="mb-4 inline-flex rounded-full border border-blue-400/30 bg-blue-500/20 px-3 py-1 text-xs font-bold text-blue-200">
                            Güvenli Üyelik
                        </span>

                        <h1 class="text-2xl font-black leading-tight sm:text-3xl">
                            Haberleri, ilanları ve topluluk deneyimini hesabınızla takip edin.
                        </h1>

                        <p class="mt-4 text-sm leading-6 text-slate-300">
                            Üyeliğinizle yorum yapabilir, bildirimleri yönetebilir ve size özel paneli kullanabilirsiniz.
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-2.5">
                        <div class="min-w-0 rounded-2xl bg-white/10 p-3">
                            <div class="mb-1 text-xl">🧾</div>
                            <div class="text-sm font-black">Profil</div>
                            <div class="mt-1 text-[11px] leading-4 text-slate-300">Kişisel alan</div>
                        </div>

                        <div class="min-w-0 rounded-2xl bg-white/10 p-3">
                            <div class="mb-1 text-xl">🔔</div>
                            <div class="text-sm font-black">Bildirim</div>
                            <div class="mt-1 text-[11px] leading-4 text-slate-300">Hesap akışı</div>
                        </div>

                        <div class="min-w-0 rounded-2xl bg-white/10 p-3">
                            <div class="mb-1 text-xl">🛡️</div>
                            <div class="text-sm font-black">Güvenli</div>
                            <div class="mt-1 text-[11px] leading-4 text-slate-300">Spam koruma</div>
                        </div>
                    </div>
                </div>

                <div class="order-1 h-auto overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl lg:order-2">
                    <div class="bg-slate-950 px-6 py-5 text-white">
                        <h2 class="text-2xl font-black">Üye Ol</h2>
                        <p class="mt-1 text-sm text-slate-300">
                            Hesabınızı oluşturup güvenli şekilde devam edin.
                        </p>
                    </div>

                    <div class="p-6 sm:p-7">
                        @if($registrationDisabled)
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-900">
                                <div class="text-lg font-black">Yeni üyelikler geçici olarak kapalıdır.</div>
                                <p class="mt-2 text-sm leading-6">
                                    Kayıt sistemi yönetici tarafından kapatılmış. Mevcut hesabınız varsa giriş yapabilirsiniz.
                                </p>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <a href="{{ route('login') }}" class="rounded-xl bg-blue-700 px-4 py-3 text-center font-black text-white transition hover:bg-blue-800">
                                    Giriş Yap
                                </a>
                                <a href="/" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-center font-black text-slate-700 transition hover:bg-slate-100">
                                    Anasayfa
                                </a>
                            </div>
                        @else
                            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                                @csrf

                                <div>
                                    <x-input-label for="name" value="Ad Soyad" />
                                    <x-text-input id="name" class="mt-2 block h-11 w-full rounded-xl px-4" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ad Soyad" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" value="E-posta Adresi" />
                                    <x-text-input id="email" class="mt-2 block h-11 w-full rounded-xl px-4" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="ornek@mail.com" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" value="Şifre" />
                                    <x-text-input id="password" class="mt-2 block h-11 w-full rounded-xl px-4" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                                    <p class="mt-2 text-xs text-slate-500">En az 8 karakter güçlü şifre önerilir.</p>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password_confirmation" value="Şifre Tekrar" />
                                    <x-text-input id="password_confirmation" class="mt-2 block h-11 w-full rounded-xl px-4" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>

                                @if($recaptchaEnabled)
                                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-3">
                                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                                    </div>
                                    <x-input-error :messages="$errors->get('g-recaptcha-response')" class="mt-2" />
                                @elseif(! app()->isProduction())
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs font-semibold text-amber-800">
                                        reCAPTCHA anahtarları tanımlı olmadığı için geliştirme ortamında doğrulama atlandı.
                                    </div>
                                @else
                                    <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-xs font-semibold text-red-700">
                                        Kayıt güvenlik doğrulaması yapılandırılmamış. Lütfen site yöneticisiyle iletişime geçin.
                                    </div>
                                @endif

                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                                    Hesap oluşturarak kullanım şartlarını ve topluluk kurallarını kabul etmiş olursunuz.
                                </div>

                                <button type="submit" class="w-full rounded-xl bg-blue-700 py-3 font-black text-white transition hover:bg-blue-800">
                                    Kayıt Ol
                                </button>
                            </form>

                            <div class="mt-6 text-center text-sm text-slate-600">
                                Zaten hesabınız var mı?
                                <a href="{{ route('login') }}" class="font-black text-blue-700 hover:underline">
                                    Giriş yap
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
