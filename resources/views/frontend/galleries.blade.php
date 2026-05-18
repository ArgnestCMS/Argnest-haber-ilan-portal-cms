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

<div class="container mx-auto px-4 py-8">

    <div class="mb-8">

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            🖼️ Galeriler
        </h1>

        <p class="mt-2 text-gray-500">
            Son eklenen fotoğraf galerileri
        </p>

    </div>

    @if($galleries->count())

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

            @foreach($galleries as $gallery)

                <a href="{{ route('galleries.show', $gallery->slug) }}"
                   class="group overflow-hidden rounded-2xl bg-white shadow transition hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-900">

                    <div class="relative overflow-hidden">

                        @if($gallery->cover_image)

                            <img
                                src="{{ asset('storage/' . $gallery->cover_image) }}"
                                alt="{{ $gallery->title }}"
                                class="h-64 w-full object-cover transition duration-300 group-hover:scale-105"
                            >

                        @else

                            <div class="flex h-64 items-center justify-center bg-gray-200 text-5xl dark:bg-gray-800">
                                🖼️
                            </div>

                        @endif

                        <div class="absolute bottom-3 left-3 rounded-full bg-black/70 px-3 py-1 text-xs font-bold text-white backdrop-blur">
                            {{ $gallery->images_count }} Fotoğraf
                        </div>

                    </div>

                    <div class="p-5">

                        <h2 class="line-clamp-2 text-lg font-bold text-gray-900 dark:text-white">
                            {{ $gallery->title }}
                        </h2>

                        <div class="mt-3 flex items-center justify-between text-sm text-gray-500">

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
                🖼️
            </div>

            <h2 class="mt-4 text-2xl font-bold text-gray-900 dark:text-white">
                Henüz galeri bulunmuyor
            </h2>

        </div>

    @endif

</div>

@endsection