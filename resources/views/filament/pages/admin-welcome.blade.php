<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="max-w-3xl">
                <p class="text-sm font-bold uppercase tracking-wide text-primary-600 dark:text-primary-400">Argnest Haber-Ilan Portal CMS</p>
                <h2 class="mt-2 text-2xl font-black text-gray-950 dark:text-white">Baslangic Merkezi</h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Kurulumdan sonra en cok kullanacaginiz yonetim alanlari ve ilk kontrol listesi burada.
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
            @foreach($this->cards() as $card)
                <a href="{{ $card['url'] }}" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-primary-300 hover:shadow-md dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-base font-black text-gray-950 dark:text-white">{{ $card['title'] }}</div>
                    <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ $card['description'] }}</p>
                </a>
            @endforeach
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-lg font-black text-gray-950 dark:text-white">First Setup Checklist</h3>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                @foreach($this->checklist() as $item)
                    <div class="flex items-center gap-3 rounded-lg border border-gray-100 px-4 py-3 dark:border-gray-800">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-sm font-black {{ $item['ok'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $item['ok'] ? 'OK' : '!' }}
                        </span>
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
