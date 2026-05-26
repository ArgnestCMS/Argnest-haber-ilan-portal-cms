<!DOCTYPE html>
<html lang="tr">

@php
    $siteSetting = \App\Models\SiteSetting::first();
    $seoSetting = \App\Models\SeoSetting::current();
    $themeSetting = \App\Models\ThemeSetting::current();
    $siteName = $siteSetting?->site_name ?? config('app.name');
    $homeAnnouncementBarEnabled = $siteSetting?->homeModuleEnabled('announcement_bar') ?? false;
    $homeBreakingNewsEnabled = $siteSetting?->homeModuleEnabled('breaking_news') ?? false;
    $homeMenuModules = [
        'news' => $siteSetting?->homeModuleEnabled('news') ?? true,
        'announcements' => $siteSetting?->homeModuleEnabled('announcements') ?? true,
        'forum' => ($siteSetting?->homeModuleEnabled('forum') ?? false) && (bool) ($siteSetting?->forum_enabled ?? true),
        'galleries' => $siteSetting?->homeModuleEnabled('galleries') ?? true,
        'videos' => $siteSetting?->homeModuleEnabled('videos') ?? true,
        'polls' => $siteSetting?->homeModuleEnabled('polls') ?? false,
    ];
    $mobileBottomColumns = $homeMenuModules['forum'] ? 'grid-cols-5' : 'grid-cols-4';
    $siteAnnouncements = $homeAnnouncementBarEnabled ? \App\Models\SiteAnnouncement::visible()
        ->orderBy('sort_order')
        ->orderByDesc('created_at')
        ->get() : collect();
    $headerSlots = \App\Models\HeaderSlot::visibleInHeader()->get();
    $breakingTickerItems = $homeBreakingNewsEnabled ? app(\App\Services\PortalCacheService::class)->remember('portal:layout:breaking-ticker', 'layout', fn () => collect()
    ->merge(
        \App\Models\News::published()
            ->where('is_breaking', true)
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($news) => [
                'label' => '⚡ ' . $news->title,
                'url' => url('/haber/' . $news->slug),
                'target' => '_self',
                'date' => $news->created_at,
            ])
     )
     ->merge(
        \App\Models\Announcement::query()
            ->where('is_active', true)
            ->where('is_breaking', true)
            ->where(function ($query) {
                $query->whereNull('publish_date')
                    ->orWhere('publish_date', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('deadline')
                    ->orWhere('deadline', '>=', now());
            })
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($announcement) => [
                'label' => '📢 ' . $announcement->title,
                'url' => url('/ilan/' . $announcement->slug),
                'target' => '_self',
                'date' => $announcement->created_at,
            ])
     )
     ->sortByDesc('date')
     ->take(15)
     ->values()) : collect();

    $rawMetaTitle = trim($__env->yieldContent(
        'title',
        $seoSetting?->site_title
            ?? $siteSetting?->seo_title
            ?? $siteName
    ));

    $metaTitle = $rawMetaTitle && ! \Illuminate\Support\Str::contains($rawMetaTitle, $siteName)
        ? $rawMetaTitle . ' | ' . $siteName
        : $rawMetaTitle;

    $metaDescription = trim($__env->yieldContent(
        'meta_description',
        $seoSetting?->site_description
            ?? $siteSetting?->seo_description
            ?? 'Güncel haberler, kamu ilanları, personel alımları ve son dakika gelişmeleri.'
    ));

    $metaKeywords = trim($__env->yieldContent(
        'meta_keywords',
        $seoSetting?->site_keywords
            ?? $siteSetting?->seo_keywords
            ?? 'haberler, ilanlar, memur alımı, kamu ilanları'
    ));

    $metaImage = trim($__env->yieldContent(
        'meta_image',
        $seoSetting?->og_image
            ? asset('storage/' . $seoSetting->og_image)
            : asset('default-og.jpg')
    ));

    $canonical = trim($__env->yieldContent(
        'canonical',
        $seoSetting?->canonical_url ?: url()->current()
    ));

    $robots = trim($__env->yieldContent(
        'robots',
        (($seoSetting?->robots_index ?? true) ? 'index' : 'noindex')
        . ', ' .
        (($seoSetting?->robots_follow ?? true) ? 'follow' : 'nofollow')
    ));

    $seoMeta = app(\App\Services\SeoService::class)->meta([
        'title' => $__env->yieldContent('title'),
        'description' => $__env->yieldContent('meta_description'),
        'keywords' => $__env->yieldContent('meta_keywords'),
        'image' => $__env->yieldContent('meta_image'),
        'canonical' => $__env->yieldContent('canonical'),
        'robots' => $__env->yieldContent('robots', $robots),
        'author' => $__env->yieldContent('author'),
        'language' => $__env->yieldContent('language'),
        'og_type' => $__env->yieldContent('og_type', 'website'),
    ]);

    $metaTitle = $seoMeta['title'];
    $metaDescription = $seoMeta['description'];
    $metaKeywords = $seoMeta['keywords'];
    $metaImage = $seoMeta['image'];
    $canonical = $seoMeta['canonical'];
    $robots = $seoMeta['robots'];

    $themeColors = [
        'primary' => $themeSetting->color('primary_color'),
        'secondary' => $themeSetting->color('secondary_color'),
        'topbar' => $themeSetting->color('topbar_color'),
        'navbar' => $themeSetting->color('navbar_color'),
        'breaking' => $themeSetting->color('breaking_bar_color'),
        'announcement' => $themeSetting->color('announcement_bar_color'),
        'button' => $themeSetting->color('button_color'),
        'button_hover' => $themeSetting->color('button_hover_color'),
        'link' => $themeSetting->color('link_color'),
        'heading' => $themeSetting->color('heading_color'),
        'text' => $themeSetting->color('text_color'),
        'card' => $themeSetting->color('card_background_color'),
        'footer' => $themeSetting->color('footer_color'),
    ];
@endphp

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="{{ $themeColors['primary'] }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $siteName }}">

    <title>{{ $metaTitle }}</title>

    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <meta name="author" content="{{ $seoMeta['author'] }}">
    <meta name="language" content="{{ $seoMeta['language'] }}">
    <meta name="robots" content="{{ $robots }}">

    <link rel="canonical" href="{{ $canonical }}">

    <meta property="og:type" content="{{ $seoMeta['og_type'] }}">
    <meta property="og:title" content="{{ $seoMeta['og_title'] }}">
    <meta property="og:description" content="{{ $seoMeta['og_description'] }}">
    <meta property="og:url" content="{{ $seoMeta['og_url'] }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:image" content="{{ $seoMeta['og_image'] }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoMeta['twitter_title'] }}">
    <meta name="twitter:description" content="{{ $seoMeta['twitter_description'] }}">
    <meta name="twitter:image" content="{{ $seoMeta['twitter_image'] }}">
    @yield('news_meta')

    <link rel="icon" type="image/png"
          href="{{ $siteSetting?->favicon ? asset('storage/' . $siteSetting->favicon) : asset('favicon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <link rel="apple-touch-icon" href="{{ asset('pwa/icon.svg') }}">

    <script src="https://cdn.tailwindcss.com"></script>

    <script defer
            src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/js/app.js'])

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    @if($siteSetting?->header_scripts)
        {!! $siteSetting->header_scripts !!}
    @endif

    @if($siteSetting?->google_analytics)
        {!! $siteSetting->google_analytics !!}
    @endif

    @if($seoSetting?->google_analytics)
        {!! $seoSetting->google_analytics !!}
    @endif

    @if($seoSetting?->google_tag_manager)
        {!! $seoSetting->google_tag_manager !!}
    @endif

    @if($seoSetting?->json_ld)
        <script type="application/ld+json">
            {!! $seoSetting->json_ld !!}
        </script>
    @endif

    <script type="application/ld+json">
        {!! json_encode(app(\App\Services\SeoService::class)->organizationSchema($siteSetting), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    <script type="application/ld+json">
        {!! json_encode(app(\App\Services\SeoService::class)->websiteSchema($siteSetting), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

@yield('schema')
<style>
    :root {
        --theme-primary: {{ $themeColors['primary'] }};
        --theme-secondary: {{ $themeColors['secondary'] }};
        --theme-topbar: {{ $themeColors['topbar'] }};
        --theme-navbar: {{ $themeColors['navbar'] }};
        --theme-breaking: {{ $themeColors['breaking'] }};
        --theme-announcement: {{ $themeColors['announcement'] }};
        --theme-button: {{ $themeColors['button'] }};
        --theme-button-hover: {{ $themeColors['button_hover'] }};
        --theme-link: {{ $themeColors['link'] }};
        --theme-heading: {{ $themeColors['heading'] }};
        --theme-text: {{ $themeColors['text'] }};
        --theme-card: {{ $themeColors['card'] }};
        --theme-footer: {{ $themeColors['footer'] }};
        --mobile-shell-bottom: 74px;
        --safe-bottom: env(safe-area-inset-bottom, 0px);
    }

    body.frontend-public-body {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 100%;
        color: var(--theme-text);
    }

    html,
    body.frontend-public-body {
        max-width: 100%;
        overflow-x: clip;
    }

    body.frontend-public-body *,
    body.frontend-public-body *::before,
    body.frontend-public-body *::after {
        box-sizing: border-box;
    }

    body.frontend-public-body img,
    body.frontend-public-body video,
    body.frontend-public-body iframe,
    body.frontend-public-body table {
        max-width: 100%;
    }

    body.frontend-public-body iframe {
        display: block;
    }

    .theme-card,
    .premium-card,
    .premium-article,
    .premium-page-shell {
        min-width: 0;
    }

    .premium-ad-slot,
    .premium-ad-slot a,
    .premium-ad-slot img {
        max-width: 100%;
    }

    .premium-ad-slot img {
        height: auto;
        object-fit: contain;
    }

    .premium-reading {
        overflow-wrap: anywhere;
    }

    .frontend-user-logout-form {
        display: inline-flex;
        align-items: center;
        margin: 0;
        padding: 0;
        border: 0;
    }

    .frontend-user-link {
        display: inline-flex;
        align-items: center;
        margin: 0;
        padding: 0;
        border: 0;
        background: transparent;
        color: inherit;
        font: inherit;
        line-height: inherit;
        text-decoration: none;
        cursor: pointer;
        appearance: none;
    }

    .frontend-user-link:hover {
        color: rgb(226 232 240);
    }

    .frontend-mobile-user-link {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0.75rem 0;
        border: 0;
        background: transparent;
        color: inherit;
        font: inherit;
        font-weight: 700;
        line-height: inherit;
        text-align: left;
        cursor: pointer;
        appearance: none;
    }

    .frontend-mobile-logout-form {
        margin: 0;
        padding: 0;
        border-top: 0;
    }

    h1, h2, h3,
    .theme-heading {
        color: var(--theme-heading);
    }

    .theme-topbar {
        background-color: var(--theme-topbar) !important;
    }

    .theme-navbar {
        background-color: var(--theme-navbar) !important;
    }

    .theme-breaking {
        background-color: var(--theme-breaking) !important;
    }

    .theme-announcement {
        background-color: var(--theme-announcement) !important;
    }

    .theme-footer {
        background-color: var(--theme-footer) !important;
    }

    .theme-button {
        background-color: var(--theme-button) !important;
        color: #fff !important;
    }

    .theme-button:hover {
        background-color: var(--theme-button-hover) !important;
    }

    .theme-link,
    main a:not([class*="bg-"]):not([class*="text-white"]) {
        color: var(--theme-link);
    }

    .theme-chip {
        background-color: var(--theme-primary) !important;
        color: #fff !important;
    }

    .theme-card {
        background-color: var(--theme-card) !important;
    }

    .theme-primary-bg {
        background-color: var(--theme-primary) !important;
    }

    .theme-secondary-bg {
        background-color: var(--theme-secondary) !important;
    }

    .theme-primary-text {
        color: var(--theme-primary) !important;
    }

    .header-slot-scroll {
        display: flex;
        flex: 1 1 auto;
        width: 100%;
        min-width: 0;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
        overflow-x: hidden;
        overflow-y: hidden;
        scrollbar-width: none;
    }

    .header-slot-scroll::-webkit-scrollbar {
        display: none;
    }

    .header-slot-button {
        align-items: center;
        background-color: var(--header-slot-bg, var(--theme-button));
        border-radius: var(--header-slot-radius, 6px);
        color: var(--header-slot-color, #fff);
        display: inline-flex;
        flex: 0 0 auto;
        font-weight: 900;
        gap: 0.35rem;
        line-height: 1;
        max-width: 180px;
        transition: background-color 160ms ease, transform 160ms ease;
        white-space: nowrap;
    }

    .header-slot-button:hover {
        background-color: var(--header-slot-hover, var(--theme-button-hover));
        transform: translateY(-1px);
    }

    .header-slot-banner {
        flex: 0 1 auto;
        max-width: min(100%, var(--header-slot-banner-max-width, 260px));
        min-width: 0;
    }

    .header-slot-banner img {
        display: block;
        width: auto;
        height: auto;
        max-height: 40px;
        max-width: 100%;
        object-fit: contain;
    }

    .header-slot-banner a,
    .header-slot-banner span {
        display: inline-flex;
        max-width: 100%;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 1023px) {
        .header-slot-scroll {
            overflow-x: auto;
        }
    }

    @media (max-width: 767px) {
        body {
            padding-bottom: calc(var(--mobile-shell-bottom) + var(--safe-bottom));
        }

        body.frontend-public-body {
            overflow-x: hidden;
        }

        .mobile-safe-bottom {
            padding-bottom: var(--safe-bottom);
        }

        .mobile-app-banner {
            bottom: calc(var(--mobile-shell-bottom) + var(--safe-bottom) + 12px);
        }

        .mobile-ux-toast-area {
            bottom: calc(var(--mobile-shell-bottom) + var(--safe-bottom) + 86px);
        }

        .mobile-realtime-pill {
            bottom: calc(var(--mobile-shell-bottom) + var(--safe-bottom) + 14px);
        }

        .mobile-push-banner {
            padding: 12px;
        }

        .mobile-push-banner p {
            display: none;
        }
    }

    @keyframes mobileBadgePulse {
        0% { transform: scale(1); }
        40% { transform: scale(1.18); }
        100% { transform: scale(1); }
    }

    .mobile-badge {
        box-shadow: 0 0 0 2px rgba(255,255,255,0.95);
    }

    .mobile-badge-pop {
        animation: mobileBadgePulse 420ms ease-out;
    }
</style>
</head>

<body class="frontend-public-body @yield('body_class') m-0 bg-[#f3f3f3] text-slate-900"><header class="site-header-root" x-data="{ mobileMenu: false, mobileSearch: false, authModal: null, liveActivityModal: false }">

    {{-- ÜST MAVİ MENÜ --}}
    <div class="theme-topbar bg-[#0878c9] text-white">
       <div class="max-w-7xl mx-auto px-4">

            <div class="h-14 flex items-center justify-between">

                <a href="/" class="shrink-0 text-lg md:text-xl font-black tracking-tight leading-none whitespace-nowrap">
                    {{ $siteName }}
                </a>

                <nav class="mx-4 hidden min-w-0 flex-1 items-center gap-2 text-xs font-bold whitespace-nowrap md:flex">
                    @if($headerSlots->isNotEmpty())
                        <div class="header-slot-scroll">
                            @foreach($headerSlots as $headerSlot)
                                @if($headerSlot->isButton() && filled($headerSlot->button_text))
                                    @php
                                        $buttonStyle = collect([
                                            '--header-slot-bg: ' . ($headerSlot->button_background_color ?: 'var(--theme-button)'),
                                            '--header-slot-hover: ' . ($headerSlot->button_hover_color ?: 'var(--theme-button-hover)'),
                                            '--header-slot-color: ' . ($headerSlot->button_text_color ?: '#ffffff'),
                                            '--header-slot-radius: ' . ($headerSlot->button_radius ?? 6) . 'px',
                                        ])->implode('; ');
                                        $buttonTarget = $headerSlot->button_target === '_blank' ? '_blank' : '_self';
                                    @endphp

                                    @if($headerSlot->button_url)
                                        <a
                                            href="{{ $headerSlot->button_url }}"
                                            target="{{ $buttonTarget }}"
                                            @if($buttonTarget === '_blank') rel="noopener noreferrer" @endif
                                            class="header-slot-button {{ $headerSlot->buttonSizeClasses() }} {{ $headerSlot->custom_css_class }}"
                                            style="{{ $buttonStyle }}"
                                        >
                                            @if($headerSlot->button_icon)
                                                <span>{{ $headerSlot->button_icon }}</span>
                                            @endif
                                            <span class="truncate">{{ $headerSlot->button_text }}</span>
                                        </a>
                                    @else
                                        <span
                                            class="header-slot-button {{ $headerSlot->buttonSizeClasses() }} {{ $headerSlot->custom_css_class }}"
                                            style="{{ $buttonStyle }}"
                                        >
                                            @if($headerSlot->button_icon)
                                                <span>{{ $headerSlot->button_icon }}</span>
                                            @endif
                                            <span class="truncate">{{ $headerSlot->button_text }}</span>
                                        </span>
                                    @endif
                                @elseif($headerSlot->isBanner())
                                    @php
                                        $bannerTarget = $headerSlot->banner_target === '_blank' ? '_blank' : '_self';
                                        $bannerWidth = filled($headerSlot->banner_width) ? (int) $headerSlot->banner_width : null;
                                        $bannerHeight = filled($headerSlot->banner_height) ? (int) $headerSlot->banner_height : null;
                                        $bannerImageExists = $headerSlot->banner_image
                                            ? \Illuminate\Support\Facades\Storage::disk('public')->exists($headerSlot->banner_image)
                                            : false;
                                        $bannerImage = $bannerImageExists ? asset('storage/' . $headerSlot->banner_image) : null;
                                        $bannerStyle = collect([
                                            $bannerWidth ? 'width: ' . $bannerWidth . 'px' : null,
                                            $bannerHeight ? 'height: ' . $bannerHeight . 'px' : null,
                                            'max-height: 40px',
                                            'max-width: 100%',
                                            'object-fit: contain',
                                        ])->filter()->implode('; ');
                                        $bannerWrapperStyle = $bannerWidth
                                            ? '--header-slot-banner-max-width: clamp(120px, 25vw, ' . $bannerWidth . 'px)'
                                            : '--header-slot-banner-max-width: clamp(120px, 25vw, 260px)';
                                    @endphp

                                    <div class="header-slot-banner flex items-center overflow-hidden" style="{{ $bannerWrapperStyle }}">
                                        @if($bannerImageExists)
                                            @if($headerSlot->banner_url)
                                                <a
                                                    href="{{ $headerSlot->banner_url }}"
                                                    target="{{ $bannerTarget }}"
                                                    @if($bannerTarget === '_blank') rel="noopener noreferrer" @endif
                                                    class="overflow-hidden rounded border border-white/10"
                                                >
                                                    <img
                                                        src="{{ $bannerImage }}"
                                                        alt="{{ $headerSlot->banner_alt ?: $headerSlot->title }}"
                                                        style="{{ $bannerStyle }}"
                                                    >
                                                </a>
                                            @else
                                                <img
                                                    src="{{ $bannerImage }}"
                                                    alt="{{ $headerSlot->banner_alt ?: $headerSlot->title }}"
                                                    class="overflow-hidden rounded border border-white/10"
                                                    style="{{ $bannerStyle }}"
                                                >
                                            @endif
                                        @elseif($headerSlot->banner_image)
                                            <div
                                                class="flex items-center justify-center rounded border border-dashed border-white/40 px-3 text-[11px] font-bold text-white/90"
                                                style="{{ $bannerStyle }}"
                                            >
                                                Banner görseli bulunamadı
                                            </div>
                                        @endif

                                        @if($headerSlot->html_code)
                                            <div class="max-h-9 overflow-hidden">
                                                {!! $headerSlot->html_code !!}
                                            </div>
                                        @endif

                                        @if($headerSlot->script_code)
                                            {!! $headerSlot->script_code !!}
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </nav>

                <div class="hidden shrink-0 items-center gap-4 text-sm font-semibold md:flex">

                    <form action="/arama" method="GET" class="hidden lg:flex items-center">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Ara..."
                            class="w-24 focus:w-36 transition-all duration-300 px-2 py-1.5 rounded-l bg-white text-slate-900 text-sm outline-none"
                        >

                        <button type="submit" class="theme-button bg-slate-900 px-3 py-1.5 rounded-r hover:bg-slate-800 transition">
                            🔍
                        </button>
                    </form>

                    @auth

                        @php
                            $activeWorkSession = \App\Models\WorkSession::where('user_id', auth()->id())
                                ->where('status', 'active')
                                ->latest()
                                ->first();

                            $workLabel = $activeWorkSession
                                ? match($activeWorkSession->type) {
                                    'work' => 'Mesaide',
                                    'break' => 'Molada',
                                    'lunch' => 'Yemekte',
                                    default => 'Aktif',
                                }
                                : 'Mesai';

                            $todayWorkMinutes = \App\Models\WorkSession::where('user_id', auth()->id())
                                ->whereDate('created_at', today())
                                ->where('type', 'work')
                                ->sum('duration_minutes');

                            $todayBreakMinutes = \App\Models\WorkSession::where('user_id', auth()->id())
                                ->whereDate('created_at', today())
                                ->where('type', 'break')
                                ->sum('duration_minutes');

                            $todayLunchMinutes = \App\Models\WorkSession::where('user_id', auth()->id())
                                ->whereDate('created_at', today())
                                ->where('type', 'lunch')
                                ->sum('duration_minutes');

                            $weekWorkMinutes = \App\Models\WorkSession::where('user_id', auth()->id())
                                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                ->where('type', 'work')
                                ->sum('duration_minutes');

                            $monthWorkMinutes = \App\Models\WorkSession::where('user_id', auth()->id())
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->where('type', 'work')
                                ->sum('duration_minutes');
                        @endphp

                        @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('panel_giris'))

                            <div x-data="{ openWorkPanel: false }" class="relative">
                                <button
                                    @click="openWorkPanel = true"
                                    class="theme-button bg-slate-900 px-3 py-2 rounded font-black hover:bg-slate-800 transition"
                                >
                                    ⏱ {{ $workLabel }}
                                </button>

                                <div
                                    x-show="openWorkPanel"
                                    x-transition
                                    class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
                                    style="display:none;"
                                >
                                    <div
                                        @click.away="openWorkPanel = false"
                                        class="bg-white text-slate-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                                    >

                                        <div class="theme-navbar bg-slate-950 text-white px-6 py-4 flex items-center justify-between">
                                            <div>
                                                <h3 class="text-xl font-black">Mesai Durumu</h3>
                                                <p class="text-xs text-slate-300">Mola, yemek ve mesai takibi</p>
                                            </div>

                                            <button @click="openWorkPanel = false" class="text-2xl">
                                                ×
                                            </button>
                                        </div>

                                        <div class="p-6 space-y-4">

                                            <div class="grid grid-cols-3 gap-3">
                                                <div class="bg-green-50 border border-green-100 rounded-xl p-3 text-center">
                                                    <div class="text-xs text-green-700 font-bold">Bugün Mesai</div>
                                                    <div class="text-xl font-black text-green-700">{{ $todayWorkMinutes }} dk</div>
                                                </div>

                                                <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-3 text-center">
                                                    <div class="text-xs text-yellow-700 font-bold">Bugün Mola</div>
                                                    <div class="text-xl font-black text-yellow-700">{{ $todayBreakMinutes }} dk</div>
                                                </div>

                                                <div class="bg-red-50 border border-red-100 rounded-xl p-3 text-center">
                                                    <div class="text-xs text-red-700 font-bold">Bugün Yemek</div>
                                                    <div class="text-xl font-black text-red-700">{{ $todayLunchMinutes }} dk</div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="bg-slate-100 rounded-xl p-3 text-center">
                                                    <div class="text-xs text-slate-500 font-bold">Haftalık Mesai</div>
                                                    <div class="text-lg font-black text-slate-900">{{ $weekWorkMinutes }} dk</div>
                                                </div>

                                                <div class="bg-slate-100 rounded-xl p-3 text-center">
                                                    <div class="text-xs text-slate-500 font-bold">Aylık Mesai</div>
                                                    <div class="text-lg font-black text-slate-900">{{ $monthWorkMinutes }} dk</div>
                                                </div>
                                            </div>

                                            <div class="bg-slate-100 rounded-xl p-4 text-center">
                                                <div class="text-xs text-slate-500">Mevcut Durum</div>

                                                <div class="text-2xl font-black mt-1">
                                                    {{ $workLabel }}
                                                </div>

                                                @if($activeWorkSession)
                                                    <div class="text-xs text-slate-500 mt-1">
                                                        Başlangıç: {{ $activeWorkSession->started_at->format('H:i:s') }}
                                                    </div>
                                                @else
                                                    <div class="text-xs text-slate-500 mt-1">
                                                        Henüz aktif işlem yok
                                                    </div>
                                                @endif
                                            </div>

                                            <form action="{{ route('work.start') }}" method="POST">
                                                @csrf
                                                <button class="w-full bg-green-600 text-white py-3 rounded-xl font-black hover:bg-green-700">
                                                    🟢 Mesai Başlat
                                                </button>
                                            </form>

                                            <form action="{{ route('work.break') }}" method="POST">
                                                @csrf
                                                <button class="w-full bg-yellow-500 text-white py-3 rounded-xl font-black hover:bg-yellow-600">
                                                    ☕ Molaya Çık
                                                </button>
                                            </form>

                                            <form action="{{ route('work.lunch') }}" method="POST">
                                                @csrf
                                                <button class="w-full bg-red-500 text-white py-3 rounded-xl font-black hover:bg-red-600">
                                                    🍔 Yemeğe Çık
                                                </button>
                                            </form>

                                            <form action="{{ route('work.active') }}" method="POST">
                                                @csrf
                                                <button class="theme-button w-full bg-blue-600 text-white py-3 rounded-xl font-black hover:bg-blue-700">
                                                    ⚡ Aktife Dön
                                                </button>
                                            </form>

                                            <form action="{{ route('work.end') }}" method="POST">
                                                @csrf
                                                <button class="theme-button w-full bg-slate-900 text-white py-3 rounded-xl font-black hover:bg-slate-800">
                                                    🔴 Mesai Bitir
                                                </button>
                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endif

                        <div
    x-data="notificationSystem({{ auth()->user()->unreadNotifications()->count() }})"
    x-init="init()"
    class="relative">

    <a href="{{ route('user.notifications') }}"
       class="relative hover:text-slate-200 transition">

        <span class="text-xl">🔔</span>

        <span
            x-show="count > 0"
            x-text="count"
            class="absolute -top-2 -right-3 bg-red-600 text-white text-[10px] font-black min-w-[18px] h-[18px] rounded-full flex items-center justify-center px-1">
        </span>

    </a>

</div>

                        <a href="/dashboard" class="frontend-user-link">Panelim</a>

                        <a
                            href="{{ route('messages.index') }}"
                            x-data="privateMessageCounter()"
                            x-init="init()"
                            class="frontend-user-link relative"
                        >
                            Mesajlar
                            <span
                                x-show="count > 0"
                                x-text="count"
                                class="absolute -top-3 -right-4 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-black text-white"
                                style="display:none;"
                            ></span>
                        </a>

                        <a href="/profil/{{ auth()->id() }}" class="frontend-user-link">Profilim</a>

                        <form method="POST" action="{{ route('logout') }}" class="frontend-user-logout-form">
                            @csrf
                            <button type="submit" class="frontend-user-link">
                                Çıkış
                            </button>
                        </form>

                    @else

                        <button @click="authModal = 'login'" class="hover:text-slate-200">
                            Üye Girişi
                        </button>

                        <button @click="authModal = 'register'" class="theme-button bg-slate-800 px-3 py-2 rounded hover:bg-slate-700">
                            Kayıt Ol
                        </button>

                    @endauth

                </div>

                <div class="flex md:hidden items-center gap-3">
                    @auth
                        <a
                            href="{{ route('user.notifications') }}"
                            x-data="notificationSystem({{ auth()->user()->unreadNotifications()->count() }})"
                            x-init="init()"
                            class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-lg"
                            aria-label="Bildirimler"
                        >
                            <span>!</span>
                            <span
                                x-show="count > 0"
                                x-text="count"
                                :class="{ 'mobile-badge-pop': pulse }"
                                class="mobile-badge absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-black text-white"
                                style="display:none;"
                            ></span>
                        </a>
                    @endauth
                    <button @click="mobileSearch = !mobileSearch" class="text-xl">
                        🔍
                    </button>

                    <button @click="mobileMenu = !mobileMenu" class="text-2xl">
                        ☰
                    </button>
                </div>

            </div>

            {{-- MOBİL ARAMA --}}
            <div x-show="mobileSearch" x-transition class="md:hidden pb-4" style="display:none;">
                <form action="/arama" method="GET" class="flex">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Ara..."
                        class="flex-1 px-4 py-2 rounded-l text-slate-900 outline-none"
                    >

                    <button type="submit" class="theme-button bg-slate-900 px-4 rounded-r">
                        🔍
                    </button>
                </form>
            </div>

            {{-- MOBİL MENÜ --}}
            <div x-show="mobileMenu" x-transition class="theme-topbar md:hidden bg-[#0667ad] border-t border-white/10" style="display:none;">
                <div class="flex flex-col text-sm font-bold">

                    <a href="/" class="py-3 border-b border-white/10">Ana Sayfa</a>
                    @foreach($headerSlots->where('slot_type', \App\Models\HeaderSlot::TYPE_BUTTON) as $headerSlot)
                        @if(filled($headerSlot->button_text))
                            @php
                                $mobileButtonTarget = $headerSlot->button_target === '_blank' ? '_blank' : '_self';
                            @endphp

                            @if($headerSlot->button_url)
                                <a
                                    href="{{ $headerSlot->button_url }}"
                                    target="{{ $mobileButtonTarget }}"
                                    @if($mobileButtonTarget === '_blank') rel="noopener noreferrer" @endif
                                    class="py-3 border-b border-white/10"
                                >
                                    @if($headerSlot->button_icon)
                                        <span class="mr-1">{{ $headerSlot->button_icon }}</span>
                                    @endif
                                    {{ $headerSlot->button_text }}
                                </a>
                            @else
                                <div class="py-3 border-b border-white/10">
                                    @if($headerSlot->button_icon)
                                        <span class="mr-1">{{ $headerSlot->button_icon }}</span>
                                    @endif
                                    {{ $headerSlot->button_text }}
                                </div>
                            @endif
                        @endif
                    @endforeach
                    @if($homeMenuModules['news'])
                        <a href="/haberler" class="py-3 border-b border-white/10">Haberler</a>
                    @endif
                    @if($homeMenuModules['announcements'])
                    <a href="/ilanlar" class="py-3 border-b border-white/10">İlanlar</a>
                    @endif
                    @if($homeMenuModules['videos'])
                        <a href="{{ route('videos.index') }}" class="py-3 border-b border-white/10">Videolar</a>
                    @endif
                    @if($homeMenuModules['galleries'])
                        <a href="{{ route('galleries.index') }}" class="py-3 border-b border-white/10">Galeriler</a>
                    @endif
                    @if($homeMenuModules['polls'])
                        <a href="{{ route('polls.index') }}" class="py-3 border-b border-white/10">Anketler</a>
                    @endif

                    @if($homeMenuModules['forum'])
                        <a href="{{ route('forum.index') }}" class="py-3 border-b border-white/10">Forum</a>
                    @endif

                    @if($siteSetting?->live_chat_enabled || $siteSetting?->live_stream_enabled || $siteSetting?->live_announcement_enabled)
                        <button
                            type="button"
                            @click="liveActivityModal = true; mobileMenu = false"
                            class="py-3 border-b border-white/10 text-left"
                        >
                            Canlı Aktivite
                        </button>
                    @endif

                    <a href="/arama" class="py-3 border-b border-white/10">Arama</a>

                    @auth
                        <a href="/dashboard" class="py-3 border-b border-white/10">Panelim</a>
                        <a
                            href="{{ route('messages.index') }}"
                            x-data="privateMessageCounter()"
                            x-init="init()"
                            class="relative py-3 border-b border-white/10"
                        >
                            Mesajlar
                            <span
                                x-show="count > 0"
                                x-text="count"
                                class="ml-2 inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-black text-white"
                                style="display:none;"
                            ></span>
                        </a>
                        <a
                            href="{{ route('user.notifications') }}"
                            x-data="notificationSystem({{ auth()->user()->unreadNotifications()->count() }})"
                            x-init="init()"
                            class="relative py-3 border-b border-white/10"
                        >
                            Bildirimler
                            <span
                                x-show="count > 0"
                                x-text="count"
                                :class="{ 'mobile-badge-pop': pulse }"
                                class="mobile-badge ml-2 inline-flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-black text-white"
                                style="display:none;"
                            ></span>
                        </a>
                        <a href="/profil/{{ auth()->id() }}" class="py-3 border-b border-white/10">Profilim</a>

                        <form method="POST" action="{{ route('logout') }}" class="frontend-mobile-logout-form border-b border-white/10">
                            @csrf
                            <button type="submit" class="frontend-mobile-user-link">
                                Çıkış
                            </button>
                        </form>
                    @else
                        <button @click="authModal = 'login'; mobileMenu = false" class="py-3 border-b border-white/10 text-left">
                            Giriş Yap
                        </button>

                        <button @click="authModal = 'register'; mobileMenu = false" class="py-3 text-left">
                            Kayıt Ol
                        </button>
                    @endauth

                </div>
            </div>

            {{-- GİRİŞ / KAYIT POPUP --}}
            <div
                x-show="authModal"
                x-transition
                class="fixed inset-0 z-[9999] bg-black/60 flex items-center justify-center p-4"
                style="display:none;"
            >
                <div
                    @click.away="authModal = null"
                    class="theme-card bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden text-slate-900"
                >
                    <div class="theme-navbar bg-slate-950 text-white px-6 py-5 flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-black" x-text="authModal === 'login' ? 'Üye Girişi' : 'Üye Ol'"></h2>
                            <p class="text-sm text-slate-300 mt-1">
                                Güvenli kullanıcı paneli
                            </p>
                        </div>

                        <button @click="authModal = null" class="text-3xl leading-none">
                            ×
                        </button>
                    </div>

                    <div class="p-6">

                        <template x-if="authModal === 'login'">
                            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="text-sm font-bold">E-posta</label>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        class="mt-2 w-full rounded-xl border-slate-300 text-slate-900"
                                        placeholder="ornek@mail.com"
                                    >
                                </div>

                                <div>
                                    <label class="text-sm font-bold">Şifre</label>
                                    <input
                                        type="password"
                                        name="password"
                                        required
                                        class="mt-2 w-full rounded-xl border-slate-300 text-slate-900"
                                        placeholder="••••••••"
                                    >
                                </div>

                                <div class="flex items-center justify-between text-sm">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="remember" class="rounded">
                                        Beni hatırla
                                    </label>

                                    <a href="{{ route('password.request') }}" class="theme-link text-blue-700 font-bold hover:underline">
                                        Şifremi unuttum
                                    </a>
                                </div>

                                <button class="theme-button w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-xl font-black">
                                    Giriş Yap
                                </button>

                                <p class="text-center text-sm text-slate-600">
                                    Hesabınız yok mu?
                                    <button type="button" @click="authModal = 'register'" class="theme-link text-blue-700 font-black">
                                        Üye ol
                                    </button>
                                </p>
                            </form>
                        </template>

                        <div x-show="authModal === 'register'" style="display:none;">
                            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label class="text-sm font-bold">Ad Soyad</label>
                                    <input
                                        type="text"
                                        name="name"
                                        required
                                        class="mt-2 w-full rounded-xl border-slate-300 text-slate-900"
                                        placeholder="Ad Soyad"
                                    >
                                </div>

                                <div>
                                    <label class="text-sm font-bold">E-posta</label>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        class="mt-2 w-full rounded-xl border-slate-300 text-slate-900"
                                        placeholder="ornek@mail.com"
                                    >
                                </div>

                                <div>
                                    <label class="text-sm font-bold">Şifre</label>
                                    <input
                                        type="password"
                                        name="password"
                                        required
                                        class="mt-2 w-full rounded-xl border-slate-300 text-slate-900"
                                        placeholder="••••••••"
                                    >
                                </div>

                                <div>
                                    <label class="text-sm font-bold">Şifre Tekrar</label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        required
                                        class="mt-2 w-full rounded-xl border-slate-300 text-slate-900"
                                        placeholder="••••••••"
                                    >
                                </div>

                                <div class="bg-slate-50 border rounded-xl p-3 text-xs text-slate-600">
                                    Kayıt olarak kullanım şartlarını ve topluluk kurallarını kabul etmiş olursunuz.
                                </div>
<div>
    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>

    @error('g-recaptcha-response')
        <p class="text-sm text-red-600 mt-2">
            {{ $message }}
        </p>
    @enderror
</div>
                                <button class="theme-button w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-xl font-black">
                                    Hesap Oluştur
                                </button>

                                <p class="text-center text-sm text-slate-600">
                                    Zaten hesabınız var mı?
                                    <button type="button" @click="authModal = 'login'" class="theme-link text-blue-700 font-black">
                                        Giriş yap
                                    </button>
                                </p>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
   {{-- CANLI AKTİVİTE POPUP --}}
<div
    x-show="liveActivityModal"
    x-transition
    class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 p-4"
    style="display:none;"
>
    <div
        @click.away="liveActivityModal = false"
        class="theme-card w-full max-w-md overflow-hidden rounded-3xl bg-white shadow-2xl text-slate-900"
    >
        <div class="theme-breaking flex items-center justify-between bg-red-700 px-6 py-5 text-white">
            <div>
                <h2 class="text-2xl font-black">Canlı Aktivite</h2>

                <p class="mt-1 text-sm text-red-100">
                    Canlı yayın, sohbet ve duyurular
                </p>
            </div>

            <button
                @click="liveActivityModal = false"
                class="text-3xl leading-none"
            >
                ×
            </button>
        </div>

        <div class="space-y-3 p-6">

            @if($siteSetting?->live_chat_enabled)
                <a href="{{ route('live-chat.index') }}"
                   class="block rounded-2xl border border-red-100 bg-red-50 p-4 font-black text-red-700 transition hover:bg-red-100">

                    💬 Canlı Sohbet

                    <div class="mt-1 text-xs font-semibold text-red-500">
                        Üyelerle anlık sohbet alanı
                    </div>
                </a>
            @endif

            @if($siteSetting?->live_stream_enabled)
                <a href="{{ route('live-activity.index') }}"
                   class="block rounded-2xl border border-slate-200 bg-slate-50 p-4 font-black text-slate-800 transition hover:bg-slate-100">

                    📺 Canlı Yayın

                    <div class="mt-1 text-xs font-semibold text-slate-500">
                        Aktif yayın varsa buradan izleyebilirsiniz
                    </div>
                </a>
            @endif

            @if($siteSetting?->live_announcement_enabled)
                <a href="#son-dakika"
                   @click="liveActivityModal = false"
                   class="block rounded-2xl border border-yellow-100 bg-yellow-50 p-4 font-black text-yellow-700 transition hover:bg-yellow-100">

                    📢 Canlı Duyuru

                    <div class="mt-1 text-xs font-semibold text-yellow-600">
                        {{ $siteSetting?->live_announcement_text ?? 'Güncel duyuruları görüntüle' }}
                    </div>
                </a>
            @endif

            @if($homeMenuModules['forum'])
            <a href="{{ route('forum.index') }}"
               class="theme-link block rounded-2xl border border-blue-100 bg-blue-50 p-4 font-black text-blue-700 transition hover:bg-blue-100">

                👥 Forum

                <div class="mt-1 text-xs font-semibold text-blue-500">
                    Topluluk konuları ve tartışmalar
                </div>
            </a>
            @endif

        </div>
    </div>
</div>
    {{-- ALT MENÜ --}}
    <div class="theme-navbar bg-slate-800 text-white">
        <div class="max-w-7xl mx-auto flex min-h-10 items-center gap-4 px-4 py-2 text-sm font-semibold">
            <nav class="flex shrink-0 items-center gap-5 overflow-x-auto whitespace-nowrap">
                <a href="/" class="{{ request()->is('/') ? 'text-blue-200' : 'hover:text-blue-200' }}">Ana Sayfa</a>
                @if($homeMenuModules['news'])
                <a href="/haberler" class="{{ request()->is('haberler*') || request()->is('haber/*') ? 'text-blue-200' : 'hover:text-blue-200' }}">Haberler</a>
                @endif
                @if($homeMenuModules['announcements'])
                <a href="/ilanlar" class="{{ request()->is('ilanlar*') || request()->is('ilan/*') ? 'text-blue-200' : 'hover:text-blue-200' }}">İlanlar</a>
                @endif
                @if($homeMenuModules['videos'])
                <a href="{{ route('videos.index') }}" class="{{ request()->is('videolar*') || request()->is('video/*') ? 'text-blue-200' : 'hover:text-blue-200' }}">Videolar</a>
                @endif
                @if($homeMenuModules['galleries'])
                <a href="{{ route('galleries.index') }}" class="{{ request()->is('galeriler*') || request()->is('galeri/*') ? 'text-blue-200' : 'hover:text-blue-200' }}">Galeriler</a>
                @endif
                @if($homeMenuModules['polls'])
                <a href="{{ route('polls.index') }}" class="{{ request()->is('anketler*') || request()->is('anket/*') ? 'text-blue-200' : 'hover:text-blue-200' }}">Anketler</a>
                @endif
            </nav>

            @if($siteAnnouncements->isNotEmpty())
                <div class="theme-announcement hidden min-w-0 flex-1 items-center overflow-hidden rounded border border-white/10 bg-slate-900/45 px-3 py-1.5 text-xs font-bold text-slate-100 md:flex">
                    <marquee behavior="scroll" direction="left" scrollamount="4" class="min-w-0">
                        @foreach($siteAnnouncements as $announcement)
                            @if($announcement->link_url)
                                <a
                                    href="{{ $announcement->link_url }}"
                                    target="{{ $announcement->link_target }}"
                                    @if($announcement->link_target === '_blank') rel="noopener noreferrer" @endif
                                    class="hover:text-blue-200"
                                >
                                    {{ $announcement->icon ?: '📢' }} {{ $announcement->text }}
                                </a>
                            @else
                                <span>{{ $announcement->icon ?: '📢' }} {{ $announcement->text }}</span>
                            @endif

                            @unless($loop->last)
                                <span class="px-3 text-slate-400">—</span>
                            @endunless
                        @endforeach
                    </marquee>
                </div>
            @endif

            <div class="ml-auto hidden shrink-0 items-center gap-2 md:flex">
                @if($homeMenuModules['forum'])
                    <a href="{{ route('forum.index') }}"
                       class="theme-chip ml-2 rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-3 py-1.5 text-[11px] font-black text-white shadow-md transition hover:scale-105 whitespace-nowrap">
                        FORUM
                    </a>
                @endif

                @if($siteSetting?->live_chat_enabled || $siteSetting?->live_stream_enabled || $siteSetting?->live_announcement_enabled)
                    <button type="button"
                        @click="liveActivityModal = true"
                        class="theme-chip inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-red-600 to-red-700 px-3 py-1.5 text-[11px] font-black text-white shadow-md transition hover:scale-105 whitespace-nowrap">
                        <span class="h-2 w-2 rounded-full bg-white shadow animate-pulse"></span>
                        CANLI AKTİVİTE
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- SON DAKİKA --}}
    @if($homeBreakingNewsEnabled)
    <div id="son-dakika" class="theme-breaking bg-red-600 text-white overflow-hidden border-b border-red-700">
        <div class="max-w-7xl mx-auto flex items-center h-10">
            <div class="theme-announcement px-4 h-full flex items-center font-bold text-sm whitespace-nowrap">
                SON DAKİKA
            </div>

            <marquee behavior="scroll" direction="left" scrollamount="5" class="min-w-0 px-4 text-sm font-semibold">
                @forelse($breakingTickerItems as $item)
                    @if($item['url'])
                        <a
                            href="{{ $item['url'] }}"
                            target="{{ $item['target'] }}"
                            @if($item['target'] === '_blank') rel="noopener noreferrer" @endif
                            class="hover:text-red-100"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span>{{ $item['label'] }}</span>
                    @endif

                    @unless($loop->last)
                        <span class="px-3 text-red-100/80">-</span>
                    @endunless
                @empty
                    <span>Son dakika haberlerini takip edin</span>
                @endforelse
            </marquee>

            <marquee behavior="scroll" direction="left" scrollamount="5" class="hidden text-sm font-semibold px-4">
                🔥 Memur alımı ilanları güncellendi —
                🔥 KPSS tercih süreci başladı —
                🔥 Yeni personel alım ilanları yayımlandı —
                🔥 Akademik ilanlarda yeni kadrolar açıldı —
                🔥 Son dakika haberlerini takip edin
            </marquee>
        </div>
    </div>

    {{-- FİNANS + HAVA DURUMU --}}
    @endif

    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 h-9 flex items-center justify-between text-sm">

            <div class="flex items-center gap-6 overflow-x-auto text-sm font-semibold whitespace-nowrap">
                <span><b>Dolar:</b> {{ $market['dolar'] ?? '45.35' }} <span class="text-green-600">%0.24 ↑</span></span>
                <span><b>Euro:</b> {{ $market['euro'] ?? '53.52' }} <span class="text-green-600">%0.56 ↑</span></span>
                <span><b>Altın:</b> {{ $market['altin'] ?? '6875.62' }} <span class="text-green-600">%0.87 ↑</span></span>
                <span><b>BIST:</b> {{ $market['bist'] ?? '15062.65' }} <span class="text-green-600">%0.15 ↑</span></span>
                <span><b>BTC:</b> {{ $market['btc'] ?? '81256' }} <span class="text-green-600">%0.48 ↑</span></span>
            </div>

            <div class="hidden md:block whitespace-nowrap">
                {{ $weather['city'] ?? 'İstanbul' }},
                {{ $weather['status'] ?? 'Açık' }}
                •
                <b>{{ $weather['temp'] ?? 19 }}°</b>
            </div>

        </div>
    </div>

</header>

<main class="min-h-screen pb-4 md:pb-0">
    @yield('content')
</main>

<div
    id="pwa-install-banner"
    class="theme-card mobile-app-banner fixed inset-x-4 z-[9998] hidden rounded-2xl border border-slate-200 bg-white p-4 text-slate-900 shadow-2xl md:bottom-4 md:left-auto md:w-96"
>
    <div class="flex items-start gap-3">
        <div class="theme-chip flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-600 text-sm font-black text-white">APP</div>
        <div class="min-w-0">
            <div class="text-sm font-black">Uygulama olarak yukle</div>
            <p class="mt-1 text-xs font-bold leading-5 text-slate-500">{{ $siteName }}'i mobil cihazinizda daha hizli acabilirsiniz.</p>
        </div>
    </div>
    <div class="mt-3 flex gap-2">
        <button type="button" data-pwa-install class="theme-button rounded-lg bg-red-600 px-4 py-2 text-xs font-black text-white transition hover:bg-red-700">
            Yukle
        </button>
        <button type="button" data-pwa-dismiss class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-black text-slate-700 transition hover:bg-slate-200">
            Daha sonra
        </button>
    </div>
</div>

@auth
    <div
        id="pwa-notification-banner"
        class="mobile-app-banner mobile-push-banner fixed inset-x-4 z-[9997] hidden rounded-2xl border border-blue-100 bg-blue-50 p-4 text-blue-950 shadow-2xl md:bottom-4 md:left-auto md:w-96"
    >
        <div class="text-sm font-black">Bildirimleri ac</div>
        <p class="mt-1 text-xs font-bold leading-5 text-blue-800/80">Forum, mesaj ve moderasyon bildirimlerini tarayicinizdan alabilirsiniz.</p>
        <div class="mt-3 flex gap-2">
            <button type="button" data-pwa-notification-enable class="theme-button rounded-lg bg-blue-700 px-4 py-2 text-xs font-black text-white transition hover:bg-blue-800">
                Izin ver
            </button>
            <button type="button" data-pwa-notification-dismiss class="rounded-lg bg-white px-4 py-2 text-xs font-black text-blue-800 transition hover:bg-blue-100">
                Kapat
            </button>
        </div>
    </div>
@endauth

<nav class="mobile-safe-bottom fixed inset-x-0 bottom-0 z-[9996] border-t border-slate-200 bg-white/95 shadow-[0_-8px_24px_rgba(15,23,42,0.12)] backdrop-blur md:hidden">
    <div class="grid h-[74px] {{ $mobileBottomColumns }} text-[11px] font-black text-slate-500">
        <a href="/" class="flex flex-col items-center justify-center gap-1 {{ request()->is('/') ? 'text-blue-700' : '' }}">
            <span class="text-lg leading-none">⌂</span>
            <span>Ana</span>
        </a>

        @if($homeMenuModules['forum'])
            <a href="{{ route('forum.index') }}" class="flex flex-col items-center justify-center gap-1 {{ request()->is('forum*') ? 'text-blue-700' : '' }}">
                <span class="text-lg leading-none">#</span>
                <span>Forum</span>
            </a>
        @endif

        <a href="{{ route('search') }}" class="flex flex-col items-center justify-center gap-1 {{ request()->is('arama*') ? 'text-blue-700' : '' }}">
            <span class="text-lg leading-none">⌕</span>
            <span>Arama</span>
        </a>

        @auth
            <a
                href="{{ route('messages.index') }}"
                x-data="privateMessageCounter()"
                x-init="init()"
                class="relative flex flex-col items-center justify-center gap-1 {{ request()->is('mesajlar*') ? 'text-blue-700' : '' }}"
            >
                <span class="text-lg leading-none">✉</span>
                <span>Mesaj</span>
                <span
                    x-show="count > 0"
                    x-text="count"
                    :class="{ 'mobile-badge-pop': pulse }"
                    class="mobile-badge absolute right-4 top-2 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-black text-white"
                    style="display:none;"
                ></span>
            </a>

            <a
                href="/profil/{{ auth()->id() }}"
                x-data="notificationSystem({{ auth()->user()->unreadNotifications()->count() }})"
                x-init="init()"
                class="relative flex flex-col items-center justify-center gap-1 {{ request()->is('bildirimler*') || request()->is('profil*') ? 'text-blue-700' : '' }}"
            >
                <span class="text-lg leading-none">●</span>
                <span>Profil</span>
                <span
                    x-show="count > 0"
                    x-text="count"
                    :class="{ 'mobile-badge-pop': pulse }"
                    class="mobile-badge absolute right-4 top-2 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-blue-700 px-1 text-[10px] font-black text-white"
                    style="display:none;"
                ></span>
            </a>
        @else
            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center gap-1">
                <span class="text-lg leading-none">✉</span>
                <span>Mesaj</span>
            </a>

            <a href="{{ route('login') }}" class="flex flex-col items-center justify-center gap-1">
                <span class="text-lg leading-none">●</span>
                <span>Profil</span>
            </a>
        @endauth
    </div>
</nav>

@auth
    <div
        x-data="realtimeMobileUx()"
        x-init="init()"
        class="pointer-events-none fixed inset-x-4 z-[9995] md:hidden"
    >
        <div class="mobile-ux-toast-area fixed inset-x-4 space-y-2">
            <template x-for="toast in toasts" :key="toast.id">
                <a
                    :href="toast.url || '#'"
                    x-show="toast.visible"
                    x-transition
                    class="pointer-events-auto block rounded-2xl border border-slate-200 bg-white p-3 text-slate-950 shadow-2xl"
                >
                    <div class="flex items-start gap-3">
                        <div
                            class="mt-0.5 h-2.5 w-2.5 shrink-0 rounded-full"
                            :class="toast.kind === 'message' ? 'bg-red-600' : 'bg-blue-700'"
                        ></div>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-black" x-text="toast.title"></div>
                            <div class="mt-0.5 line-clamp-2 text-xs font-bold leading-5 text-slate-500" x-text="toast.message"></div>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <div
            x-show="showPill"
            x-transition
            class="mobile-realtime-pill pointer-events-none fixed left-4 inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/95 px-3 py-2 text-[11px] font-black text-slate-700 shadow-lg backdrop-blur"
            style="display:none;"
        >
            <span
                class="h-2 w-2 rounded-full"
                :class="{
                    'bg-green-500': online && status === 'connected',
                    'bg-amber-500': online && status === 'polling',
                    'bg-red-500': !online || status === 'disconnected',
                }"
            ></span>
            <span x-text="label"></span>
        </div>
    </div>
@endauth

<footer class="theme-footer bg-slate-900 text-white mt-12 pb-24 md:pb-0">
    <div class="max-w-7xl mx-auto px-4 py-10 grid md:grid-cols-4 gap-8 text-sm">

        <div>
            <h3 class="font-bold text-lg mb-3">
                {{ $siteName }}
            </h3>

            <p class="text-slate-300">
                {{ $siteSetting?->footer_about ?? 'Güncel haberler, kamu ilanları ve duyurular.' }}
            </p>
        </div>

        <div>
            <h3 class="font-bold mb-3">Hızlı Linkler</h3>
            <div class="space-y-2 text-slate-300">
                <div><a href="/">Anasayfa</a></div>
                <div><a href="/haberler">Haberler</a></div>
                <div><a href="/ilanlar">İlanlar</a></div>
            </div>
        </div>

        <div>
            <h3 class="font-bold mb-3">Kategoriler</h3>
            <div class="space-y-2 text-slate-300">
                <div>Memur Alımı</div>
                <div>KPSS</div>
                <div>Akademik İlanlar</div>
            </div>
        </div>

        <div>
            <h3 class="font-bold mb-3">İletişim</h3>
            <div class="space-y-2 text-slate-300">
                <div>
                    <a href="mailto:Argnest@gmail.com" class="theme-link hover:underline">
                        Argnest@gmail.com
                    </a>
                </div>

                @if($siteSetting?->phone)
                    <div>{{ $siteSetting->phone }}</div>
                @endif

                @if($siteSetting?->address)
                    <div>{{ $siteSetting->address }}</div>
                @endif
            </div>
        </div>

    </div>

    <div class="border-t border-slate-700 py-4 text-center text-sm text-slate-400">
        {{ $siteSetting?->footer_copyright ?? '© ' . date('Y') . ' ' . $siteName }}
    </div>

    <div class="border-t border-white/10 px-4 py-4 text-center text-xs leading-6 text-slate-400">
        <div>
            Powered by {{ config('portal.name') }} {{ config('portal.version') }}
        </div>

        <div class="mt-1 text-[11px] text-slate-500">
            {{ config('portal.tagline') }}
        </div>

        <div class="mt-2 inline-flex flex-wrap items-center justify-center gap-1 rounded-full border border-white/10 px-3 py-1 text-[11px] text-slate-400">
            <span>Destek &amp; İletişim:</span>
            <a href="mailto:argnest@gmail.com" class="font-semibold text-slate-200 hover:text-white hover:underline">
                argnest@gmail.com
            </a>
        </div>
    </div>
</footer>

@if($siteSetting?->footer_scripts)
    {!! $siteSetting->footer_scripts !!}
@endif
<script>
(() => {
    if ('serviceWorker' in navigator && !window.location.pathname.startsWith('/admin')) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {});
        });
    }

    let deferredInstallPrompt = null;
    const installBanner = document.getElementById('pwa-install-banner');
    const installButton = installBanner?.querySelector('[data-pwa-install]');
    const installDismiss = installBanner?.querySelector('[data-pwa-dismiss]');

    window.addEventListener('beforeinstallprompt', (event) => {
        if (localStorage.getItem('pwa-install-dismissed') === '1') {
            return;
        }

        event.preventDefault();
        deferredInstallPrompt = event;
        installBanner?.classList.remove('hidden');
    });

    installButton?.addEventListener('click', async () => {
        if (!deferredInstallPrompt) {
            return;
        }

        deferredInstallPrompt.prompt();
        await deferredInstallPrompt.userChoice.catch(() => null);
        deferredInstallPrompt = null;
        installBanner?.classList.add('hidden');
    });

    installDismiss?.addEventListener('click', () => {
        localStorage.setItem('pwa-install-dismissed', '1');
        installBanner?.classList.add('hidden');
    });

    const notificationBanner = document.getElementById('pwa-notification-banner');
    const notificationEnable = notificationBanner?.querySelector('[data-pwa-notification-enable]');
    const notificationDismiss = notificationBanner?.querySelector('[data-pwa-notification-dismiss]');

    if (
        notificationBanner
        && 'Notification' in window
        && Notification.permission === 'default'
        && localStorage.getItem('pwa-notification-dismissed') !== '1'
    ) {
        setTimeout(() => notificationBanner.classList.remove('hidden'), 2500);
    }

    const urlBase64ToUint8Array = (base64String) => {
        if (!/^[A-Za-z0-9_-]+$/.test(base64String || '')) {
            throw new Error('VAPID public key base64url formatinda degil.');
        }

        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const rawData = window.atob(base64);

        if (rawData.length !== 65) {
            throw new Error(`VAPID public key decode uzunlugu hatali: ${rawData.length} byte. Beklenen: 65 byte.`);
        }

        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    };

    const subscribeToPush = async () => {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            return false;
        }

        if (!window.isSecureContext) {
            console.warn('Push subscription secure context gerektirir. Local test icin localhost/127.0.0.1 veya HTTPS/ngrok kullanin.');

            return false;
        }

        const permission = await Notification.requestPermission().catch(() => 'denied');

        if (permission !== 'granted') {
            return false;
        }

        const configResponse = await fetch('{{ route('push.config') }}', {
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!configResponse.ok) {
            return false;
        }

        const config = await configResponse.json();

        const publicKey = config.public_key || config.vapidPublicKey;

        if (!config.can_subscribe || !publicKey) {
            return false;
        }

        const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' }).then(() => navigator.serviceWorker.ready);

        if (!registration.scope.endsWith('/')) {
            console.warn('Service worker scope beklenenden farkli:', registration.scope);

            return false;
        }

        const applicationServerKey = urlBase64ToUint8Array(publicKey);
        let subscription = await registration.pushManager.getSubscription();

        if (!subscription) {
            subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
                applicationServerKey,
            });
        }

        const payload = subscription.toJSON();

        const storeResponse = await fetch('{{ route('push.subscriptions.store') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                endpoint: payload.endpoint,
                keys: payload.keys,
                content_encoding: 'aes128gcm',
                preferences: {
                    enabled: true,
                    types: {},
                },
            }),
        });

        if (!storeResponse.ok) {
            return false;
        }

        window.dispatchEvent(new CustomEvent('push-subscription:updated', {
            detail: {
                subscribed: true,
                sendEnabled: Boolean(config.send_enabled),
            },
        }));

        return true;
    };

    notificationEnable?.addEventListener('click', async () => {
        await subscribeToPush().catch((error) => {
            console.warn('Push subscription failed:', error);

            return false;
        });
        notificationBanner?.classList.add('hidden');
    });

    notificationDismiss?.addEventListener('click', () => {
        localStorage.setItem('pwa-notification-dismissed', '1');
        notificationBanner?.classList.add('hidden');
    });
})();
</script>
<script>
function notificationSystem(initialCount = 0) {
    const manager = window.__appCountPollingManager ??= createAppCountPollingManager();

    return {
        count: initialCount,
        pulse: false,

        init() {
            manager.registerNotification(this, initialCount);
        },

        updateCount(nextCount) {
            if (nextCount > this.count) {
                this.flashBadge();
            }

            this.count = nextCount;
        },

        flashBadge() {
            this.pulse = false;
            requestAnimationFrame(() => {
                this.pulse = true;
                setTimeout(() => this.pulse = false, 450);
            });
        },
    };
}

function createAppCountPollingManager() {
    return {
        notificationCount: 0,
        messageCount: 0,
        notificationSubscribers: new Set(),
        messageSubscribers: new Set(),
        intervalId: null,
        notificationInFlight: false,
        messageInFlight: false,
        visibilityBound: false,
        realtimeNotificationBound: false,
        realtimeMessageBound: false,
        urls: {
            notificationCount: '/bildirimler/count',
            messageCount: null,
        },

        isDisabled() {
            return window.location.pathname.startsWith('/admin');
        },

        hasRealtime() {
            return Boolean(window.Echo);
        },

        intervalMs() {
            return this.hasRealtime() ? 120000 : 30000;
        },

        registerNotification(component, initialCount = 0) {
            if (this.isDisabled()) {
                return;
            }

            this.notificationSubscribers.add(component);
            this.notificationCount = Math.max(this.notificationCount, Number(initialCount || 0));
            component.updateCount(this.notificationCount);
            this.start();
            this.bindNotificationRealtime();
        },

        registerMessage(component) {
            if (this.isDisabled()) {
                return;
            }

            this.messageSubscribers.add(component);
            component.updateCount(this.messageCount);
            this.start();
            this.bindMessageRealtime();
        },

        start() {
            if (this.isDisabled()) {
                return;
            }

            this.bindVisibility();
            this.refreshAll();

            if (this.intervalId) {
                return;
            }

            this.intervalId = setInterval(() => {
                if (document.hidden) {
                    return;
                }

                this.refreshAll();
            }, this.intervalMs());
        },

        bindVisibility() {
            if (this.visibilityBound) {
                return;
            }

            this.visibilityBound = true;

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    this.refreshAll();
                }
            });
        },

        refreshAll() {
            if (document.hidden || this.isDisabled()) {
                return;
            }

            this.fetchNotificationCount();
            this.fetchMessageCount();
        },

        fetchNotificationCount() {
            if (this.notificationInFlight || !this.notificationSubscribers.size) {
                return;
            }

            this.notificationInFlight = true;

            fetch(this.urls.notificationCount, {
                headers: {
                    'Accept': 'application/json',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    this.setNotificationCount(Number(data.count || 0));
                })
                .catch(() => {})
                .finally(() => {
                    this.notificationInFlight = false;
                });
        },

        fetchMessageCount() {
            if (this.messageInFlight || !this.urls.messageCount || !this.messageSubscribers.size) {
                return;
            }

            this.messageInFlight = true;

            fetch(this.urls.messageCount, {
                headers: {
                    'Accept': 'application/json',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    this.setMessageCount(Number(data.count || 0));
                })
                .catch(() => {})
                .finally(() => {
                    this.messageInFlight = false;
                });
        },

        setNotificationCount(count) {
            this.notificationCount = count;
            this.notificationSubscribers.forEach((component) => component.updateCount(count));
        },

        setMessageCount(count) {
            this.messageCount = count;
            this.messageSubscribers.forEach((component) => component.updateCount(count));
        },

        bindNotificationRealtime() {
            if (!window.Echo || this.realtimeNotificationBound) {
                return;
            }

            this.realtimeNotificationBound = true;

            window.Echo.private('users.{{ auth()->id() }}.notifications')
                .listen('.user-notification.created', (notification) => {
                    this.setNotificationCount(this.notificationCount + 1);
                    window.dispatchEvent(new CustomEvent('realtime-ux:toast', {
                        detail: {
                            id: 'notification-' + (notification?.id || Date.now()),
                            kind: notification?.type === 'mention' ? 'mention' : 'notification',
                            title: notification?.title || 'Yeni bildirim',
                            message: notification?.message || 'Bildirim merkezinde yeni bir hareket var.',
                            url: notification?.url || '{{ route('user.notifications') }}',
                        },
                    }));
                });
        },

        bindMessageRealtime() {
            if (!window.Echo || this.realtimeMessageBound) {
                return;
            }

            this.realtimeMessageBound = true;

            window.Echo.private('users.{{ auth()->id() }}.messages')
                .listen('.private-message.sent', (message) => {
                    this.fetchMessageCount();
                    window.dispatchEvent(new CustomEvent('realtime-ux:toast', {
                        detail: {
                            id: 'message-' + (message?.id || Date.now()),
                            kind: 'message',
                            title: message?.sender ? 'Yeni mesaj: ' + message.sender : 'Yeni mesaj',
                            message: message?.body || 'Mesaj kutunuzda yeni bir mesaj var.',
                            url: '{{ route('messages.index') }}',
                        },
                    }));
                });
        },
    };
}
</script>
@auth
<script>
function privateMessageCounter() {
    const manager = window.__appCountPollingManager ??= createAppCountPollingManager();
    manager.urls.messageCount = '{{ route('messages.count') }}';

    return {
        count: 0,
        pulse: false,

        init() {
            manager.registerMessage(this);
        },

        updateCount(nextCount) {
            if (nextCount > this.count) {
                this.flashBadge();
            }

            this.count = nextCount;
        },
        flashBadge() {
            this.pulse = false;
            requestAnimationFrame(() => {
                this.pulse = true;
                setTimeout(() => this.pulse = false, 450);
            });
        },
    };
}

function realtimeMobileUx() {
    return {
        online: navigator.onLine,
        status: window.Echo ? 'connecting' : 'polling',
        toasts: [],
        seenToastIds: new Set(),
        showPill: false,
        get label() {
            if (!this.online) {
                return 'Cevrimdisi';
            }

            if (this.status === 'connected') {
                return 'Canli bagli';
            }

            if (this.status === 'disconnected') {
                return 'Yeniden baglaniyor';
            }

            return 'Sessiz polling';
        },
        init() {
            this.bindNetwork();
            this.bindRealtime();
            this.bindToasts();
            this.updatePill();
        },
        bindNetwork() {
            window.addEventListener('online', () => {
                this.online = true;
                this.pushToast({
                    id: 'network-online-' + Date.now(),
                    kind: 'status',
                    title: 'Tekrar cevrimici',
                    message: 'Canli bildirimler kaldigi yerden devam edecek.',
                }, 2600);
                this.updatePill();
            });

            window.addEventListener('offline', () => {
                this.online = false;
                this.pushToast({
                    id: 'network-offline-' + Date.now(),
                    kind: 'status',
                    title: 'Baglanti koptu',
                    message: 'Sayfa sessizce tekrar baglanmayi deneyecek.',
                }, 4200);
                this.updatePill(true);
            });
        },
        bindRealtime() {
            const connection = window.Echo?.connector?.pusher?.connection;

            if (!connection) {
                this.status = 'polling';
                this.updatePill();

                return;
            }

            this.status = connection.state === 'connected' ? 'connected' : 'connecting';

            connection.bind('state_change', (states) => {
                this.status = states.current === 'connected' ? 'connected' : 'disconnected';
                this.updatePill(states.current !== 'connected');
            });

            connection.bind('connected', () => {
                this.status = 'connected';
                this.updatePill();
            });

            connection.bind('unavailable', () => {
                this.status = 'disconnected';
                this.updatePill(true);
            });

            connection.bind('error', () => {
                this.status = 'disconnected';
                this.updatePill(true);
            });
        },
        bindToasts() {
            window.addEventListener('realtime-ux:toast', (event) => {
                this.pushToast(event.detail || {});
            });
        },
        updatePill(keepVisible = false) {
            this.showPill = keepVisible || !this.online || this.status !== 'connected';

            if (!keepVisible && this.online && this.status === 'connected') {
                setTimeout(() => {
                    if (this.online && this.status === 'connected') {
                        this.showPill = false;
                    }
                }, 1800);
            }
        },
        pushToast(detail, timeout = 5200) {
            if (!detail?.id || this.seenToastIds.has(detail.id)) {
                return;
            }

            this.seenToastIds.add(detail.id);

            const toast = {
                id: detail.id,
                kind: detail.kind || 'notification',
                title: detail.title || 'Yeni hareket',
                message: detail.message || 'Bildirim merkezinde yeni bir hareket var.',
                url: detail.url || '{{ route('user.notifications') }}',
                visible: true,
            };

            this.toasts = [toast, ...this.toasts].slice(0, 3);

            setTimeout(() => {
                toast.visible = false;
                this.toasts = this.toasts.filter((item) => item.id !== toast.id);
            }, timeout);
        },
    };
}

setInterval(() => {
    fetch('{{ route('presence.heartbeat') }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
    }).catch(() => {});
}, 60000);
</script>
@endauth
<style>
    .content-image-lightbox {
        position: fixed;
        inset: 0;
        z-index: 100000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(2, 6, 23, 0.9);
        padding: 64px 18px 24px;
    }

    .content-image-lightbox.is-open {
        display: flex;
    }

    .content-image-lightbox img {
        max-width: min(100%, 1180px);
        max-height: calc(100vh - 96px);
        border-radius: 10px;
        object-fit: contain;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.55);
    }

    .content-image-lightbox__close {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 100001;
        display: inline-flex;
        min-width: 44px;
        height: 44px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.35);
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.92);
        color: #ffffff;
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45);
    }

    .content-image-lightbox__close:hover,
    .content-image-lightbox__close:focus {
        background: #ffffff;
        color: #0f172a;
        outline: none;
    }

    .premium-reading img {
        cursor: zoom-in;
    }

    @media (max-width: 640px) {
        .content-image-lightbox {
            padding: 72px 12px 18px;
        }

        .content-image-lightbox__close {
            top: 12px;
            right: 12px;
            min-width: 48px;
            height: 48px;
            font-size: 32px;
        }
    }
</style>

<div class="content-image-lightbox" data-content-image-lightbox aria-hidden="true">
    <button type="button" class="content-image-lightbox__close" data-content-image-lightbox-close aria-label="Kapat">
        ×
    </button>
    <img src="" alt="">
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const lightbox = document.querySelector('[data-content-image-lightbox]');
    const image = lightbox?.querySelector('img');
    const closeButton = lightbox?.querySelector('[data-content-image-lightbox-close]');

    if (!lightbox || !image || !closeButton) {
        return;
    }

    const close = () => {
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        image.removeAttribute('src');
        image.removeAttribute('alt');
        document.body.style.overflow = '';
    };

    document.addEventListener('click', (event) => {
        const clickedImage = event.target.closest?.('.premium-reading img, [data-content-lightbox-image]');

        if (!clickedImage) {
            return;
        }

        event.preventDefault();
        const nestedImage = clickedImage.matches('img') ? clickedImage : clickedImage.querySelector('img');

        image.src = clickedImage.href || nestedImage?.currentSrc || nestedImage?.src || '';
        image.alt = nestedImage?.alt || clickedImage.getAttribute('aria-label') || '';
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeButton.focus();
    });

    closeButton.addEventListener('click', close);

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && lightbox.classList.contains('is-open')) {
            close();
        }
    });
});
</script>
</body>
</html>
