<x-guest-layout>
    @php
        $siteSetting = \App\Models\SiteSetting::first();
    @endphp

    <div class="min-h-screen bg-slate-100 flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            <div class="text-center mb-6">
                <a href="/" class="inline-block text-3xl font-black text-blue-700">
                    {{ $siteSetting?->site_name ?? config('app.name') }}
                </a>

                <p class="text-sm text-slate-500 mt-2">
                    Yeni hesap oluşturun ve topluluğa katılın.
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">

                <div class="bg-slate-950 text-white px-6 py-5">
                    <h1 class="text-2xl font-black">
                        Üye Ol
                    </h1>

                    <p class="text-sm text-slate-300 mt-1">
                        Haber yorumları, bildirimler ve kullanıcı özellikleri için kayıt olun.
                    </p>
                </div>

                <div class="p-6">

                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <div>
                            <x-input-label for="name" value="Ad Soyad" />

                            <x-text-input
                                id="name"
                                class="block mt-2 w-full rounded-xl"
                                type="text"
                                name="name"
                                :value="old('name')"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Ad Soyad"
                            />

                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="E-posta Adresi" />

                            <x-text-input
                                id="email"
                                class="block mt-2 w-full rounded-xl"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autocomplete="username"
                                placeholder="ornek@mail.com"
                            />

                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" value="Şifre" />

                            <x-text-input
                                id="password"
                                class="block mt-2 w-full rounded-xl"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />

                            <p class="text-xs text-slate-500 mt-2">
                                En az 8 karakter güçlü şifre önerilir.
                            </p>

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Şifre Tekrar" />

                            <x-text-input
                                id="password_confirmation"
                                class="block mt-2 w-full rounded-xl"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="••••••••"
                            />

                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-600">
                            Hesap oluşturarak kullanım şartlarını ve topluluk kurallarını kabul etmiş olursunuz.
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-xl font-black transition"
                        >
                            Hesap Oluştur
                        </button>

                    </form>

                    <div class="mt-6 text-center text-sm text-slate-600">
                        Zaten hesabınız var mı?

                        <a href="{{ route('login') }}" class="font-black text-blue-700 hover:underline">
                            Giriş yap
                        </a>
                    </div>

                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-500">
                Güvenli kayıt sistemi aktif • Spam ve bot koruması uygulanır.
            </div>

        </div>
    </div>
</x-guest-layout>
