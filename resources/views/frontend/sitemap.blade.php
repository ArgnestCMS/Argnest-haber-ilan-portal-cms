@php
    echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ url('/haberler') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ url('/ilanlar') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>hourly</changefreq>
        <priority>0.9</priority>
    </url>

    <url>
        <loc>{{ route('videos.index') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    <url>
        <loc>{{ route('galleries.index') }}</loc>
        <lastmod>{{ now()->toAtomString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    @foreach ($categories as $category)
        <url>
            <loc>{{ url('/kategori/' . $category->slug) }}</loc>
            <lastmod>{{ optional($category->updated_at)->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    @foreach ($news as $item)
        <url>
            <loc>{{ url('/haber/' . $item->slug) }}</loc>
            <lastmod>{{ optional($item->updated_at)->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    @foreach ($announcements as $item)
        <url>
            <loc>{{ url('/ilan/' . $item->slug) }}</loc>
            <lastmod>{{ optional($item->updated_at)->toAtomString() }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    @foreach ($videos as $item)
        <url>
            <loc>{{ route('videos.show', $item->slug) }}</loc>
            <lastmod>{{ optional($item->updated_at)->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    @foreach ($galleries as $item)
        <url>
            <loc>{{ route('galleries.show', $item->slug) }}</loc>
            <lastmod>{{ optional($item->updated_at)->toAtomString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

</urlset>