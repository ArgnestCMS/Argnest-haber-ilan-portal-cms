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
                            🖼 {{ $gallery->images->count() }} Fotoğraf
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

                <div class="grid gap-3 md:grid-cols-2 md:gap-5 xl:grid-cols-3" data-gallery-lightbox-grid>

                    @foreach($gallery->images as $imageIndex => $image)

                        <a
                            href="{{ asset('storage/' . $image->image) }}"
                            data-gallery-lightbox-item
                            data-gallery-lightbox-index="{{ $imageIndex }}"
                            data-gallery-lightbox-src="{{ asset('storage/' . $image->image) }}"
                            data-gallery-lightbox-title="{{ $image->title }}"
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

                <div
                    class="gallery-detail-lightbox"
                    data-gallery-lightbox
                    aria-hidden="true"
                    role="dialog"
                    aria-modal="true"
                    aria-label="Galeri gorsel goruntuleyici">

                    <button
                        type="button"
                        class="gallery-detail-lightbox__close"
                        data-gallery-lightbox-close
                        aria-label="Kapat">
                        &times;
                    </button>

                    <button
                        type="button"
                        class="gallery-detail-lightbox__nav gallery-detail-lightbox__nav--prev"
                        data-gallery-lightbox-prev
                        aria-label="Onceki gorsel">
                        &#8249;
                    </button>

                    <figure class="gallery-detail-lightbox__figure">
                        <img data-gallery-lightbox-image src="" alt="">
                        <figcaption class="gallery-detail-lightbox__caption" data-gallery-lightbox-caption></figcaption>
                    </figure>

                    <button
                        type="button"
                        class="gallery-detail-lightbox__nav gallery-detail-lightbox__nav--next"
                        data-gallery-lightbox-next
                        aria-label="Sonraki gorsel">
                        &#8250;
                    </button>

                    <div class="gallery-detail-lightbox__thumbs" data-gallery-lightbox-thumbs aria-label="Galeri kucuk gorselleri">
                        @foreach($gallery->images as $imageIndex => $image)
                            <button
                                type="button"
                                class="gallery-detail-lightbox__thumb"
                                data-gallery-lightbox-thumb="{{ $imageIndex }}"
                                aria-label="{{ $imageIndex + 1 }}. gorseli ac">
                                <img
                                    src="{{ asset('storage/' . $image->image) }}"
                                    alt="{{ $image->title }}"
                                >
                            </button>
                        @endforeach
                    </div>
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
                🖼 İlgili Galeriler
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

<style>
    .gallery-detail-lightbox {
        position: fixed;
        inset: 0;
        z-index: 100010;
        display: none;
        grid-template-rows: minmax(0, 1fr) auto;
        align-items: center;
        justify-items: center;
        gap: 16px;
        background: rgba(2, 6, 23, 0.94);
        padding: 72px 76px 22px;
    }

    .gallery-detail-lightbox.is-open {
        display: grid;
    }

    .gallery-detail-lightbox__figure {
        display: grid;
        min-width: 0;
        max-width: min(1180px, 100%);
        max-height: 100%;
        gap: 12px;
        margin: 0;
        justify-items: center;
    }

    .gallery-detail-lightbox__figure img {
        max-width: 100%;
        max-height: calc(100vh - 190px);
        border-radius: 12px;
        object-fit: contain;
        box-shadow: 0 28px 90px rgba(0, 0, 0, 0.55);
    }

    .gallery-detail-lightbox__caption {
        min-height: 24px;
        color: #f8fafc;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
    }

    .gallery-detail-lightbox__close,
    .gallery-detail-lightbox__nav {
        position: fixed;
        z-index: 100011;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.32);
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.88);
        color: #ffffff;
        box-shadow: 0 14px 38px rgba(0, 0, 0, 0.42);
        transition: background 160ms ease, color 160ms ease, transform 160ms ease;
    }

    .gallery-detail-lightbox__close:hover,
    .gallery-detail-lightbox__close:focus,
    .gallery-detail-lightbox__nav:hover,
    .gallery-detail-lightbox__nav:focus {
        background: #ffffff;
        color: #0f172a;
        outline: none;
        transform: scale(1.04);
    }

    .gallery-detail-lightbox__close {
        top: 18px;
        right: 18px;
        width: 48px;
        height: 48px;
        font-size: 34px;
        font-weight: 800;
        line-height: 1;
    }

    .gallery-detail-lightbox__nav {
        top: 50%;
        width: 54px;
        height: 54px;
        font-size: 46px;
        line-height: 1;
        transform: translateY(-50%);
    }

    .gallery-detail-lightbox__nav:hover,
    .gallery-detail-lightbox__nav:focus {
        transform: translateY(-50%) scale(1.04);
    }

    .gallery-detail-lightbox__nav--prev {
        left: 18px;
    }

    .gallery-detail-lightbox__nav--next {
        right: 18px;
    }

    .gallery-detail-lightbox__thumbs {
        display: flex;
        width: min(100%, 900px);
        gap: 10px;
        overflow-x: auto;
        padding: 4px 2px 8px;
        scrollbar-width: thin;
    }

    .gallery-detail-lightbox__thumb {
        flex: 0 0 74px;
        height: 54px;
        overflow: hidden;
        border: 2px solid transparent;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        opacity: 0.62;
        transition: border-color 160ms ease, opacity 160ms ease, transform 160ms ease;
    }

    .gallery-detail-lightbox__thumb.is-active,
    .gallery-detail-lightbox__thumb:hover,
    .gallery-detail-lightbox__thumb:focus {
        border-color: #ffffff;
        opacity: 1;
        outline: none;
        transform: translateY(-1px);
    }

    .gallery-detail-lightbox__thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (max-width: 767px) {
        .gallery-detail-lightbox {
            gap: 12px;
            padding: 66px 12px calc(18px + env(safe-area-inset-bottom, 0px));
        }

        .gallery-detail-lightbox__figure img {
            max-height: calc(100vh - 190px);
            border-radius: 10px;
        }

        .gallery-detail-lightbox__close {
            top: 12px;
            right: 12px;
            width: 48px;
            height: 48px;
        }

        .gallery-detail-lightbox__nav {
            top: auto;
            bottom: calc(86px + env(safe-area-inset-bottom, 0px));
            width: 46px;
            height: 46px;
            font-size: 38px;
            transform: none;
        }

        .gallery-detail-lightbox__nav:hover,
        .gallery-detail-lightbox__nav:focus {
            transform: scale(1.04);
        }

        .gallery-detail-lightbox__nav--prev {
            left: 14px;
        }

        .gallery-detail-lightbox__nav--next {
            right: 14px;
        }

        .gallery-detail-lightbox__caption {
            padding: 0 48px;
            font-size: 13px;
        }

        .gallery-detail-lightbox__thumb {
            flex-basis: 62px;
            height: 46px;
            border-radius: 8px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const grid = document.querySelector('[data-gallery-lightbox-grid]');
    const lightbox = document.querySelector('[data-gallery-lightbox]');

    if (!grid || !lightbox) {
        return;
    }

    const items = Array.from(grid.querySelectorAll('[data-gallery-lightbox-item]')).map((item) => ({
        link: item,
        src: item.dataset.galleryLightboxSrc || item.href,
        title: item.dataset.galleryLightboxTitle || item.querySelector('img')?.alt || '',
    }));

    if (!items.length) {
        return;
    }

    const image = lightbox.querySelector('[data-gallery-lightbox-image]');
    const caption = lightbox.querySelector('[data-gallery-lightbox-caption]');
    const closeButton = lightbox.querySelector('[data-gallery-lightbox-close]');
    const prevButton = lightbox.querySelector('[data-gallery-lightbox-prev]');
    const nextButton = lightbox.querySelector('[data-gallery-lightbox-next]');
    const thumbs = Array.from(lightbox.querySelectorAll('[data-gallery-lightbox-thumb]'));
    let currentIndex = 0;
    let touchStartX = null;

    const setActiveThumb = () => {
        thumbs.forEach((thumb, index) => {
            thumb.classList.toggle('is-active', index === currentIndex);
            thumb.setAttribute('aria-current', index === currentIndex ? 'true' : 'false');
        });

        thumbs[currentIndex]?.scrollIntoView({
            block: 'nearest',
            inline: 'center',
            behavior: 'smooth',
        });
    };

    const show = (index) => {
        currentIndex = (index + items.length) % items.length;
        const item = items[currentIndex];

        image.src = item.src;
        image.alt = item.title || '';
        caption.textContent = item.title || `${currentIndex + 1} / ${items.length}`;
        setActiveThumb();
    };

    const open = (index) => {
        show(index);
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        closeButton.focus();
    };

    const close = () => {
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        image.removeAttribute('src');
        image.removeAttribute('alt');
        caption.textContent = '';
        document.body.style.overflow = '';
        items[currentIndex]?.link.focus();
    };

    const isOpen = () => lightbox.classList.contains('is-open');
    const prev = () => show(currentIndex - 1);
    const next = () => show(currentIndex + 1);

    grid.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-gallery-lightbox-item]');

        if (!trigger) {
            return;
        }

        event.preventDefault();
        open(Number(trigger.dataset.galleryLightboxIndex || 0));
    });

    closeButton.addEventListener('click', close);
    prevButton.addEventListener('click', prev);
    nextButton.addEventListener('click', next);

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            show(Number(thumb.dataset.galleryLightboxThumb || 0));
        });
    });

    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            close();
        }
    });

    lightbox.addEventListener('touchstart', (event) => {
        touchStartX = event.changedTouches[0]?.clientX ?? null;
    }, { passive: true });

    lightbox.addEventListener('touchend', (event) => {
        if (touchStartX === null) {
            return;
        }

        const touchEndX = event.changedTouches[0]?.clientX ?? touchStartX;
        const distance = touchEndX - touchStartX;
        touchStartX = null;

        if (Math.abs(distance) < 48) {
            return;
        }

        if (distance > 0) {
            prev();
        } else {
            next();
        }
    }, { passive: true });

    document.addEventListener('keydown', (event) => {
        if (!isOpen()) {
            return;
        }

        if (event.key === 'Escape') {
            close();
        }

        if (event.key === 'ArrowLeft') {
            prev();
        }

        if (event.key === 'ArrowRight') {
            next();
        }
    });
});
</script>

@endsection




