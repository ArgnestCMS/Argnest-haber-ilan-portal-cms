<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Kurulum Tamamlandi - Argnest Haber-Ilan Portal CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { color-scheme: dark; }

        body {
            background: #020617;
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px) scale(.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes check-draw {
            from { stroke-dashoffset: 56; }
            to { stroke-dashoffset: 0; }
        }

        @keyframes ring-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, .32), 0 0 70px rgba(16, 185, 129, .24); }
            50% { box-shadow: 0 0 0 18px rgba(16, 185, 129, 0), 0 0 95px rgba(16, 185, 129, .34); }
        }

        @keyframes progress-fill {
            from { width: 0; }
            to { width: 100%; }
        }

        .enter { animation: fade-up .7s cubic-bezier(.2, .8, .2, 1) both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
        .delay-3 { animation-delay: .24s; }
        .success-ring { animation: ring-pulse 2.8s ease-in-out infinite; }
        .check-path { stroke-dasharray: 56; stroke-dashoffset: 56; animation: check-draw .7s .26s ease-out forwards; }
        .progress-bar { animation: progress-fill 1.1s .22s cubic-bezier(.2, .8, .2, 1) both; }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_-10%,rgba(239,68,68,0.34),transparent_30%),radial-gradient(circle_at_12%_30%,rgba(127,29,29,0.48),transparent_28%),radial-gradient(circle_at_90%_18%,rgba(14,165,233,0.14),transparent_26%),linear-gradient(135deg,#020617_0%,#0f172a_46%,#020617_100%)]"></div>
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-300/70 to-transparent"></div>
        <div class="absolute left-1/2 top-0 h-72 w-72 -translate-x-1/2 rounded-full bg-red-500/20 blur-3xl"></div>

        <section class="relative mx-auto flex min-h-screen w-full max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full gap-6 lg:grid-cols-[1fr_420px] lg:items-stretch">
                <div class="enter overflow-hidden rounded-2xl border border-white/10 bg-white/[0.06] shadow-2xl shadow-black/50 backdrop-blur-2xl">
                    <div class="border-b border-white/10 bg-black/20 px-6 py-5 sm:px-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs font-black uppercase tracking-[0.3em] text-red-200">Argnest Haber-Ilan Portal CMS</div>
                                <div class="mt-2 text-sm font-semibold text-slate-400">Premium Installer</div>
                            </div>
                            <div class="rounded-full border border-emerald-300/30 bg-emerald-400/10 px-4 py-2 text-xs font-black uppercase tracking-[0.18em] text-emerald-200">
                                100% tamamlandi
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-8 sm:px-8 lg:px-10 lg:py-10">
                        <div class="flex flex-col gap-7 lg:flex-row lg:items-start">
                            <div class="success-ring flex h-24 w-24 shrink-0 items-center justify-center rounded-full border border-emerald-300/40 bg-emerald-300/15">
                                <svg class="h-14 w-14 text-emerald-200" viewBox="0 0 56 56" fill="none" aria-hidden="true">
                                    <circle cx="28" cy="28" r="25" stroke="currentColor" stroke-width="2" opacity=".24" />
                                    <path class="check-path" d="M16 29.5 24.5 38 41 19" stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h1 class="text-4xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                                    Kurulum basariyla tamamlandi
                                </h1>
                                <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300">
                                    Sistem dosyalari hazirlandi, SQL yedegi import edildi ve kurulum kilidi olusturuldu. Artik panel ve site kullanima hazir.
                                </p>
                            </div>
                        </div>

                        <div class="delay-1 enter mt-9 rounded-xl border border-white/10 bg-slate-950/55 p-5 shadow-xl shadow-black/30">
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div class="text-sm font-black text-white">Kurulum ilerlemesi</div>
                                <div class="text-sm font-black text-emerald-200">Tamamlandi</div>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-white/10">
                                <div class="progress-bar h-full rounded-full bg-gradient-to-r from-emerald-400 via-cyan-300 to-red-300"></div>
                            </div>

                            <div class="mt-5 grid gap-3 md:grid-cols-3">
                                <div class="rounded-lg border border-emerald-300/20 bg-emerald-400/10 p-4">
                                    <div class="text-xl font-black text-white">01</div>
                                    <div class="mt-2 text-sm font-bold text-emerald-100">Veritabani baglantisi basarili</div>
                                </div>
                                <div class="rounded-lg border border-cyan-300/20 bg-cyan-400/10 p-4">
                                    <div class="text-xl font-black text-white">02</div>
                                    <div class="mt-2 text-sm font-bold text-cyan-100">SQL import tamamlandi</div>
                                </div>
                                <div class="rounded-lg border border-red-300/25 bg-red-400/10 p-4">
                                    <div class="text-xl font-black text-white">03</div>
                                    <div class="mt-2 text-sm font-bold text-red-100">Kurulum kilidi olusturuldu</div>
                                </div>
                            </div>
                        </div>

                        <div class="delay-2 enter mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-white/10 bg-slate-950/45 p-5">
                                <div class="text-xs font-black uppercase tracking-[0.22em] text-slate-500">Veritabani</div>
                                <div class="mt-2 truncate text-2xl font-black text-white">{{ $database ?: 'Hazir' }}</div>
                            </div>
                            <div class="rounded-xl border border-white/10 bg-slate-950/45 p-5">
                                <div class="text-xs font-black uppercase tracking-[0.22em] text-slate-500">Import edilen ifade</div>
                                <div class="mt-2 text-2xl font-black text-white">{{ $importedStatements }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="delay-2 enter rounded-2xl border border-red-400/60 bg-red-950/45 p-6 shadow-2xl shadow-red-950/50 backdrop-blur-2xl">
                    <div class="rounded-xl border border-red-300/25 bg-black/25 p-5 shadow-xl shadow-red-950/30">
                        <div class="text-xs font-black uppercase tracking-[0.28em] text-red-200">Admin bilgileri</div>
                        <h2 class="mt-3 text-3xl font-black leading-tight text-white">Panel giris hesabi</h2>
                        <p class="mt-3 text-sm font-bold leading-6 text-red-100">Bu bilgileri degistirmeniz onerilir.</p>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-xl border border-red-300/30 bg-slate-950/70 p-5 shadow-lg shadow-red-950/30">
                            <div class="text-xs font-black uppercase tracking-[0.24em] text-red-200">E-posta</div>
                            <a href="mailto:demo@gmail.com" class="mt-3 block break-all text-3xl font-black text-white">
                                demo@gmail.com
                            </a>
                        </div>

                        <div class="rounded-xl border border-red-300/30 bg-slate-950/70 p-5 shadow-lg shadow-red-950/30">
                            <div class="text-xs font-black uppercase tracking-[0.24em] text-red-200">Sifre</div>
                            <div class="mt-3 flex flex-col gap-3">
                                <div class="flex items-center justify-between gap-3 rounded-lg border border-red-300/20 bg-black/30 px-4 py-3">
                                    <code id="admin-password" class="text-4xl font-black tracking-wide text-white">123456</code>
                                    <button
                                        type="button"
                                        data-copy-password
                                        class="shrink-0 rounded-md border border-red-200/30 bg-red-500 px-4 py-2 text-sm font-black text-white shadow-lg shadow-red-950/40 transition hover:bg-red-400"
                                    >
                                        Kopyala
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="delay-3 enter mt-6 grid gap-3">
                        <a href="{{ $adminUrl ?? '/admin/login' }}" class="inline-flex items-center justify-center rounded-lg bg-red-500 px-5 py-4 text-sm font-black text-white shadow-xl shadow-red-950/50 transition hover:bg-red-400">
                            Admin Paneline Git
                        </a>
                        <a href="{{ $siteUrl ?? url('/') }}" class="inline-flex items-center justify-center rounded-lg border border-white/15 bg-white/10 px-5 py-4 text-sm font-black text-white shadow-xl shadow-black/30 transition hover:bg-white/15">
                            Siteye Git
                        </a>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <script>
        document.querySelector('[data-copy-password]')?.addEventListener('click', async (event) => {
            const button = event.currentTarget;
            const password = document.getElementById('admin-password')?.textContent?.trim() || '123456';

            try {
                await navigator.clipboard.writeText(password);
                button.textContent = 'Kopyalandi';
            } catch (error) {
                button.textContent = '123456';
            }

            setTimeout(() => {
                button.textContent = 'Kopyala';
            }, 1800);
        });
    </script>
</body>
</html>
