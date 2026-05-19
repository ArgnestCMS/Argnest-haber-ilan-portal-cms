@php
    $siteSetting = \App\Models\SiteSetting::first();
@endphp
@extends('frontend.layout')

@section('title', $gallery->title)

@section(
    'meta_description',
    \Illuminate\Support\Str::limit(
        strip_tags($gallery->description ?? $gallery->title),
        160
    )
)

@section(
    'meta_keywords',
    $gallery->title . ', galeri, haber galerisi, kamu haberleri'
)

@section(
    'meta_image',
    $gallery->cover_image
        ? asset('storage/' . $gallery->cover_image)
        : asset('default-og.jpg')
)

@section('canonical', route('galleries.show', $gallery->slug))

@section('og_type', 'article')
@section('schema')

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ImageGallery',

    'name' => $gallery->title,

    'description' => \Illuminate\Support\Str::limit(
        strip_tags($gallery->description ?? $gallery->title),
        160
    ),

    'url' => route('galleries.show', $gallery->slug),

    'image' => $gallery->cover_image
        ? asset('storage/' . $gallery->cover_image)
        : asset('default-og.jpg'),

    'datePublished' => $gallery->created_at?->toAtomString(),

    'dateModified' => $gallery->updated_at?->toAtomString(),

    'publisher' => [
        '@type' => 'Organization',
        'name' => $siteSetting?->site_name ?? config('app.name'),
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $siteSetting?->logo
                ? asset('storage/' . $siteSetting->logo)
                : asset('favicon.png'),
        ],
    ],

], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',

    'itemListElement' => [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Anasayfa',
            'item' => url('/'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 2,
            'name' => 'Galeriler',
            'item' => route('galleries.index'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $gallery->title,
            'item' => route('galleries.show', $gallery->slug),
        ],
    ],

], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

@endsection
@section('content')

<div class="mx-auto max-w-7xl px-3 py-5 md:px-4 md:py-8">

    {{-- GALERİ CARD --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-lg dark:bg-gray-900 md:rounded-3xl md:shadow-2xl">

        <div class="p-4 md:p-8">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

                <div>

                    <h1 class="text-3xl font-black text-gray-900 dark:text-white md:text-4xl">
                        {{ $gallery->title }}
                    </h1>

                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500 md:gap-5 md:text-sm">

                        <span>
                            🖼️ {{ $gallery->images->count() }} Fotoğraf
                        </span>

                        <span>
                            👁 {{ number_format($gallery->views) }}
                        </span>

                        <span>
                            📅 {{ $gallery->created_at->format('d.m.Y H:i') }}
                        </span>

                    </div>

                </div>

            </div>

            @if($gallery->description)

                <div class="prose mb-6 max-w-none text-[17px] leading-8 dark:prose-invert md:mb-8">

                    {!! $gallery->description !!}

                </div>

            @endif

            {{-- FOTOĞRAF GRID --}}
            @if($gallery->images->count())

                <div class="grid gap-3 md:grid-cols-2 md:gap-5 xl:grid-cols-3">

                    @foreach($gallery->images as $image)

                        <a
                            href="{{ asset('storage/' . $image->image) }}"
                            target="_blank"
                            class="group overflow-hidden rounded-2xl bg-gray-100 shadow-sm transition hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-800 md:shadow-xl">

                            <div class="overflow-hidden">

                                <img
                                    src="{{ asset('storage/' . $image->image) }}"
                                    alt="{{ $image->title }}"
                                    class="h-56 w-full object-cover transition duration-500 group-hover:scale-110 md:h-72"
                                >

                            </div>

                            @if($image->title)

                                <div class="p-4">

                                    <div class="font-bold text-gray-900 dark:text-white">
                                        {{ $image->title }}
                                    </div>

                                </div>

                            @endif

                        </a>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

    {{-- YORUMLAR --}}
    <div class="mt-5 rounded-2xl bg-white p-4 shadow-lg dark:bg-gray-900 md:mt-8 md:p-6 md:shadow-2xl">

        <h2 class="mb-6 text-2xl font-black text-gray-900 dark:text-white">
            💬 Yorumlar
        </h2>

        @auth

            <form action="{{ route('comments.gallery.store', $gallery) }}"
                  method="POST"
                  class="mb-8">

                @csrf

                <textarea
                    name="content"
                    rows="4"
                    required
                    placeholder="Yorumunuzu yazın..."
                    class="w-full rounded-2xl border border-gray-300 p-4 text-base dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>

                <button
                    type="submit"
                    class="mt-4 rounded-xl bg-blue-600 px-6 py-3 font-bold text-white transition hover:bg-blue-700">

                    Yorumu Gönder

                </button>

            </form>

        @else

            <div class="mb-8 rounded-xl bg-yellow-100 p-4 text-yellow-800">
                Yorum yapabilmek için giriş yapmalısınız.
            </div>

        @endauth

        <div class="space-y-5">

            @forelse($gallery->approvedComments as $comment)

                <div class="rounded-2xl border border-gray-200 p-5 dark:border-gray-700">

                    <div class="flex items-center justify-between">

                        <div class="font-bold text-gray-900 dark:text-white">
                            {{ $comment->user->name }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $comment->created_at->diffForHumans() }}
                        </div>

                    </div>

                    <div class="mt-3 text-gray-700 dark:text-gray-300">
                        {{ $comment->content }}
                    </div>

                </div>

            @empty

                <div class="rounded-2xl border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-gray-700">
                    Henüz yorum yapılmamış.
                </div>

            @endforelse

        </div>

    </div>

    {{-- İLGİLİ GALERİLER --}}
    @if($relatedGalleries->count())

        <div class="mt-10">

            <h2 class="mb-6 text-3xl font-black text-gray-900 dark:text-white">
                🖼️ İlgili Galeriler
            </h2>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-2 md:gap-6 xl:grid-cols-3">

                @foreach($relatedGalleries as $related)

                    <a href="{{ route('galleries.show', $related->slug) }}"
                       class="group overflow-hidden rounded-2xl bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-900 md:shadow-xl">

                        @if($related->cover_image)

                            <img
                                src="{{ asset('storage/' . $related->cover_image) }}"
                                alt="{{ $related->title }}"
                                class="h-32 w-full object-cover transition duration-300 group-hover:scale-105 md:h-56"
                            >

                        @endif

                        <div class="p-5">

                            <h3 class="line-clamp-2 text-sm font-bold text-gray-900 transition group-hover:text-blue-600 dark:text-white md:text-lg">
                                {{ $related->title }}
                            </h3>

                        </div>

                    </a>

                @endforeach

            </div>

        </div>

    @endif

</div>

@endsection
