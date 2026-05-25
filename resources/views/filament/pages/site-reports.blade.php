<x-filament-panels::page>
    @php($report = $this->report())

    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filtre</span>
                        <select wire:model.live="period" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                            <option value="today">Bugun</option>
                            <option value="week">Bu hafta</option>
                            <option value="month">Bu ay</option>
                            <option value="custom">Ozel tarih</option>
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Baslangic</span>
                        <input wire:model.live="startDate" type="date" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Bitis</span>
                        <input wire:model.live="endDate" type="date" class="mt-1 block w-full rounded-lg border-gray-300 text-sm shadow-sm dark:border-gray-700 dark:bg-gray-950 dark:text-white">
                    </label>

                    <div class="rounded-lg bg-gray-50 p-3 dark:bg-gray-950/50">
                        <div class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Rapor araligi</div>
                        <div class="mt-1 text-sm font-semibold text-gray-950 dark:text-white">
                            {{ $report['period']['label'] }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row lg:justify-end">
                    <x-filament::button tag="a" href="{{ $this->exportUrl('csv') }}" icon="heroicon-o-arrow-down-tray" color="gray">
                        CSV indir
                    </x-filament::button>

                    <x-filament::button tag="a" href="{{ $this->exportUrl('xlsx') }}" icon="heroicon-o-document-arrow-down" color="primary">
                        XLSX indir
                    </x-filament::button>
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($report['metrics'] as $metric)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $metric['label'] }}</div>
                    <div class="mt-2 text-2xl font-bold text-gray-950 dark:text-white">{{ number_format($metric['value'], 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h3 class="text-base font-bold text-gray-950 dark:text-white">En cok okunan haberler</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] table-fixed text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-950/40">
                            <tr>
                                <th class="w-[65%] px-5 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Baslik</th>
                                <th class="w-[20%] px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Okunma</th>
                                <th class="w-[15%] px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($report['top_news'] as $row)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-gray-950 dark:text-white"><span class="block truncate" title="{{ $row['title'] }}">{{ $row['title'] }}</span></td>
                                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($row['views'], 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ $row['created_at'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">Kayit yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                    <h3 class="text-base font-bold text-gray-950 dark:text-white">En cok goruntulenen ilanlar</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[520px] table-fixed text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-950/40">
                            <tr>
                                <th class="w-[65%] px-5 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Baslik</th>
                                <th class="w-[20%] px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Goruntulenme</th>
                                <th class="w-[15%] px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Tarih</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($report['top_announcements'] as $row)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-gray-950 dark:text-white"><span class="block truncate" title="{{ $row['title'] }}">{{ $row['title'] }}</span></td>
                                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($row['views'], 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-right text-gray-600 dark:text-gray-400">{{ $row['created_at'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500 dark:text-gray-400">Kayit yok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
