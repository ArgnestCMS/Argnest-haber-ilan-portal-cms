@extends('frontend.layout')

@section('title', $poll->title . ' | Anket')
@section('meta_description', $poll->subtitle ?: Str::limit(strip_tags($poll->description), 160))
@section('canonical', route('polls.show', $poll->slug))

@section('content')
@php
    $showResults = $poll->share_results && ($hasVoted || session('poll_success'));
@endphp

<section class="max-w-5xl mx-auto px-3 mt-4 md:px-4 md:mt-6">
    <article class="premium-article overflow-hidden">
        @if($poll->image)
            <img src="{{ asset('storage/' . $poll->image) }}" alt="{{ $poll->title }}" class="h-64 w-full object-cover md:h-96">
        @endif

        <div class="p-5 md:p-8">
            <div class="mb-3 text-sm font-black text-blue-700">{{ $poll->topic ?: 'Anket' }}</div>
            <h1 class="text-3xl font-black leading-tight text-slate-950 md:text-5xl">{{ $poll->title }}</h1>

            @if($poll->subtitle)
                <p class="mt-4 text-lg font-semibold leading-8 text-slate-600">{{ $poll->subtitle }}</p>
            @endif

            @if($poll->description)
                <div class="premium-reading prose mt-6 max-w-none">
                    {!! $poll->description !!}
                </div>
            @endif

            @if(session('poll_error'))
                <div class="mt-6 rounded-2xl border border-red-100 bg-red-50 p-4 font-bold text-red-700">
                    {{ session('poll_error') }}
                </div>
            @endif

            @if(session('poll_success'))
                <div class="mt-6 rounded-2xl border border-green-100 bg-green-50 p-4 font-bold text-green-700">
                    {{ session('poll_success') }}
                    @unless($poll->share_results)
                        Sonuçlar bu anket için paylaşılmıyor.
                    @endunless
                </div>
            @endif

            @if(! $hasVoted)
                <form method="POST" action="{{ route('polls.vote', $poll) }}" class="mt-8 space-y-4">
                    @csrf

                    @foreach($poll->activeOptions as $option)
                        <label class="flex cursor-pointer gap-4 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-blue-300">
                            <input
                                type="{{ $poll->allow_multiple ? 'checkbox' : 'radio' }}"
                                name="{{ $poll->allow_multiple ? 'option_ids[]' : 'option_id' }}"
                                value="{{ $option->id }}"
                                class="mt-1"
                                {{ $poll->allow_multiple ? '' : 'required' }}
                            >

                            @if($option->image)
                                <img src="{{ asset('storage/' . $option->image) }}" alt="{{ $option->title }}" class="h-16 w-20 rounded-xl object-cover">
                            @endif

                            <span class="min-w-0">
                                <span class="block font-black text-slate-950">{{ $option->title }}</span>
                                @if($option->description)
                                    <span class="mt-1 block text-sm leading-6 text-slate-500">{{ $option->description }}</span>
                                @endif
                            </span>
                        </label>
                    @endforeach

                    <button class="rounded-full bg-blue-700 px-6 py-3 font-black text-white transition hover:bg-blue-800">
                        Oy Ver
                    </button>
                </form>
            @endif

            @if($showResults)
                <div class="mt-10 border-t border-slate-100 pt-8">
                    <h2 class="text-2xl font-black text-slate-950">Sonuçlar</h2>
                    <div class="mt-5 space-y-4">
                        @foreach($poll->activeOptions as $option)
                            @php
                                $percent = $totalVotes > 0 ? round(($option->votes_count / $totalVotes) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3 text-sm font-black">
                                    <span>{{ $option->title }}</span>
                                    <span>{{ $percent }}% · {{ $option->votes_count }} oy</span>
                                </div>
                                <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-blue-700" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($hasVoted && ! $poll->share_results)
                <div class="mt-8 rounded-2xl border border-green-100 bg-green-50 p-5 font-bold text-green-700">
                    Katılımınız için teşekkürler. Sonuçlar bu anket için paylaşılmıyor.
                </div>
            @endif
        </div>
    </article>
</section>
@endsection
