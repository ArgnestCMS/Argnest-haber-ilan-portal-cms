<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>E-posta Doğrulama</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

    @php
        $siteSetting = \App\Models\SiteSetting::first();
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-slate-900 flex items-center justify-center px-4 py-10">

        <div class="w-full max-w-lg">

            <div class="text-center mb-8">

                <a href="/" class="inline-flex items-center justify-center text-4xl font-black text-white tracking-tight">
                    {{ $siteSetting?->site_name ?? config('app.name') }}
                </a>

                <p class="text-sm text-blue-200 mt-3">
                    Güvenli üyelik doğrulama ekranı
                </p>

            </div>

            <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-white/20">

                <div class="bg-gradient-to-r from-blue-700 to-slate-950 text-white px-8 py-7">

                    <div class="flex items-center gap-4">

                        <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-3xl">
                            ✉️
                        </div>

                        <div>
                            <h1 class="text-3xl font-black">
                                E-posta Doğrulama
                            </h1>

                            <p class="text-sm text-blue-100 mt-1">
                                Hesabınızı kullanmaya başlamak için mail adresinizi doğrulayın.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="p-8">

                    <div class="rounded-2xl bg-blue-50 border border-blue-100 p-5 text-blue-900 leading-7 text-sm">

                        <div class="font-black text-lg mb-2">
                            Üyeliğiniz başarıyla oluşturuldu 🎉
                        </div>

                        <p>
                            Size bir doğrulama bağlantısı gönderdik. Hesabınızı aktifleştirmek için lütfen e-posta kutunuzu kontrol edin ve gelen bağlantıya tıklayın.
                        </p>

                        <p class="mt-3 text-blue-700">
                            Mail gelmediyse spam/gereksiz klasörünü de kontrol edebilirsiniz.
                        </p>

                    </div>

                    @if (session('status') == 'verification-link-sent')

                        <div class="mt-5 rounded-2xl bg-green-50 border border-green-100 p-4 text-green-700 text-sm font-semibold">
                            ✅ Yeni doğrulama bağlantısı e-posta adresinize gönderildi.
                        </div>

                    @endif

                    <div class="mt-6 grid gap-3">

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf

                            <button
                                type="submit"
                                class="w-full bg-blue-700 hover:bg-blue-800 text-white py-4 rounded-2xl font-black transition shadow-lg shadow-blue-700/20"
                            >
                                Tekrar Doğrulama Maili Gönder
                            </button>
                        </form>

                        <a
                            href="/"
                            class="w-full text-center bg-slate-100 hover:bg-slate-200 text-slate-800 py-4 rounded-2xl font-black transition"
                        >
                            Siteye Dön
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-2xl font-black transition"
                            >
                                Çıkış Yap
                            </button>
                        </form>

                    </div>

                    <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-500 leading-6">
                        Güvenliğiniz için e-posta doğrulaması zorunludur. Doğrulama yapılmadan kullanıcı paneli ve yorum sistemi tam olarak kullanılamaz.
                    </div>

                </div>

            </div>

            <div class="mt-6 text-center text-xs text-blue-200">
                {{ $siteSetting?->site_name ?? config('app.name') }} • Güvenli üyelik sistemi
            </div>

        </div>

    </div>

</body>
</html>
