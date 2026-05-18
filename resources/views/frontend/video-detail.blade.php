@php
    $siteSetting = \App\Models\SiteSetting::first();
@endphp
@extends('frontend.layout')

@section('title', $video->title)

@section(
    'meta_description',
    \Illuminate\Support\Str::limit(
        strip_tags($video->description ?? $video->title),
        160
    )
)

@section(
    'meta_keywords',
    $video->title . ', video, haber videosu, kamu haberleri'
)

@section(
    'meta_image',
    $video->thumbnail
        ? asset('storage/' . $video->thumbnail)
        : asset('default-og.jpg')
)
@section('schema')

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'VideoObject',

    'name' => $video->title,

    'description' => \Illuminate\Support\Str::limit(
        strip_tags($video->description ?? $video->title),
        160
    ),

    'thumbnailUrl' => [
        $video->thumbnail
            ? asset('storage/' . $video->thumbnail)
            : asset('default-og.jpg')
    ],

    'uploadDate' => $video->created_at?->toAtomString(),

    'datePublished' => $video->created_at?->toAtomString(),

    'dateModified' => $video->updated_at?->toAtomString(),

    'contentUrl' => $video->video_path
        ? asset('storage/' . $video->video_path)
        : null,

    'embedUrl' => $video->youtube_url ?? null,

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

    'mainEntityOfPage' => [
        '@type' => 'WebPage',
        '@id' => route('videos.show', $video->slug),
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
            'name' => 'Videolar',
            'item' => route('videos.index'),
        ],
        [
            '@type' => 'ListItem',
            'position' => 3,
            'name' => $video->title,
            'item' => route('videos.show', $video->slug),
        ],
    ],

], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>

@endsection
@section('canonical', route('videos.show', $video->slug))

@section('og_type', 'video.other')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-8">

    <div class="grid gap-8 lg:grid-cols-3">

        {{-- SOL ALAN --}}
        <div class="lg:col-span-2">

            {{-- VIDEO CARD --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-xl dark:bg-gray-900">

                <div class="aspect-video bg-black">

                    @if($video->video_type === 'youtube' && $video->youtube_url)

                        @php
                            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/', $video->youtube_url, $matches);
                            $youtubeId = $matches[1] ?? null;
                        @endphp

                        @if($youtubeId)

                            <iframe
                                class="h-full w-full"
                                src="https://www.youtube.com/embed/{{ $youtubeId }}"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>

                        @endif

                    @elseif($video->video_path)

                        <video
                            controls
                            class="h-full w-full">

                            <source
                                src="{{ asset('storage/' . $video->video_path) }}"
                                type="video/mp4">

                        </video>

                    @endif

                </div>

                <div class="p-6">

                    <h1 class="text-3xl font-black text-gray-900 dark:text-white">
                        {{ $video->title }}
                    </h1>

                    <div class="mt-4 flex flex-wrap items-center gap-5 text-sm text-gray-500">

                        <span>
                            👁 {{ number_format($video->views) }} görüntülenme
                        </span>

                        <span>
                            📅 {{ $video->created_at->format('d.m.Y H:i') }}
                        </span>

                    </div>

                    @if($video->description)

                        <div class="prose mt-6 max-w-none dark:prose-invert">

                            {!! $video->description !!}

                        </div>

                    @endif

                </div>

            </div>

            {{-- YORUMLAR --}}
            <div class="mt-8 rounded-2xl bg-white p-6 shadow-xl dark:bg-gray-900">

                <h2 class="mb-6 text-2xl font-black text-gray-900 dark:text-white">
                    💬 Yorumlar
                </h2>

                @auth

                    <form action="{{ route('comments.video.store', $video) }}"
                          method="POST"
                          class="mb-8">

                        @csrf

                        <textarea
                            name="content"
                            rows="4"
                            required
                            placeholder="Yorumunuzu yazın..."
                            class="w-full rounded-2xl border border-gray-300 p-4 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>

                        <button
                            type="submit"
                            class="mt-4 rounded-xl bg-red-600 px-6 py-3 font-bold text-white transition hover:bg-red-700">

                            Yorumu Gönder

                        </button>

                    </form>

                @else

                    <div class="mb-8 rounded-xl bg-yellow-100 p-4 text-yellow-800">
                        Yorum yapabilmek için giriş yapmalısınız.
                    </div>

                @endauth

                <div class="space-y-5">

                    @forelse($video->approvedComments as $comment)

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

            {{-- İLGİLİ VIDEOLAR --}}
            @if($relatedVideos->count())

                <div class="mt-10">

                    <h2 class="mb-6 text-2xl font-black text-gray-900 dark:text-white">
                        🎬 İlgili Videolar
                    </h2>

                    <div class="grid gap-6 md:grid-cols-2">

                        @foreach($relatedVideos as $related)

                            <a href="{{ route('videos.show', $related->slug) }}"
                               class="overflow-hidden rounded-2xl bg-white shadow-lg transition hover:-translate-y-1 hover:shadow-2xl dark:bg-gray-900">

                                @if($related->thumbnail)

                                    <img
                                        src="{{ asset('storage/' . $related->thumbnail) }}"
                                        class="h-44 w-full object-cover"
                                        alt="{{ $related->title }}">

                                @endif

                                <div class="p-4">

                                    <h3 class="line-clamp-2 font-bold text-gray-900 dark:text-white">
                                        {{ $related->title }}
                                    </h3>

                                </div>

                            </a>

                        @endforeach

                    </div>

                </div>

            @endif

        </div>

        {{-- SAĞ SIDEBAR --}}
        <div>

            <div class="sticky top-24 rounded-2xl bg-white p-5 shadow-xl dark:bg-gray-900">

                <h3 class="mb-5 text-xl font-black text-gray-900 dark:text-white">
                    📺 Son Videolar
                </h3>

                <div class="space-y-5">

                    @foreach($sidebarVideos as $item)

                        <a href="{{ route('videos.show', $item->slug) }}"
                           class="flex gap-3 group">

                            @if($item->thumbnail)

                                <img
                                    src="{{ asset('storage/' . $item->thumbnail) }}"
                                    class="h-20 w-28 rounded-xl object-cover"
                                    alt="{{ $item->title }}">

                            @endif

                            <div>

                                <div class="line-clamp-2 text-sm font-bold text-gray-900 transition group-hover:text-red-600 dark:text-white">
                                    {{ $item->title }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $item->created_at->diffForHumans() }}
                                </div>

                            </div>

                        </a>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

</div>

@endsection