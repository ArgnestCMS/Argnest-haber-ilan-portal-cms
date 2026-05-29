@extends('install.layout')

@section('content')
    @php
        $input = 'mt-2 block min-h-12 w-full rounded-xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white shadow-lg shadow-slate-950/30 outline-none transition placeholder:text-slate-400 focus:border-red-500 focus:ring-4 focus:ring-red-500/20';
        $label = 'block text-sm font-bold text-slate-200';
    @endphp

    <div class="flex h-full flex-col justify-center">
        <div class="mb-8">
            <div class="text-xs font-black uppercase tracking-[0.2em] text-red-200">Veritabani</div>
            <h2 class="mt-3 text-3xl font-black text-white">Baglanti bilgileri</h2>
            <p class="mt-3 max-w-xl text-sm leading-6 text-slate-400">
                Bilgiler kaydedildikten sonra baglanti test edilir. Import basarili olursa kurulum kilidi yazilir.
            </p>
        </div>

        @if(! $backupExists)
            <div class="mb-5 border border-amber-300/30 bg-amber-300/10 px-4 py-3 text-sm font-semibold text-amber-100">
                database/install/backup.sql bulunamadi. Devam etmeden once SQL yedegini bu konuma ekleyin.
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
                <div class="font-black">Kurulum tamamlanamadi.</div>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <div class="mt-3 text-xs text-red-200">Detaylar storage/logs/install.log dosyasina yazildi.</div>
            </div>
        @endif

        <form method="POST" action="{{ route('install.run') }}" class="install-form space-y-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="{{ $label }}">
                    DB_HOST
                    <input name="DB_HOST" value="{{ old('DB_HOST', $defaults['DB_HOST'] ?? 'localhost') }}" class="{{ $input }}" placeholder="localhost" required>
                </label>

                <label class="{{ $label }}">
                    DB_PORT
                    <input name="DB_PORT" type="number" min="1" max="65535" value="{{ old('DB_PORT', $defaults['DB_PORT'] ?? '3306') }}" class="{{ $input }}" placeholder="3306" required>
                </label>

                <label class="{{ $label }}">
                    DB_DATABASE
                    <input name="DB_DATABASE" value="{{ old('DB_DATABASE', $defaults['DB_DATABASE'] ?? '') }}" class="{{ $input }}" placeholder="database_name" required>
                </label>

                <label class="{{ $label }}">
                    DB_USERNAME
                    <input name="DB_USERNAME" value="{{ old('DB_USERNAME', $defaults['DB_USERNAME'] ?? '') }}" class="{{ $input }}" placeholder="database_user" required>
                </label>

                <label class="{{ $label }} sm:col-span-2">
                    DB_PASSWORD
                    <input name="DB_PASSWORD" type="password" value="{{ old('DB_PASSWORD', $defaults['DB_PASSWORD'] ?? '') }}" class="{{ $input }}" autocomplete="new-password" placeholder="Sifre">
                </label>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-white/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ route('install') }}" class="inline-flex items-center justify-center rounded-xl border border-white/10 px-5 py-3 text-sm font-black text-slate-200 transition hover:bg-white/5">
                    Geri
                </a>

                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-red-500 px-5 py-3 text-sm font-black text-white shadow-lg shadow-red-950/40 transition hover:bg-red-400">
                    Kurulumu tamamla
                </button>
            </div>
        </form>
    </div>
@endsection
