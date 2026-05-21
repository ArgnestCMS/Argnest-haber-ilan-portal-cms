@extends('frontend.layout')

@section('title', 'Anketler | ' . ($siteSetting?->site_name ?? config('app.name')))
@section('meta_description', 'Güncel anketler ve katılım sayfası.')
@section('canonical', route('polls.index'))

@section('content')
<section class="max-w-7xl mx-auto px-3 mt-4 md:px-4 md:mt-6">
    <div class="premium-page-shell mb-5 p-5 md:p-7">
        <span class="premium-kicker border-blue-200 bg-blue-50 text-blue-700">ANKETLER</span>
        <h1 class="mt-3 text-3xl font-black text-slate-950 md:text-4xl">Aktif Anketler</h1>
        <p class="mt-2 text-slate-500">Gündemdeki sorulara katılın, sonuçları takip edin.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse($polls as $poll)
            <a href="{{ route('polls.show', $poll->slug) }}" class="premium-card premium-card-hover block overflow-hidden">
                @if($poll->image)
                    <img src="{{ asset('storage/' . $poll->image) }}" alt="{{ $poll->title }}" class="h-44 w-full object-cover">
                @endif

                <div class="p-5">
                    <div class="mb-3 flex items-center justify-between gap-3 text-xs font-black text-slate-500">
                        <span>{{ $poll->topic ?: 'Anket' }}</span>
                        <span>{{ $poll->activeOptions->count() }} seçenek</span>
                    </div>

                    <h2 class="text-xl font-black leading-7 text-slate-950">{{ $poll->title }}</h2>

                    @if($poll->subtitle)
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ Str::limit($poll->subtitle, 120) }}</p>
                    @endif

                    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4 text-sm font-bold">
                        <span class="text-blue-700">Oy ver</span>
                        <span class="text-slate-400">{{ $poll->options->sum('votes_count') }} oy</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="premium-card col-span-full p-8 text-center text-slate-500">
                Aktif anket bulunmuyor.
            </div>
        @endforelse
    </div>

    <div class="premium-pagination mt-8">
        {{ $polls->links() }}
    </div>
</section>
@endsection
