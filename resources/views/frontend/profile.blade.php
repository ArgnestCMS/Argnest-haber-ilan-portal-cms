@extends('frontend.layout')

@section('title', $user->name . ' Profili')

@section('content')

<section class="max-w-6xl mx-auto px-4 mt-8">

    <div class="bg-white shadow rounded-3xl overflow-hidden">

        <div class="bg-gradient-to-r from-blue-700 to-slate-900 h-48"></div>

        <div class="p-8">

            <div class="-mt-24 flex flex-col md:flex-row md:items-end gap-6">

                <div class="w-40 h-40 rounded-3xl overflow-hidden border-8 border-white shadow-xl bg-slate-100">

                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl font-black text-slate-400">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                </div>

                <div class="pb-4">
                    <h1 class="text-4xl font-black text-slate-900">
                        {{ $user->name }}
                    </h1>

                    <p class="text-slate-500 mt-2">
                        {{ $user->email }}
                    </p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <span class="rounded-full {{ $user->isOnline() ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }} px-3 py-1 text-xs font-black">
                            {{ $user->isOnline() ? 'Online' : 'Offline' }}
                        </span>

                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-black text-red-700">
                            {{ $user->forum_reputation ?? 0 }} itibar
                        </span>

                        <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-black text-indigo-700">
                            Seviye {{ $user->forum_level ?? 1 }}
                        </span>

                        <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-black text-amber-700">
                            {{ number_format($user->forum_xp ?? 0) }} XP
                        </span>

                        @foreach($user->forumBadges as $badge)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-700">
                                {{ $badge->name }}
                            </span>
                        @endforeach
                    </div>

                    @auth
                        @if(auth()->id() === $user->id)
                            <div class="mt-5">
                                <a href="{{ route('forum.dashboard') }}" class="inline-flex rounded-lg bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700">
                                    Forum Panelim
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>

            </div>

            @if($user->bio)
                <div class="mt-8 bg-slate-50 border border-slate-200 rounded-2xl p-6">
                    <h2 class="font-black text-xl mb-4">
                        Hakkında
                    </h2>

                    <div class="text-slate-700 leading-8">
                        {{ $user->bio }}
                    </div>
                </div>
            @endif

            <div class="grid md:grid-cols-4 gap-6 mt-8">

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">Seviye</div>
                    <div class="text-4xl font-black mt-2">{{ $user->forum_level ?? 1 }}</div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full bg-red-600" style="width: {{ $levelProgress['percent'] ?? 0 }}%"></div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">XP</div>
                    <div class="text-4xl font-black mt-2">{{ number_format($user->forum_xp ?? 0) }}</div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">Forum Konusu</div>
                    <div class="text-4xl font-black mt-2">{{ $forumStats['topics'] ?? 0 }}</div>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 border">
                    <div class="text-sm text-slate-500">Forum Cevabı</div>
                    <div class="text-4xl font-black mt-2">{{ $forumStats['posts'] ?? 0 }}</div>
                </div>

            </div>

            <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-6">
                <h2 class="text-xl font-black text-slate-950">Forum Konuları</h2>

                <div class="mt-4 divide-y divide-slate-200">
                    @forelse($latestForumTopics as $topic)
                        <a href="{{ route('forum.topics.show', $topic->slug) }}" class="block py-3">
                            <div class="font-black text-slate-800">{{ $topic->title }}</div>
                            <div class="mt-1 text-xs font-bold text-slate-500">
                                {{ $topic->created_at?->diffForHumans() }} · {{ number_format($topic->views) }} görüntülenme
                            </div>
                        </a>
                    @empty
                        <div class="py-4 text-sm text-slate-500">
                            Bu kullanıcının yayınlanmış forum konusu yok.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</section>

@endsection
