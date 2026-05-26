@extends('frontend.layout')

@section('title', $query ? '"' . $query . '" arama sonuçları' : 'Arama')
@section('meta_description', 'Haber, ilan, forum konusu, forum cevabı, kullanıcı ve etiketlerde arama yapın.')

@section('content')
@php
    $typeLabels = [
        null => 'Tümü',
        'news' => 'Haberler',
        'announcements' => 'İlanlar',
        'forum_topics' => 'Forum Konuları',
        'forum_posts' => 'Forum Cevapları',
        'users' => 'Kullanıcılar',
        'tags' => 'Etiketler',
    ];
    $total = collect($sections)->sum(fn ($items) => $items->count());
@endphp

<section class="mx-auto mt-6 max-w-7xl px-4">
    <div class="border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <span class="inline-flex bg-blue-700 px-3 py-1 text-xs font-black text-white">ARAMA</span>
                <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">
                    {{ $query ? '"' . $query . '" için sonuçlar' : 'Site içi arama' }}
                </h1>
                <p class="mt-2 text-sm font-semibold text-slate-500">
                    Haber, ilan, forum, kullanıcı ve etiketlerde public içerikler aranır.
                </p>
            </div>

            <div class="rounded-xl bg-slate-100 px-4 py-3 text-sm font-black text-slate-700">
                {{ $total }} sonuç
            </div>
        </div>

        <form action="{{ route('search') }}" method="GET" class="mt-6 grid gap-3 md:grid-cols-[1fr_220px_auto]" x-data="instantSearch()">
            <div class="relative">
                <input
                    type="search"
                    name="q"
                    value="{{ $query }}"
                    placeholder="Haber, ilan, forum konusu, etiket veya kullanıcı ara"
                    autocomplete="off"
                    class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm font-bold text-slate-900"
                    @input.debounce.250ms="fetchResults($event.target.value)"
                    @focus="open = true"
                >

                <div
                    x-show="open && results.length"
                    @click.outside="open = false"
                    class="absolute z-40 mt-2 max-h-96 w-full overflow-auto rounded-xl border border-slate-200 bg-white shadow-2xl"
                    style="display:none;"
                >
                    <template x-for="item in results" :key="item.type + item.url">
                        <a :href="item.url" class="block border-b border-slate-100 p-4 transition hover:bg-slate-50">
                            <div class="flex items-center gap-2">
                                <span class="rounded bg-slate-900 px-2 py-1 text-[11px] font-black text-white" x-text="item.type_label"></span>
                                <span class="text-xs font-bold text-slate-400" x-text="item.meta"></span>
                            </div>
                            <div class="mt-2 text-sm font-black text-slate-900" x-text="item.title"></div>
                            <div class="mt-1 text-xs font-semibold leading-5 text-slate-500" x-text="item.excerpt"></div>
                        </a>
                    </template>
                </div>
            </div>

            <select name="type" class="rounded-xl border-slate-300 px-4 py-3 text-sm font-bold text-slate-700">
                @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" @selected($type === $value || ($value === null && ! $type))>{{ $label }}</option>
                @endforeach
            </select>

            <button class="rounded-xl bg-slate-950 px-6 py-3 text-sm font-black text-white transition hover:bg-blue-700">
                Ara
            </button>
        </form>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach($typeLabels as $value => $label)
            <a href="{{ route('search', array_filter(['q' => $query, 'type' => $value])) }}"
               class="rounded-full px-4 py-2 text-xs font-black transition {{ ($type === $value || ($value === null && ! $type)) ? 'bg-blue-700 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if($suggestions->isNotEmpty() || $trending->isNotEmpty())
        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            <div class="border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-black uppercase text-slate-400">Öneriler</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse($suggestions as $suggestion)
                        <a href="{{ $suggestion['url'] }}" class="rounded-full bg-blue-50 px-3 py-2 text-xs font-black text-blue-700 hover:bg-blue-100">
                            {{ $suggestion['query'] }}
                        </a>
                    @empty
                        <span class="text-sm font-semibold text-slate-500">AI-assisted öneri için arama geçmişi birikiyor.</span>
                    @endforelse
                </div>
            </div>

            <div class="border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-black uppercase text-slate-400">Trend Aramalar</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse($trending as $item)
                        <a href="{{ $item['url'] }}" class="rounded-full bg-slate-100 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-200">
                            {{ $item['query'] }}
                        </a>
                    @empty
                        <span class="text-sm font-semibold text-slate-500">Henüz trend arama yok.</span>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6 grid gap-6 lg:grid-cols-[280px_1fr]">
        <aside class="space-y-3">
            @foreach($sections as $sectionKey => $items)
                <a href="{{ route('search', array_filter(['q' => $query, 'type' => $sectionKey])) }}"
                   class="flex items-center justify-between border border-slate-200 bg-white px-4 py-3 text-sm font-black shadow-sm transition hover:bg-slate-50">
                    <span>{{ $typeLabels[$sectionKey] ?? $sectionKey }}</span>
                    <span class="rounded bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $items->count() }}</span>
                </a>
            @endforeach
        </aside>

        <div class="space-y-6">
            @forelse($sections as $sectionKey => $items)
                @continue($type && $type !== $sectionKey)

                <div class="overflow-hidden border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                        <h2 class="text-xl font-black text-slate-950">{{ $typeLabels[$sectionKey] ?? $sectionKey }}</h2>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600">{{ $items->count() }}</span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($items as $item)
                            <a href="{{ $item['url'] }}" class="block p-5 transition hover:bg-slate-50">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded bg-slate-950 px-2 py-1 text-[11px] font-black text-white">{{ $item['type_label'] }}</span>
                                    <span class="text-xs font-bold text-slate-400">{{ $item['meta'] }}</span>
                                </div>
                                <h3 class="mt-2 text-lg font-black text-slate-900 hover:text-blue-700">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">{{ $item['excerpt'] }}</p>
                            </a>
                        @empty
                            <div class="p-6 text-sm font-semibold text-slate-500">Bu bölümde sonuç yok.</div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="border border-slate-200 bg-white p-10 text-center shadow-sm">
                    <h2 class="text-xl font-black text-slate-900">Sonuç bulunamadı</h2>
                    <p class="mt-2 text-sm font-semibold text-slate-500">Farklı bir kelime veya daha kısa bir arama deneyin.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
function instantSearch() {
    return {
        open: false,
        results: [],
        async fetchResults(query) {
            if (!query || query.trim().length < 2) {
                this.results = [];
                return;
            }

            const params = new URLSearchParams({ q: query.trim() });
            const response = await fetch(`{{ route('search.instant') }}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            }).catch(() => null);

            if (!response || !response.ok) {
                this.results = [];
                return;
            }

            const data = await response.json();
            this.results = data.results || [];
            this.open = this.results.length > 0;
        },
    };
}
</script>
@endsection




