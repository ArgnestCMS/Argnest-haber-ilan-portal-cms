@extends('frontend.layout')

@section('title', 'Arama Sonuçları')

@section('content')

<section class="max-w-7xl mx-auto px-4 mt-6">

    {{-- BAŞLIK --}}
    <div class="bg-white border border-slate-200 shadow-sm p-7 mb-6">

        <div class="flex items-center gap-3 mb-3">

            <span class="bg-blue-700 text-white text-xs font-bold px-3 py-1 rounded">
                ARAMA
            </span>

            <span class="text-sm text-slate-500">
                Site içi arama sonuçları
            </span>

        </div>

        <h1 class="text-4xl font-black text-slate-950">
            "{{ $query }}" için sonuçlar
        </h1>

        <p class="text-slate-500 mt-2">
            Toplam
            {{ $news->total() + $announcements->total() }}
            sonuç bulundu
        </p>

    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- HABERLER --}}
        <div class="bg-white border border-slate-200 shadow-sm">

            <div class="border-b px-5 py-4">
                <h2 class="text-2xl font-black">
                    Haberler
                </h2>
            </div>

            <div class="divide-y">

                @forelse($news as $item)

                    <a href="/haber/{{ $item->slug }}"
                       class="block p-5 hover:bg-slate-50 transition">

                        <h3 class="text-xl font-black text-slate-900 hover:text-blue-700">
                            {{ $item->title }}
                        </h3>

                        @if($item->summary)
                            <p class="text-slate-600 mt-2 text-sm leading-6">
                                {{ Str::limit($item->summary, 120) }}
                            </p>
                        @endif

                        <div class="text-xs text-slate-500 mt-3">
                            📅 {{ $item->created_at->format('d.m.Y') }}
                        </div>

                    </a>

                @empty

                    <div class="p-6 text-slate-500">
                        Haber sonucu bulunamadı.
                    </div>

                @endforelse

            </div>

        </div>

        {{-- İLANLAR --}}
        <div class="bg-white border border-slate-200 shadow-sm">

            <div class="border-b px-5 py-4">
                <h2 class="text-2xl font-black">
                    İlanlar
                </h2>
            </div>

            <div class="divide-y">

                @forelse($announcements as $item)

                    <a href="/ilan/{{ $item->slug }}"
                       class="block p-5 hover:bg-slate-50 transition">

                        <h3 class="text-xl font-black text-slate-900 hover:text-blue-700">
                            {{ $item->title }}
                        </h3>

                        @if($item->summary)
                            <p class="text-slate-600 mt-2 text-sm leading-6">
                                {{ Str::limit($item->summary, 120) }}
                            </p>
                        @endif

                        <div class="text-xs text-slate-500 mt-3 flex gap-3">
                            <span>📍 {{ $item->city }}</span>
                            <span>🏢 {{ $item->institution }}</span>
                        </div>

                    </a>

                @empty

                    <div class="p-6 text-slate-500">
                        İlan sonucu bulunamadı.
                    </div>

                @endforelse

            </div>

        </div>

    </div>

</section>

@endsection