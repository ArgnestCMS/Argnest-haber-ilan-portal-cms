@extends('frontend.layout')

@section('title', 'Canli Aktivite | ' . ($siteSetting?->site_name ?? 'ilanhaber.net'))

@section(
    'meta_description',
    'Canli sohbet, canli yayin, duyurular ve forum baglantilari icin ilanhaber.net canli aktivite merkezi.'
)

@section('meta_keywords', 'canli aktivite, canli sohbet, canli yayin, canli duyuru, forum')

@section('canonical', route('live-activity.index'))

@section('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Canli Aktivite',
            'description' => 'Canli sohbet, canli yayin, duyurular ve forum baglantilari icin canli aktivite merkezi.',
            'url' => route('live-activity.index'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')

<section
    x-data="liveActivityFeed({
        latestUrl: '{{ route('live-activity.latest') }}',
        initialActivities: @js($activities),
    })"
    x-init="init()"
    class="bg-white"
>
    <div class="mx-auto max-w-7xl px-4 py-12">
        <div class="mb-8">
            <div class="mb-3 inline-flex rounded-full bg-red-50 px-4 py-2 text-xs font-black uppercase text-red-700">
                Canli Merkez
            </div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h1 class="text-4xl font-black text-slate-950 md:text-5xl">
                        Canli Aktivite
                    </h1>

                    <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                        Forum, sohbet ve kullanıcı hareketlerini polling tabanlı tek akışta takip edin.
                    </p>
                </div>

                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <span class="h-2.5 w-2.5 rounded-full" :class="isRefreshing ? 'bg-yellow-500' : 'bg-green-500'"></span>
                    <div>
                        <div class="text-xs font-black uppercase text-slate-500">Polling</div>
                        <div class="text-sm font-black text-slate-950" x-text="lastPolledAt ? 'Son kontrol ' + lastPolledAt : 'Hazırlanıyor'"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-slate-200 bg-slate-950 px-6 py-5 text-white md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-2xl font-black">Son Aktiviteler</h2>
                        <p class="mt-1 text-sm text-slate-300">Forum, sohbet ve oturum hareketleri</p>
                    </div>

                    <div class="flex flex-wrap gap-2 text-xs font-black">
                        <span class="rounded-full bg-white/10 px-3 py-1" x-text="activities.length + ' kayıt'"></span>
                        <span class="rounded-full bg-white/10 px-3 py-1">5 sn</span>
                    </div>
                </div>

                <div class="min-h-[540px] bg-slate-50 p-5">
                    <template x-if="activities.length === 0">
                        <div class="flex min-h-[500px] items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white text-center">
                            <div>
                                <div class="text-xl font-black text-slate-950">Henüz aktivite yok</div>
                                <p class="mt-2 text-sm text-slate-500">Yeni forum, sohbet veya oturum hareketleri burada görünecek.</p>
                            </div>
                        </div>
                    </template>

                    <div class="space-y-3">
                        <template x-for="activity in activities" :key="activity.id">
                            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div class="min-w-0">
                                        <div class="mb-2 flex flex-wrap items-center gap-2">
                                            <span
                                                class="rounded-full px-2.5 py-1 text-[11px] font-black uppercase"
                                                :class="sourceClass(activity.source)"
                                                x-text="sourceLabel(activity.source)"
                                            ></span>
                                            <span
                                                class="rounded-full px-2.5 py-1 text-[11px] font-black uppercase"
                                                :class="severityClass(activity.severity)"
                                                x-text="severityLabel(activity.severity)"
                                            ></span>
                                        </div>

                                        <h3 class="text-base font-black text-slate-950" x-html="activity.title"></h3>
                                        <p x-show="activity.message" class="mt-1 text-sm leading-6 text-slate-600" x-html="activity.message"></p>

                                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-400">
                                            <span x-text="activity.user"></span>
                                            <span>·</span>
                                            <span x-text="activity.relative_time"></span>
                                            <span>·</span>
                                            <span x-text="activity.date + ' ' + activity.time"></span>
                                        </div>
                                    </div>

                                    <template x-if="activity.url">
                                        <a :href="activity.url" class="shrink-0 rounded-lg bg-slate-900 px-4 py-2 text-center text-xs font-black text-white transition hover:bg-red-700">
                                            Aç
                                        </a>
                                    </template>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            </div>

            <aside class="space-y-5">
                <a href="{{ route('live-chat.index') }}" class="block rounded-2xl border border-red-100 bg-red-50 p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black text-red-800">Canli Sohbet</h2>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-red-700">
                            {{ $siteSetting?->live_chat_enabled ? 'Aktif' : 'Kapali' }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-red-700/80">
                        Uyeler icin anlik sohbet ve topluluk etkilesimi alani.
                    </p>
                </a>

                <div class="rounded-2xl border border-slate-200 bg-slate-950 p-6 text-white shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black">Canli Yayin</h2>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black text-slate-200">
                            {{ $siteSetting?->live_stream_enabled ? 'Aktif' : 'Kapali' }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-slate-300">
                        {{ $siteSetting?->live_stream_description ?: 'Aktif yayin basladiginda bu alanda gosterilecek.' }}
                    </p>

                    @if($siteSetting?->live_stream_url)
                        <a href="{{ $siteSetting->live_stream_url }}" target="_blank" rel="noopener" class="mt-5 inline-flex rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white transition hover:bg-red-700">
                            Yayina Git
                        </a>
                    @endif
                </div>

                <div class="rounded-2xl border border-yellow-100 bg-yellow-50 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-black text-yellow-800">Canli Duyuru</h2>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-black text-yellow-700">
                            {{ $siteSetting?->live_announcement_enabled ? 'Aktif' : 'Kapali' }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-yellow-800/80">
                        {{ $siteSetting?->live_announcement_text ?: 'Guncel duyurular panelden tanimlandiginda burada yer alacak.' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                    <h2 class="text-xl font-black text-blue-900">Forum Baglantisi</h2>
                    <p class="mt-2 text-sm text-blue-800/80">Topluluk basliklari ve tartismalara forum alanindan devam edin.</p>

                    <a href="{{ route('forum.index') }}" class="mt-5 inline-flex rounded-lg bg-blue-700 px-5 py-3 text-center text-sm font-black text-white transition hover:bg-blue-800">
                        Foruma Git
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

<script>
function liveActivityFeed(config) {
    return {
        activities: config.initialActivities || [],
        latestId: Math.max(0, ...(config.initialActivities || []).map(activity => activity.id || 0)),
        isRefreshing: false,
        lastPolledAt: '',
        init() {
            this.fetchActivities();
            this.listenForActivities();
            setInterval(() => this.fetchActivities(), 5000);
        },
        listenForActivities() {
            if (!window.Echo) {
                return;
            }

            window.Echo.channel('live.activities')
                .listen('.live-activity.recorded', (event) => {
                    const activity = event.activity;

                    if (!activity || this.activities.some((item) => item.id === activity.id)) {
                        return;
                    }

                    this.activities = [activity, ...this.activities]
                        .sort((first, second) => second.id - first.id)
                        .slice(0, 30);

                    this.latestId = Math.max(this.latestId, activity.id || 0);
                });
        },
        fetchActivities() {
            this.isRefreshing = true;

            fetch(config.latestUrl + '?after_id=' + this.latestId, {
                headers: { 'Accept': 'application/json' },
            })
                .then(response => response.json())
                .then(data => {
                    const fresh = data.activities || [];

                    if (fresh.length > 0) {
                        this.activities = [...fresh, ...this.activities]
                            .filter((activity, index, all) => all.findIndex(item => item.id === activity.id) === index)
                            .sort((first, second) => second.id - first.id)
                            .slice(0, 30);

                        this.latestId = Math.max(this.latestId, ...fresh.map(activity => activity.id || 0));
                    }

                    this.lastPolledAt = data.polled_at || '';
                })
                .finally(() => {
                    this.isRefreshing = false;
                });
        },
        sourceLabel(source) {
            return {
                auth: 'Oturum',
                forum: 'Forum',
                chat: 'Sohbet',
                system: 'Sistem',
            }[source] || 'Sistem';
        },
        severityLabel(severity) {
            return {
                info: 'Bilgi',
                success: 'Aktif',
                warning: 'Uyarı',
                danger: 'Kritik',
            }[severity] || 'Bilgi';
        },
        sourceClass(source) {
            return {
                auth: 'bg-blue-50 text-blue-700',
                forum: 'bg-red-50 text-red-700',
                chat: 'bg-green-50 text-green-700',
                system: 'bg-slate-100 text-slate-700',
            }[source] || 'bg-slate-100 text-slate-700';
        },
        severityClass(severity) {
            return {
                info: 'bg-slate-100 text-slate-700',
                success: 'bg-green-50 text-green-700',
                warning: 'bg-yellow-50 text-yellow-800',
                danger: 'bg-red-50 text-red-700',
            }[severity] || 'bg-slate-100 text-slate-700';
        },
    }
}
</script>

@endsection
