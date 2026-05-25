<x-filament-panels::page>
    <div class="space-y-5">
        @php
            $sections = [
                'Haber ekleme' => 'Admin > Haberler alanindan yeni haber olusturun. Baslik, slug, kategori, gorsel, icerik ve yayin durumunu kontrol edin.',
                'Ilan ekleme' => 'Admin > Ilanlar alanindan ilan kaydi ekleyin. Kategori, sehir, kurum ve yayin durumunu eksiksiz girin.',
                'SEO ayarlari' => 'SEO Ayarlari ekraninda varsayilan meta bilgiler, robots ve sitemap davranisini yonetin.',
                'Tema sistemi' => 'Tema Yonetimi ekranindan portal renklerini marka kimliginize gore guncelleyin.',
                'Backup sistemi' => 'Veritabani Yedekleri ekranindan manuel yedek alin, indirin veya eski yedekleri temizleyin.',
                'Rapor sistemi' => 'Site Raporlari ekraninda haber, ilan ve genel performans ozetlerini inceleyin.',
                'Kullanici yonetimi' => 'Kullanicilar ve Roller alanindan hesap durumlarini, rolleri ve yetkileri kontrol edin.',
            ];
        @endphp

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm font-bold uppercase tracking-wide text-primary-600 dark:text-primary-400">Argnest Portal</p>
            <h2 class="mt-2 text-2xl font-black text-gray-950 dark:text-white">Kullanim Kilavuzu</h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Temel admin islemleri icin hizli referans.</p>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            @foreach($sections as $title => $body)
                <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <h3 class="text-base font-black text-gray-950 dark:text-white">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ $body }}</p>
                </section>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
