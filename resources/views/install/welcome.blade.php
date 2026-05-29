@extends('install.layout')

@section('content')
    <div class="flex h-full flex-col justify-center">
        <div class="mb-8 inline-flex w-fit items-center gap-2 border border-cyan-300/20 bg-cyan-300/10 px-3 py-2 text-xs font-black uppercase tracking-[0.2em] text-cyan-100">
            Ilk kurulum
        </div>

        <h2 class="text-3xl font-black text-white sm:text-4xl">Kuruluma baslayin</h2>
        <p class="mt-4 max-w-xl text-sm leading-6 text-zinc-400">
            Installer .env veritabani bilgilerini gunceller, baglantiyi test eder ve sadece database/install/backup.sql dosyasini import eder. Migration ve seed calistirilmaz.
        </p>

        <div class="mt-8 grid gap-3 sm:grid-cols-3">
            <div class="border border-white/10 bg-white/[0.03] p-4">
                <div class="text-sm font-black text-white">1. Veritabani</div>
                <p class="mt-2 text-xs leading-5 text-zinc-500">Host, port, database ve kullanici bilgileri alinir.</p>
            </div>
            <div class="border border-white/10 bg-white/[0.03] p-4">
                <div class="text-sm font-black text-white">2. SQL import</div>
                <p class="mt-2 text-xs leading-5 text-zinc-500">backup.sql akimli okunur ve MySQL'e aktarilir.</p>
            </div>
            <div class="border border-white/10 bg-white/[0.03] p-4">
                <div class="text-sm font-black text-white">3. Kilit</div>
                <p class="mt-2 text-xs leading-5 text-zinc-500">storage/app/installed.lock olusturulur.</p>
            </div>
        </div>

        <div class="mt-10">
            <a href="{{ route('install.database') }}" class="inline-flex items-center justify-center rounded-md bg-cyan-300 px-5 py-3 text-sm font-black text-zinc-950 shadow-lg shadow-cyan-950/40 transition hover:bg-cyan-200">
                Devam et
            </a>
        </div>
    </div>
@endsection
