@extends('frontend.layout')

@php
    $siteSetting = \App\Models\SiteSetting::first();
@endphp

@section('title', 'Videolar')

@section(
    'meta_description',
    'Güncel haber videoları, kamu ilanları, personel alımı videoları ve son gelişmeler.'
)

@section(
    'meta_keywords',
    'videolar, haber videoları, ilan videoları, kamu ilanları'
)

@section('canonical', route('videos.index'))

@section('content')

<div class="container mx-auto px-3 py-5 md:px-4 md:py-8">

    <div class="mb-5 flex items-center justify-between rounded-2xl bg-white p-5 shadow-sm md:mb-8 md:bg-transparent md:p-0 md:shadow-none">

        <div>

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl">
                🎥 Videolar
            </h1>

            <p class="mt-2 text-gray-500">
                Son eklenen videolar
            </p>

        </div>

    </div>

    @if($videos->count())

        <div class="grid grid-cols-2 gap-3 md:grid-cols-2 md:gap-6 xl:grid-cols-3">

            @foreach($videos as $video)

                <a href="{{ route('videos.show', $video->slug) }}"
                   class="group overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-900 md:shadow">

                    <div class="relative overflow-hidden">

                        @if($video->thumbnail)

                            <img
                                src="{{ asset('storage/' . $video->thumbnail) }}"
                                alt="{{ $video->title }}"
                                class="h-32 w-full object-cover transition duration-300 group-hover:scale-105 md:h-56"
                            >

                        @else

                            <div class="flex h-32 items-center justify-center bg-gray-200 text-4xl dark:bg-gray-800 md:h-56 md:text-5xl">
                                🎬
                            </div>

                        @endif

                        <div class="absolute bottom-3 left-3 rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-lg">
                            VIDEO
                        </div>

                    </div>

                    <div class="p-3 md:p-5">

                        <h2 class="line-clamp-2 text-sm font-bold text-gray-900 dark:text-white md:text-lg">
                            {{ $video->title }}
                        </h2>

                        <div class="mt-3 flex flex-col gap-1 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between md:text-sm">

                            <span>
                                👁 {{ number_format($video->views) }}
                            </span>

                            <span>
                                {{ $video->created_at->diffForHumans() }}
                            </span>

                        </div>

                    </div>

                </a>

            @endforeach

        </div>

        <div class="mt-10">

            {{ $videos->links() }}

        </div>

    @else

        <div class="rounded-2xl bg-white p-10 text-center shadow dark:bg-gray-900">

            <div class="text-5xl">
                🎥
            </div>

            <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                Henüz video bulunmuyor
            </h2>

        </div>

    @endif

</div>

@endsection




