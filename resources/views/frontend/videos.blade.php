@extends('frontend.layout')

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

<div class="container mx-auto px-4 py-8">

    <div class="mb-8 flex items-center justify-between">

        <div>

            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                🎥 Videolar
            </h1>

            <p class="mt-2 text-gray-500">
                Son eklenen videolar
            </p>

        </div>

    </div>

    @if($videos->count())

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

            @foreach($videos as $video)

                <a href="{{ route('videos.show', $video->slug) }}"
                   class="group overflow-hidden rounded-2xl bg-white shadow transition hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-900">

                    <div class="relative overflow-hidden">

                        @if($video->thumbnail)

                            <img
                                src="{{ asset('storage/' . $video->thumbnail) }}"
                                alt="{{ $video->title }}"
                                class="h-56 w-full object-cover transition duration-300 group-hover:scale-105"
                            >

                        @else

                            <div class="flex h-56 items-center justify-center bg-gray-200 text-5xl dark:bg-gray-800">
                                🎬
                            </div>

                        @endif

                        <div class="absolute bottom-3 left-3 rounded-full bg-red-600 px-3 py-1 text-xs font-bold text-white shadow-lg">
                            VIDEO
                        </div>

                    </div>

                    <div class="p-5">

                        <h2 class="line-clamp-2 text-lg font-bold text-gray-900 dark:text-white">
                            {{ $video->title }}
                        </h2>

                        <div class="mt-3 flex items-center justify-between text-sm text-gray-500">

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