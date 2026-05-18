@extends('frontend.layout')

@section('title', 'Forum | ' . ($siteSetting?->site_name ?? 'ilanhaber.net'))

@section(
    'meta_description',
    'ilanhaber.net forum alaninda kamu ilanlari, haberler, KPSS, personel alimlari ve guncel konular tartisilir.'
)

@section('meta_keywords', 'forum, kamu ilanlari forum, haber forum, KPSS forum, personel alimi')

@section('canonical', route('forum.index'))

@section('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Forum',
            'description' => 'ilanhaber.net forum alaninda kamu ilanlari, haberler ve guncel konular tartisilir.',
            'url' => route('forum.index'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')

<section class="bg-slate-950 text-white">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 lg:grid-cols-[1.4fr_0.8fr] lg:items-center">
        <div>
            <div class="mb-4 inline-flex items-center rounded-full border border-red-400/40 bg-red-500/10 px-4 py-2 text-xs font-black uppercase tracking-wide text-red-200">
                Topluluk Merkezi
            </div>

            <h1 class="max-w-3xl text-4xl font-black leading-tight md:text-5xl">
                Forum
            </h1>

            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                Haberler, kamu ilanlari, personel alimlari ve gundemdeki basliklar icin premium topluluk alani.
            </p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#forum-kategorileri" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                    Kategorileri Gor
                </a>

                @auth
                    <a href="{{ route('forum.dashboard') }}" class="rounded-lg bg-white px-5 py-3 text-sm font-black text-slate-950 transition hover:bg-slate-100">
                        Forum Panelim
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg bg-white px-5 py-3 text-sm font-black text-slate-950 transition hover:bg-slate-100">
                        Forum Panelim
                    </a>
                @endauth

                <a href="{{ route('live-activity.index') }}" class="rounded-lg border border-white/20 px-5 py-3 text-sm font-black text-white transition hover:bg-white/10">
                    Canli Aktivite
                </a>
            </div>

            <div class="mt-8 grid max-w-2xl grid-cols-3 gap-3">
                <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                    <div class="text-2xl font-black">{{ $forumCategories->sum('topics_count') }}</div>
                    <div class="mt-1 text-xs font-bold text-slate-300">Konu</div>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                    <div class="text-2xl font-black">{{ $forumCategories->sum('solved_topics_count') }}</div>
                    <div class="mt-1 text-xs font-bold text-slate-300">Çözüldü</div>
                </div>

                <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                    <div class="text-2xl font-black">{{ $onlineForumUsersCount }}</div>
                    <div class="mt-1 text-xs font-bold text-slate-300">Online Üye</div>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white/5 p-6 shadow-2xl">
            <div class="text-sm font-bold text-slate-300">Forum Durumu</div>

            @if($siteSetting?->forum_enabled)
                <div class="mt-3 rounded-xl border border-green-400/30 bg-green-500/10 p-4 text-green-100">
                    <div class="text-lg font-black">Aktif</div>
                    <p class="mt-1 text-sm text-green-100/80">Forum modulu kullanima hazir. Konu ve yorum altyapisi sonraki adimda baglanabilir.</p>
                </div>
            @else
                <div class="mt-3 rounded-xl border border-yellow-400/30 bg-yellow-500/10 p-4 text-yellow-100">
                    <div class="text-lg font-black">Panelden Kapali</div>
                    <p class="mt-1 text-sm text-yellow-100/80">Forum gorunumu hazir, yayina almak icin site ayarlarindan aktif edilebilir.</p>
                </div>
            @endif
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pt-10">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-950">Forum Kesfi</h2>
                <p class="mt-1 text-sm text-slate-600">Konu, cevap, kategori ve etiketlere gore forumda hizli arama yapin.</p>
            </div>

            <form method="GET" action="{{ route('forum.index') }}" class="grid w-full gap-3 lg:max-w-4xl lg:grid-cols-[1.4fr_1fr_1fr_auto]">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    class="rounded-lg border-slate-300 text-sm"
                    placeholder="Forumda ara..."
                >

                <select name="category" class="rounded-lg border-slate-300 text-sm">
                    <option value="">Tum kategoriler</option>
                    @foreach($forumCategories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select name="filter" class="rounded-lg border-slate-300 text-sm">
                    <option value="">Yeni ve sabit</option>
                    <option value="trend" @selected(request('filter') === 'trend')>Trend</option>
                    <option value="solved" @selected(request('filter') === 'solved')>Cozulmus</option>
                    <option value="pinned" @selected(request('filter') === 'pinned')>Sabit</option>
                    <option value="open" @selected(request('filter') === 'open')>Cevaba acik</option>
                    <option value="locked" @selected(request('filter') === 'locked')>Kilitli</option>
                </select>

                @if(request('tag'))
                    <input type="hidden" name="tag" value="{{ request('tag') }}">
                @endif

                <button type="submit" class="rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                    Ara
                </button>
            </form>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            @foreach($forumTags as $tag)
                <a
                    href="{{ route('forum.index', array_filter([...request()->except('page'), 'tag' => $tag->slug])) }}"
                    class="rounded-full border px-3 py-1.5 text-xs font-black transition {{ $selectedTag?->id === $tag->id ? 'border-red-600 bg-red-600 text-white' : 'border-slate-200 bg-slate-50 text-slate-700 hover:border-red-200 hover:bg-red-50 hover:text-red-700' }}"
                >
                    #{{ $tag->name }} {{ $tag->topics_count }}
                </a>
            @endforeach

            @if($selectedTag || request()->hasAny(['q', 'category', 'filter']))
                <a href="{{ route('forum.index') }}" class="rounded-full border border-slate-200 px-3 py-1.5 text-xs font-black text-slate-500 transition hover:bg-slate-50">
                    Filtreleri temizle
                </a>
            @endif
        </div>

        <div class="mt-6 overflow-hidden rounded-xl border border-slate-200">
            @forelse($discoveryTopics as $topic)
                <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block border-b border-slate-100 p-5 transition last:border-b-0 hover:bg-slate-50">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                @if($topic->is_pinned)
                                    <span class="rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-black uppercase text-red-700">Sabit</span>
                                @endif

                                @if($topic->is_solved)
                                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-black uppercase text-green-700">Cozuldu</span>
                                @endif

                                <span class="text-xs font-bold text-slate-500">{{ $topic->category?->name }}</span>
                            </div>

                            <h3 class="mt-2 text-lg font-black text-slate-950">{{ $topic->title }}</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $topic->user?->name ?? 'Sistem' }} tarafindan acildi · {{ $topic->created_at?->diffForHumans() }}
                            </p>

                            @if($topic->tags->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($topic->tags as $tag)
                                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-black text-slate-700">#{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-4 text-sm font-bold text-slate-500 lg:justify-end">
                            <span>{{ $topic->posts_count }} cevap</span>
                            <span>{{ $topic->likes_count }} begeni</span>
                            <span>{{ number_format($topic->views) }} goruntulenme</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center">
                    <div class="text-lg font-black text-slate-950">Sonuc bulunamadi</div>
                    <p class="mt-2 text-sm text-slate-600">Arama veya filtreleri genisleterek tekrar deneyin.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $discoveryTopics->links() }}
        </div>
    </div>
</section>

<section class="mx-auto grid max-w-7xl gap-6 px-4 pt-10 lg:grid-cols-[1fr_380px]">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-950">Trend Konular</h2>
                <p class="mt-1 text-sm text-slate-600">Son 14 günde öne çıkan forum başlıkları.</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($trendingForumTopics as $topic)
                <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block rounded-xl border border-slate-100 p-4 transition hover:border-red-100 hover:bg-red-50/40">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($topic->is_pinned)
                            <span class="rounded-full bg-red-600 px-2.5 py-1 text-[11px] font-black uppercase text-white">Sabit</span>
                        @endif

                        @if($topic->is_solved)
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-black uppercase text-green-700">Çözüldü</span>
                        @endif

                        <span class="text-xs font-bold text-slate-500">{{ $topic->category?->name }}</span>
                    </div>

                    <h3 class="mt-2 text-lg font-black text-slate-950">{{ $topic->title }}</h3>

                    @if($topic->tags->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($topic->tags as $tag)
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-black text-slate-700">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-3 flex flex-wrap gap-4 text-xs font-bold text-slate-500">
                        <span>{{ number_format($topic->views) }} görüntülenme</span>
                        <span>{{ $topic->likes_count }} beğeni</span>
                        <span>{{ $topic->bookmarks_count }} favori</span>
                        <span>{{ $topic->posts_count }} cevap</span>
                        <span>Son: {{ $topic->lastPostUser?->name ?? $topic->user?->name ?? 'Sistem' }}</span>
                    </div>

                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs font-bold text-slate-500">
                        <span class="{{ $topic->user?->isOnline() ? 'text-green-600' : 'text-slate-400' }}">
                            {{ $topic->user?->isOnline() ? 'Online' : 'Offline' }}
                        </span>
                        <span>{{ $topic->user?->forum_reputation ?? 0 }} itibar</span>
                        @foreach($topic->user?->forumBadges ?? [] as $badge)
                            <span class="rounded-full bg-slate-100 px-2 py-1 text-[11px] text-slate-700">{{ $badge->name }}</span>
                        @endforeach
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-slate-100 p-5 text-center text-sm font-bold text-slate-500">
                    Trend konu oluşması için biraz hareket gerekiyor.
                </div>
            @endforelse
        </div>
    </div>

    <aside class="space-y-6">
        @auth
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-950 text-lg font-black text-white">
                        {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                    </div>

                    <div>
                        <div class="font-black text-slate-950">{{ auth()->user()->name }}</div>
                        <div class="text-xs font-bold text-slate-500">Forum profil özeti</div>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 p-3 text-center">
                        <div class="text-xl font-black text-slate-950">{{ $myForumTopics->count() }}</div>
                        <div class="text-xs font-bold text-slate-500">Son Konu</div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3 text-center">
                        <div class="text-xl font-black text-slate-950">{{ $myForumPosts->count() }}</div>
                        <div class="text-xs font-bold text-slate-500">Son Cevap</div>
                    </div>
                </div>

                <a href="{{ route('forum.dashboard') }}" class="mt-5 inline-flex w-full justify-center rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-red-700">
                    Forum Panelim
                </a>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-xl font-black text-slate-950">Forum Gönderilerim</h2>

                <div class="mt-4 space-y-4">
                    <div>
                        <div class="mb-2 text-xs font-black uppercase text-slate-400">Konular</div>
                        @forelse($myForumTopics as $topic)
                            <div class="border-b border-slate-100 py-2 last:border-b-0">
                                <div class="text-sm font-black text-slate-800">{{ $topic->title }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-500">{{ $topic->category?->name }} · {{ $topic->status }}</div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">Henüz konu açmadınız.</div>
                        @endforelse
                    </div>

                    <div>
                        <div class="mb-2 text-xs font-black uppercase text-slate-400">Cevaplar</div>
                        @forelse($myForumPosts as $post)
                            <div class="border-b border-slate-100 py-2 last:border-b-0">
                                <div class="text-sm font-black text-slate-800">{{ $post->topic?->title ?? 'Silinmiş konu' }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-500">{{ $post->status }} · {{ $post->created_at?->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">Henüz cevap yazmadınız.</div>
                        @endforelse
                    </div>

                    <div>
                        <div class="mb-2 text-xs font-black uppercase text-slate-400">Favoriler</div>
                        @forelse($myBookmarkedTopics as $topic)
                            <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block border-b border-slate-100 py-2 last:border-b-0">
                                <div class="text-sm font-black text-slate-800">{{ $topic->title }}</div>
                                <div class="mt-1 text-xs font-bold text-slate-500">{{ $topic->created_at?->diffForHumans() }}</div>
                            </a>
                        @empty
                            <div class="text-sm text-slate-500">Henüz favori konunuz yok.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-6 shadow-sm">
                <h2 class="text-xl font-black text-blue-900">Forum profiliniz burada görünecek</h2>
                <p class="mt-2 text-sm leading-6 text-blue-800/80">Giriş yapan üyeler kendi konularını, cevaplarını ve moderasyon durumlarını buradan takip eder.</p>
            </div>
        @endauth
    </aside>
</section>

<section class="mx-auto max-w-7xl px-4 pt-10">
    @if(session('success'))
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-bold text-green-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800">
            {{ session('error') }}
        </div>
    @endif

    @if($siteSetting?->forum_enabled)
        @auth
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-5">
                    <h2 class="text-2xl font-black text-slate-950">Yeni Konu Aç</h2>
                    <p class="mt-1 text-sm text-slate-600">Konular moderatör onayından sonra forumda yayınlanır.</p>
                </div>

                <form method="POST" action="{{ route('forum.topics.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-black text-slate-700">Kategori</label>
                            <select name="forum_category_id" required class="mt-2 w-full rounded-lg border-slate-300 text-sm">
                                <option value="">Kategori seçin</option>
                                @foreach($forumCategories as $category)
                                    <option value="{{ $category->id }}" @selected(old('forum_category_id') == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('forum_category_id')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-black text-slate-700">Başlık</label>
                            <input
                                type="text"
                                name="title"
                                value="{{ old('title') }}"
                                required
                                class="mt-2 w-full rounded-lg border-slate-300 text-sm"
                                placeholder="Konu başlığı"
                            >
                            @error('title')
                                <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">Etiketler</label>
                        <input
                            type="text"
                            name="tag_names"
                            value="{{ old('tag_names') }}"
                            class="mt-2 w-full rounded-lg border-slate-300 text-sm"
                            placeholder="ornek: KPSS, personel alimi, gundem"
                        >
                        <p class="mt-2 text-xs font-bold text-slate-500">Virgulle ayirin. En fazla 5 etiket baglanir.</p>
                        @error('tag_names')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm font-black text-slate-700">İçerik</label>
                        <div class="mt-2">
                            @include('frontend.partials.forum-rich-editor', [
                                'id' => 'forum-topic-editor',
                                'name' => 'content',
                                'value' => old('content'),
                                'placeholder' => 'Konunuzu yazin...',
                            ])
                        </div>
                        @error('content')
                            <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                        Moderasyona Gönder
                    </button>
                </form>
            </div>
        @else
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-6 shadow-sm">
                <h2 class="text-xl font-black text-blue-900">Konu açmak için giriş yapın</h2>
                <p class="mt-2 text-sm text-blue-800/80">Forumda konu açma ve cevap yazma işlemleri üyeler içindir.</p>
            </div>
        @endauth
    @endif
</section>

<section id="forum-kategorileri" class="mx-auto max-w-7xl px-4 py-10">
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
        @forelse($forumCategories as $category)
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <div class="text-lg font-black text-slate-950">{{ $category->name }}</div>
                <p class="mt-3 min-h-12 text-sm leading-6 text-slate-600">
                    {{ $category->description ?: 'Bu kategorideki guncel forum basliklarini takip edin.' }}
                </p>
                <div class="mt-5 text-xs font-black uppercase text-red-600">
                    {{ $category->topics_count }} konu
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm md:col-span-2 xl:col-span-4">
                <div class="text-xl font-black text-slate-950">Forum kategorisi henuz eklenmedi</div>
                <p class="mt-2 text-sm text-slate-600">Panelden kategori ve konu eklendiginde burada listelenecek.</p>
            </div>
        @endforelse
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-12">
    <div class="mb-5 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-950">Son Forum Konulari</h2>
            <p class="mt-1 text-sm text-slate-600">Topluluktaki en yeni ve sabitlenen basliklar.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @forelse($latestForumTopics as $topic)
            <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block border-b border-slate-100 p-5 transition last:border-b-0 hover:bg-slate-50">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                        @if($topic->is_pinned)
                            <span class="rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-black uppercase text-red-700">Sabit</span>
                        @endif

                        @if($topic->is_solved)
                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-black uppercase text-green-700">Çözüldü</span>
                        @endif

                        <span class="text-xs font-bold text-slate-500">{{ $topic->category?->name }}</span>
                    </div>

                        <h3 class="mt-2 text-lg font-black text-slate-950">{{ $topic->title }}</h3>
                    @if($topic->tags->isNotEmpty())
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($topic->tags as $tag)
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-black text-slate-700">#{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $topic->user?->name ?? 'Sistem' }} tarafindan acildi
                        · {{ $topic->user?->forum_reputation ?? 0 }} itibar
                        · {{ $topic->user?->isOnline() ? 'Online' : 'Offline' }}
                    </p>
                </div>

                <div class="flex gap-4 text-sm font-bold text-slate-500">
                    <span>{{ $topic->likes_count }} beğeni</span>
                    <span>{{ $topic->bookmarks_count }} favori</span>
                    <span>{{ $topic->posts_count }} cevap</span>
                    <span>{{ number_format($topic->views) }} goruntulenme</span>
                    <span>Son: {{ $topic->lastPostUser?->name ?? $topic->user?->name ?? 'Sistem' }}</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="p-8 text-center">
                <div class="text-lg font-black text-slate-950">Henuz konu yok</div>
                <p class="mt-2 text-sm text-slate-600">Ilk forum konulari panelden eklendiginde burada gorunecek.</p>
            </div>
        @endforelse
    </div>
</section>

@endsection
