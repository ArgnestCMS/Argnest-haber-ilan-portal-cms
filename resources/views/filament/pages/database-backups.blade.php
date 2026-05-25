<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0">
                    <h2 class="text-lg font-bold text-gray-950 dark:text-white">Manuel veritabani yedegi</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Yedekler <code>storage/app/backups/database</code> altinda saklanir.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <x-filament::button
                        wire:click="deleteAllBackups"
                        wire:confirm="Tum yedek dosyalarini silmek istediginizden emin misiniz?"
                        icon="heroicon-o-trash"
                        color="danger"
                    >
                        Tum Yedekleri Temizle
                    </x-filament::button>

                    <x-filament::button wire:click="createBackup" icon="heroicon-o-plus" color="primary">
                        Yeni Yedek Al
                    </x-filament::button>
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h3 class="text-base font-bold text-gray-950 dark:text-white">Yedek Dosyalari</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] table-fixed divide-y divide-gray-200 text-sm dark:divide-gray-800">
                    <thead class="bg-gray-50 dark:bg-gray-950/40">
                        <tr>
                            <th class="w-[42%] px-5 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Dosya adi</th>
                            <th class="w-[18%] px-5 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Olusturulma</th>
                            <th class="w-[12%] px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Boyut</th>
                            <th class="w-[12%] px-5 py-3 text-center font-semibold text-gray-700 dark:text-gray-300">Durum</th>
                            <th class="w-[16%] px-5 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Islem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($this->backups() as $backup)
                            <tr class="align-middle">
                                <td class="px-5 py-4 font-medium text-gray-950 dark:text-white">
                                    <span class="block max-w-full truncate" title="{{ $backup['name'] }}">
                                        {{ $backup['name'] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-gray-600 dark:text-gray-400">
                                    {{ $backup['created_at']->format('d.m.Y H:i:s') }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right text-gray-600 dark:text-gray-400">
                                    {{ $this->humanSize($backup['size']) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-xs font-bold text-green-700 ring-1 ring-green-600/20 dark:bg-green-500/10 dark:text-green-300">
                                        {{ $backup['status'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex min-w-[140px] justify-end gap-2">
                                        <x-filament::button
                                            tag="a"
                                            href="{{ $this->downloadUrl($backup['name']) }}"
                                            icon="heroicon-o-arrow-down-tray"
                                            color="gray"
                                            size="sm"
                                            class="whitespace-nowrap"
                                        >
                                            Indir
                                        </x-filament::button>

                                        <x-filament::button
                                            wire:click="deleteBackup(@js($backup['name']))"
                                            wire:confirm="Bu yedek dosyasini silmek istediginizden emin misiniz?"
                                            icon="heroicon-o-trash"
                                            color="danger"
                                            size="sm"
                                            class="whitespace-nowrap"
                                        >
                                            Sil
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500 dark:text-gray-400">
                                    Henuz veritabani yedegi bulunmuyor.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
