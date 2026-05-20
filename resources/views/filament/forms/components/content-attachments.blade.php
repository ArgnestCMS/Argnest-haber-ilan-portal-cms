@php
    $attachments = $getRecord()?->contentAttachments ?? collect();
@endphp

@if ($attachments->isNotEmpty())
    <div
        class="space-y-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900"
        x-data="{
            copy(value) {
                navigator.clipboard?.writeText(value);
            },
            insert(value) {
                const editor = document.querySelector('.fi-fo-rich-editor [contenteditable=true], [contenteditable=true]');

                if (! editor) {
                    this.copy(value);
                    return;
                }

                editor.focus();
                document.execCommand('insertHTML', false, value);
            }
        }"
    >
        <div>
            <div class="text-sm font-semibold text-gray-950 dark:text-white">Ek Dosyalar / Dokümanlar</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">Linki kopyalayabilir veya aktif editör alanına ekleyebilirsiniz.</div>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($attachments as $asset)
                @php
                    $url = $asset->url;
                    $isImage = str_starts_with($asset->mime_type, 'image/');
                    $insertMarkup = $isImage
                        ? '<p><img src="' . e($url) . '" alt="' . e($asset->original_name) . '"></p>'
                        : '<p><a href="' . e($url) . '" target="_blank" rel="noopener">' . e($asset->original_name) . '</a></p>';
                @endphp

                <div class="flex flex-col gap-3 py-3 md:flex-row md:items-center md:justify-between">
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $asset->original_name }}</div>
                        <div class="mt-1 flex flex-wrap gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $asset->human_size }}</span>
                            <span>{{ $asset->created_at?->format('d.m.Y H:i') }}</span>
                            <a href="{{ $url }}" target="_blank" class="font-semibold text-primary-600 hover:underline">Dosyayı aç</a>
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2">
                        <button
                            type="button"
                            class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800"
                            x-on:click="copy(@js($url))"
                        >
                            Kopyala
                        </button>

                        <button
                            type="button"
                            class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-primary-700"
                            x-on:click="insert(@js($insertMarkup))"
                        >
                            İçeriğe ekle
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
