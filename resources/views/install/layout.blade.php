<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Argnest Haber-Ilan Portal CMS Kurulum</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { color-scheme: dark; }

        .install-form input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]) {
            min-height: 48px;
            width: 100%;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            background: #0f172a;
            color: #ffffff;
            padding: 0.75rem 1rem;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03), 0 12px 28px rgba(2, 6, 23, 0.2);
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
        }

        .install-form input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"])::placeholder {
            color: #94a3b8;
            opacity: 1;
        }

        .install-form input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]):focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .18), 0 14px 32px rgba(2, 6, 23, 0.24);
        }

        .install-form input:-webkit-autofill,
        .install-form input:-webkit-autofill:hover,
        .install-form input:-webkit-autofill:focus {
            -webkit-text-fill-color: #ffffff;
            caret-color: #ffffff;
            box-shadow: 0 0 0 1000px #0f172a inset, 0 0 0 3px rgba(239, 68, 68, .18);
            border-color: #334155;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_-10%,rgba(239,68,68,0.26),transparent_30%),radial-gradient(circle_at_12%_30%,rgba(127,29,29,0.34),transparent_28%),radial-gradient(circle_at_90%_18%,rgba(14,165,233,0.12),transparent_26%),linear-gradient(135deg,#020617_0%,#0f172a_48%,#020617_100%)]"></div>
        <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-red-300/70 to-transparent"></div>

        <section class="relative w-full max-w-5xl overflow-hidden rounded-2xl border border-white/10 bg-slate-950/80 shadow-2xl shadow-black/50 backdrop-blur-2xl">
            <div class="grid min-h-[620px] lg:grid-cols-[0.9fr_1.1fr]">
                <aside class="border-b border-white/10 bg-white/[0.03] p-6 sm:p-8 lg:border-b-0 lg:border-r">
                    <div class="flex h-full flex-col justify-between gap-10">
                        <div>
                            <div class="text-xs font-black uppercase tracking-[0.28em] text-red-200">Argnest CMS</div>
                            <h1 class="mt-4 text-3xl font-black leading-tight text-white sm:text-4xl">Haber ve ilan portal kurulumu</h1>
                            <p class="mt-4 max-w-md text-sm leading-6 text-zinc-400">
                                Temiz custom installer sadece ilk kurulumda calisir. Kurulum kilidi olustuktan sonra install rotalari 404 doner.
                            </p>
                        </div>

                        <div class="grid gap-3 text-sm">
                            <div class="flex items-center justify-between border border-white/10 bg-black/20 px-4 py-3">
                                <span class="text-zinc-400">Kaynak</span>
                                <span class="font-bold text-zinc-100">backup.sql</span>
                            </div>
                            <div class="flex items-center justify-between border border-white/10 bg-black/20 px-4 py-3">
                                <span class="text-zinc-400">Admin</span>
                                <span class="font-bold text-zinc-100">/admin/login</span>
                            </div>
                            <div class="flex items-center justify-between border border-white/10 bg-black/20 px-4 py-3">
                                <span class="text-zinc-400">Kilit</span>
                                <span class="font-bold text-zinc-100">installed.lock</span>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="p-6 sm:p-8 lg:p-10">
                    @yield('content')
                </div>
            </div>
        </section>
    </main>
</body>
</html>
