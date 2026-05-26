@extends('frontend.layout')

@php
    $siteSetting = \App\Models\SiteSetting::first();
@endphp

@section('title', 'Galeriler')

@section(
    'meta_description',
    'Güncel haber galerileri, fotoğraf içerikleri, kamu ilanları görselleri ve son gelişmeler.'
)

@section(
    'meta_keywords',
    'galeriler, haber galerileri, fotoğraf galerileri, kamu ilanları'
)

@section('canonical', route('galleries.index'))

@section('content')

<div class="container mx-auto px-3 py-5 md:px-4 md:py-8">

    <div class="mb-5 rounded-2xl bg-white p-5 shadow-sm md:mb-8 md:bg-transparent md:p-0 md:shadow-none">

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white md:text-3xl">
            🖼 Galeriler
        </h1>

        <p class="mt-2 text-gray-500">
            Son eklenen fotoğraf galerileri
        </p>

    </div>

    @if($galleries->count())

        <div class="grid grid-cols-2 gap-3 md:grid-cols-2 md:gap-6 xl:grid-cols-3">

            @foreach($galleries as $gallery)

                <a href="{{ route('galleries.show', $gallery->slug) }}"
                   class="group overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-900 md:shadow">

                    <div class="relative overflow-hidden">

                        @if($gallery->cover_image)

                            <img
                                src="{{ asset('storage/' . $gallery->cover_image) }}"
                                alt="{{ $gallery->title }}"
                                class="h-36 w-full object-cover transition duration-300 group-hover:scale-105 md:h-64"
                            >

                        @else

                            <div class="flex h-36 items-center justify-center bg-gray-200 text-4xl dark:bg-gray-800 md:h-64 md:text-5xl">
                                🖼
                            </div>

                        @endif

                        <div class="absolute bottom-3 left-3 rounded-full bg-black/70 px-3 py-1 text-xs font-bold text-white backdrop-blur">
                            {{ $gallery->images_count }} Fotoğraf
                        </div>

                    </div>

                    <div class="p-3 md:p-5">

                        <h2 class="line-clamp-2 text-sm font-bold text-gray-900 dark:text-white md:text-lg">
                            {{ $gallery->title }}
                        </h2>

                        <div class="mt-3 flex flex-col gap-1 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between md:text-sm">

                            <span>
                                👁 {{ number_format($gallery->views) }}
                            </span>

                            <span>
                                {{ $gallery->created_at->diffForHumans() }}
                            </span>

                        </div>

                    </div>

                </a>

            @endforeach

        </div>

        <div class="mt-10">

            {{ $galleries->links() }}

        </div>

    @else

        <div class="rounded-2xl bg-white p-10 text-center shadow dark:bg-gray-900">

            <div class="text-5xl">
                🖼
            </div>

            <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                Henüz galeri bulunmuyor
            </h2>

        </div>

    @endif

</div>

@endsection




