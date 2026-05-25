<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Argnest Portal Kurulum</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-950">
    @php
        $progress = (int) round(($step / count($steps)) * 100);
        $input = 'mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100';
        $label = 'block text-sm font-semibold text-slate-700';
    @endphp

    <main class="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-6 sm:py-10">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
            <div class="bg-slate-950 px-6 py-6 text-white sm:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-xs font-black uppercase tracking-[0.22em] text-blue-200">Argnest Haber-Ilan Portal CMS</div>
                        <h1 class="mt-2 text-2xl font-black sm:text-3xl">Kurulum Sihirbazi</h1>
                        <p class="mt-1 text-sm text-slate-300">Modern Haber, Ilan ve Topluluk Yonetim Sistemi</p>
                    </div>
                    <div class="rounded-xl border border-white/10 bg-white/10 px-4 py-3 text-sm">
                        <div class="text-slate-300">Surum</div>
                        <div class="font-black">{{ $version }}</div>
                    </div>
                </div>

                <div class="mt-6 h-2 overflow-hidden rounded-full bg-white/15">
                    <div class="h-full rounded-full bg-blue-400" style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <div class="grid gap-0 lg:grid-cols-[270px_1fr]">
                <aside class="border-b border-slate-200 bg-slate-50 p-5 lg:border-b-0 lg:border-r">
                    <ol class="space-y-2">
                        @foreach($steps as $number => $title)
                            <li>
                                <a href="{{ route('install', ['step' => $number]) }}"
                                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-bold {{ $number === $step ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-white' }}">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full {{ $number === $step ? 'bg-white text-blue-700' : 'bg-slate-200 text-slate-700' }}">{{ $number }}</span>
                                    <span>{{ $title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </aside>

                <section class="p-5 sm:p-8">
                    @if(session('status'))
                        <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <div class="font-black">Kontrol edilmesi gereken alanlar var.</div>
                            <ul class="mt-2 list-disc pl-5">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('install.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="step" value="{{ $step }}">

                        @if($step === 1)
                            <div class="space-y-4">
                                <h2 class="text-2xl font-black">Hos geldiniz</h2>
                                <p class="max-w-2xl text-slate-600">Bu sihirbaz Argnest Haber-Ilan Portal CMS kurulumunu guvenli adimlarla tamamlar. Demo icerik varsayilan olarak olusturulmaz.</p>
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <div class="text-sm font-black">Temiz Kurulum</div>
                                        <p class="mt-1 text-sm text-slate-500">Migration, temel ayarlar ve admin hesabi hazirlanir.</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <div class="text-sm font-black">Guvenli Kilit</div>
                                        <p class="mt-1 text-sm text-slate-500">Kurulum bitince install route tekrar acilmaz.</p>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <div class="text-sm font-black">Onboarding</div>
                                        <p class="mt-1 text-sm text-slate-500">Kurulum sonrasi baslangic merkezine yonlendirilirsiniz.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($step === 2)
                            <div class="space-y-4">
                                <h2 class="text-2xl font-black">Sistem Gereksinimleri</h2>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    @foreach($requirements as $requirement)
                                        <div class="flex items-center justify-between rounded-lg border border-slate-200 px-4 py-3">
                                            <span class="font-semibold">{{ $requirement['label'] }}</span>
                                            <span class="rounded-full px-2 py-1 text-xs font-black {{ $requirement['ok'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $requirement['ok'] ? 'OK' : 'Eksik' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($step === 3)
                            <div class="space-y-4">
                                <h2 class="text-2xl font-black">Veritabani Ayarlari</h2>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="{{ $label }}">DB_HOST<input name="db_host" value="{{ old('db_host', $data['db_host']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">DB_PORT<input name="db_port" value="{{ old('db_port', $data['db_port']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">DB_DATABASE<input name="db_database" value="{{ old('db_database', $data['db_database']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">DB_USERNAME<input name="db_username" value="{{ old('db_username', $data['db_username']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }} sm:col-span-2">DB_PASSWORD<input type="password" name="db_password" value="{{ old('db_password', $data['db_password']) }}" class="{{ $input }}" autocomplete="new-password"></label>
                                </div>
                            </div>
                        @elseif($step === 4)
                            <div class="space-y-4">
                                <h2 class="text-2xl font-black">Site Ayarlari</h2>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="{{ $label }}">Site adi<input name="site_name" value="{{ old('site_name', $data['site_name']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">Site URL<input name="site_url" value="{{ old('site_url', $data['site_url']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }} sm:col-span-2">Site aciklamasi<textarea name="site_description" rows="3" class="{{ $input }}">{{ old('site_description', $data['site_description']) }}</textarea></label>
                                    <label class="{{ $label }}">Varsayilan dil<input name="default_language" value="{{ old('default_language', $data['default_language']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">Timezone<input name="default_timezone" value="{{ old('default_timezone', $data['default_timezone']) }}" class="{{ $input }}"></label>
                                </div>
                            </div>
                        @elseif($step === 5)
                            <div class="space-y-4">
                                <h2 class="text-2xl font-black">Admin Kullanicisi</h2>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="{{ $label }}">Ad soyad<input name="admin_name" value="{{ old('admin_name', $data['admin_name']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">Kullanici adi<input name="admin_username" value="{{ old('admin_username', $data['admin_username']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }} sm:col-span-2">Email<input type="email" name="admin_email" value="{{ old('admin_email', $data['admin_email']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">Sifre<input type="password" name="admin_password" class="{{ $input }}" autocomplete="new-password"></label>
                                    <label class="{{ $label }}">Sifre tekrar<input type="password" name="admin_password_confirmation" class="{{ $input }}" autocomplete="new-password"></label>
                                </div>
                            </div>
                        @elseif($step === 6)
                            <div class="space-y-4">
                                <h2 class="text-2xl font-black">Mail Ayarlari</h2>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <label class="{{ $label }}">SMTP host<input name="mail_host" value="{{ old('mail_host', $data['mail_host']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">Port<input name="mail_port" value="{{ old('mail_port', $data['mail_port']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">Kullanici<input name="mail_username" value="{{ old('mail_username', $data['mail_username']) }}" class="{{ $input }}"></label>
                                    <label class="{{ $label }}">Sifre<input type="password" name="mail_password" value="{{ old('mail_password', $data['mail_password']) }}" class="{{ $input }}" autocomplete="new-password"></label>
                                    <label class="{{ $label }}">Encryption
                                        <select name="mail_encryption" class="{{ $input }}">
                                            @foreach(['' => 'Yok', 'tls' => 'TLS', 'ssl' => 'SSL'] as $value => $title)
                                                <option value="{{ $value }}" @selected(old('mail_encryption', $data['mail_encryption']) === $value)>{{ $title }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label class="{{ $label }}">From mail<input type="email" name="mail_from_address" value="{{ old('mail_from_address', $data['mail_from_address']) }}" class="{{ $input }}"></label>
                                </div>
                            </div>
                        @else
                            <div class="space-y-5">
                                <h2 class="text-2xl font-black">Kurulumu Tamamla</h2>
                                <p class="text-slate-600">Bu adim migrationlari calistirir, temel ayarlari olusturur, admin kullanicisini ekler ve install lock dosyasini yazar.</p>
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                    Sifreler loglanmaz. Demo icerik olusturulmaz. Kurulum tamamlandiktan sonra /install tekrar calismaz.
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <a href="{{ route('install', ['step' => max($step - 1, 1)]) }}" class="inline-flex justify-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-black text-slate-700 {{ $step === 1 ? 'pointer-events-none opacity-40' : '' }}">Geri</a>

                            <div class="flex flex-col gap-3 sm:flex-row">
                                @if($step === 3)
                                    <button type="submit" name="action" value="test-db" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-black text-blue-700">Baglantiyi test et</button>
                                @endif

                                @if($step === 7)
                                    <button type="submit" name="action" value="complete" class="rounded-lg bg-green-600 px-5 py-2 text-sm font-black text-white shadow hover:bg-green-700">Kurulumu tamamla</button>
                                @else
                                    <button type="submit" name="action" value="next" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-black text-white shadow hover:bg-blue-700">Devam et</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </section>
    </main>
</body>
</html>
