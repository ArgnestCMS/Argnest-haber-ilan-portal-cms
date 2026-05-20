@php
    $attachments = $getRecord()?->contentAttachments ?? collect();
@endphp

<style>
    .content-files-table {
        overflow: hidden;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #111827;
    }

    .content-files-table__title {
        border-bottom: 1px solid #d1d5db;
        background: #f3f4f6;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
    }

    .content-files-table__scroll {
        overflow-x: auto;
    }

    .content-files-table table {
        width: 100%;
        min-width: 820px;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 13px;
    }

    .content-files-table th {
        background: #1f2937;
        color: #f9fafb;
        padding: 10px 12px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
    }

    .content-files-table td {
        border-bottom: 1px solid #e5e7eb;
        padding: 9px 12px;
        vertical-align: middle;
    }

    .content-files-table__file-cell,
    .content-files-table__url-wrap {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 8px;
    }

    .content-files-table__file-link {
        display: block;
        min-width: 0;
        overflow: hidden;
        color: #2563eb;
        font-weight: 700;
        text-decoration: none;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .content-files-table__file-link:hover {
        text-decoration: underline;
    }

    .content-files-table__url-row td {
        background: #f9fafb;
        padding-top: 8px;
        padding-bottom: 12px;
    }

    .content-files-table__url-input {
        width: 100%;
        min-width: 0;
        height: 34px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        background: #ffffff;
        color: #111827;
        padding: 0 10px;
        font-size: 12px;
        line-height: 34px;
    }

    .content-files-table__btn {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        min-height: 32px;
        border: 1px solid transparent;
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .content-files-table__btn--delete {
        width: 30px;
        min-height: 30px;
        padding: 0;
        border-color: #fecaca;
        background: #fef2f2;
        color: #dc2626;
        font-size: 18px;
    }

    .content-files-table__btn--gray {
        border-color: #cbd5e1;
        background: #f8fafc;
        color: #334155;
    }

    .content-files-table__btn--copy {
        border-color: #94a3b8;
        background: #f1f5f9;
        color: #1e40af;
    }

    .content-files-table__btn--insert {
        border-color: #86efac;
        background: #dcfce7;
        color: #166534;
    }

    .content-files-table__actions {
        text-align: right;
    }

    .content-files-table__nowrap {
        white-space: nowrap;
    }

    .content-files-table[data-content-delete-hidden='true'],
    .content-files-table [data-content-delete-hidden='true'] {
        display: none !important;
    }

    .content-files-toast-stack {
        position: fixed;
        right: 18px;
        bottom: 18px;
        z-index: 9999;
        display: grid;
        width: min(320px, calc(100vw - 32px));
        gap: 8px;
    }

    .content-files-toast {
        border: 1px solid #334155;
        border-radius: 8px;
        background: #020617;
        color: #f8fafc;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
    }

    .dark .content-files-table {
        border-color: #374151;
        background: #111827;
        color: #f3f4f6;
    }

    .dark .content-files-table__title,
    .dark .content-files-table td {
        border-color: #374151;
        background: #111827;
        color: #f3f4f6;
    }

    .dark .content-files-table th {
        background: #1e3a8a;
        color: #ffffff;
    }

    .dark .content-files-table__url-row td {
        background: #0f172a;
    }

    .dark .content-files-table__url-input {
        border-color: #4b5563;
        background: #111827;
        color: #f9fafb;
    }

    .dark .content-files-table__file-link {
        color: #93c5fd;
    }

    .dark .content-files-table__btn--gray {
        border-color: #475569;
        background: #1e293b;
        color: #e2e8f0;
    }

    .dark .content-files-table__btn--copy {
        border-color: #475569;
        background: #0f172a;
        color: #dbeafe;
    }

    .dark .content-files-table__btn--delete {
        border-color: #991b1b;
        background: #7f1d1d;
        color: #fecaca;
    }

    .dark .content-files-table__btn--insert {
        border-color: #166534;
        background: #14532d;
        color: #dcfce7;
    }
</style>

<script>
    window.contentAttachmentTable = {
        pendingDeletedAssetIds: window.contentAttachmentTable?.pendingDeletedAssetIds ?? new Set(),
        livewire(trigger) {
            const root = trigger?.closest?.('[wire\\:id]');
            const id = root?.getAttribute('wire:id');

            return id && window.Livewire ? window.Livewire.find(id) : null;
        },
        toast(message) {
            let stack = document.querySelector('.content-files-toast-stack');

            if (! stack) {
                stack = document.createElement('div');
                stack.className = 'content-files-toast-stack';
                document.body.appendChild(stack);
            }

            const toast = document.createElement('div');
            toast.className = 'content-files-toast';
            toast.textContent = message;
            stack.appendChild(toast);
            setTimeout(() => toast.remove(), 2600);
        },
        escapeHtml(value) {
            const element = document.createElement('div');
            element.textContent = value ?? '';

            return element.innerHTML;
        },
        buildHtml(button) {
            const safeUrl = this.escapeHtml(button.dataset.url);
            const safeName = this.escapeHtml(button.dataset.name);

            return button.dataset.isImage === '1'
                ? '<img src="' + safeUrl + '" />'
                : '<a href="' + safeUrl + '" target="_blank" rel="noopener">' + safeName + '</a>';
        },
        async copy(button) {
            const html = this.buildHtml(button);

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(html);
                } else {
                    const input = document.createElement('textarea');
                    input.value = html;
                    input.style.position = 'fixed';
                    input.style.opacity = '0';
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                }

                this.toast('Kopyalandı');
            } catch (error) {
                this.toast('Kopyalama başarısız');
            }
        },
        insert(button) {
            const html = this.buildHtml(button);
            const component = this.livewire(button);
            const current = String(component?.get?.('data.content') ?? '');
            const next = current.replace(/\s*$/, '') + (current.trim().length ? '\n' : '') + html + '\n';

            component?.set?.('data.content', next, false);

            document
                .querySelectorAll('textarea[name$="[content]"], input[type="hidden"][name$="[content]"]')
                .forEach((field) => {
                    field.value = next;
                    field.dispatchEvent(new InputEvent('input', { bubbles: true, inputType: 'insertText', data: html }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                });

            this.toast('İçeriğe eklendi');
        },
        escapeRegExp(value) {
            return String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        },
        removeContentReferences(button) {
            const component = this.livewire(button);
            const current = String(component?.get?.('data.content') ?? '');
            const urls = [
                button.dataset.path,
                button.dataset.url,
                button.dataset.publicUrl,
                button.dataset.url ? window.location.origin + button.dataset.url : null,
            ];
            let next = current;

            urls.filter(Boolean).forEach((url) => {
                const quotedUrl = this.escapeRegExp(url);
                next = next
                    .replace(new RegExp('<p>\\s*<img\\b[^>]*\\bsrc=["\\\']' + quotedUrl + '["\\\'][^>]*>\\s*</p>', 'giu'), '')
                    .replace(new RegExp('<img\\b[^>]*\\bsrc=["\\\']' + quotedUrl + '["\\\'][^>]*>', 'giu'), '')
                    .replace(new RegExp('<p>\\s*<a\\b[^>]*\\bhref=["\\\']' + quotedUrl + '["\\\'][^>]*>.*?</a>\\s*</p>', 'giu'), '')
                    .replace(new RegExp('<a\\b[^>]*\\bhref=["\\\']' + quotedUrl + '["\\\'][^>]*>.*?</a>', 'giu'), '');
            });

            next = next.trim();

            if (next === current) return;

            component?.set?.('data.content', next, false);

            document
                .querySelectorAll('textarea[name$="[content]"], input[type="hidden"][name$="[content]"]')
                .forEach((field) => {
                    field.value = next;
                    field.dispatchEvent(new InputEvent('input', { bubbles: true, inputType: 'deleteContent', data: null }));
                    field.dispatchEvent(new Event('change', { bubbles: true }));
                });
        },
        markDeleted(button) {
            const component = this.livewire(button);

            if (! component) return false;

            const current = component.get?.('data.deleted_content_attachment_ids') ?? [];
            const ids = Array.isArray(current) ? current : Object.values(current || {});
            const assetId = String(button.dataset.assetId);

            if (! ids.map(String).includes(assetId)) {
                ids.push(assetId);
            }

            component.set?.('data.deleted_content_attachment_ids', ids, false);

            return true;
        },
        hideRows(button) {
            const assetId = button.dataset.assetId;
            const table = button.closest('.content-files-table');
            this.pendingDeletedAssetIds.add(String(assetId));
            table?.querySelectorAll('[data-content-asset="' + assetId + '"]').forEach((row) => {
                row.dataset.contentDeleteHidden = 'true';
                row.setAttribute('aria-hidden', 'true');
            });

            const count = table?.querySelectorAll('tbody tr:not(.content-files-table__url-row):not([data-content-delete-hidden="true"])').length ?? 0;
            const title = table?.querySelector('[data-document-count]');

            if (title) title.textContent = count + ' adet dosya bulundu.';
            if (count === 0 && table) table.dataset.contentDeleteHidden = 'true';
        },
        _applyPendingDeletesPrevious(root = document) {
            this.pendingDeletedAssetIds.forEach((assetId) => {
                if (root.matches?.('[data-content-asset="' + CSS.escape(String(assetId)) + '"]')) {
                    root.dataset.contentDeleteHidden = 'true';
                    root.setAttribute('aria-hidden', 'true');
                }

                root
                    .querySelectorAll?.('[data-content-asset="' + CSS.escape(String(assetId)) + '"]')
                    ?.forEach((row) => {
                        row.dataset.contentDeleteHidden = 'true';
                        row.setAttribute('aria-hidden', 'true');
                    });
            });

            root
                .querySelectorAll?.('.content-files-table')
                ?.forEach((table) => {
                    const count = table.querySelectorAll('tbody tr:not(.content-files-table__url-row):not([data-content-delete-hidden="true"])').length;
                    const title = table.querySelector('[data-document-count]');

                    if (title) title.textContent = count + ' adet dokÃ¼man bulundu.';
                    if (count === 0) table.dataset.contentDeleteHidden = 'true';
                });
        },
        applyPendingDeletes(root = document) {
            this.pendingDeletedAssetIds.forEach((assetId) => {
                if (root.matches?.('[data-content-asset="' + CSS.escape(String(assetId)) + '"]')) {
                    root.dataset.contentDeleteHidden = 'true';
                    root.setAttribute('aria-hidden', 'true');
                }

                root
                    .querySelectorAll?.('[data-content-asset="' + CSS.escape(String(assetId)) + '"]')
                    ?.forEach((row) => {
                        row.dataset.contentDeleteHidden = 'true';
                        row.setAttribute('aria-hidden', 'true');
                    });
            });

            root
                .querySelectorAll?.('.content-files-table')
                ?.forEach((table) => {
                    const count = table.querySelectorAll('tbody tr:not(.content-files-table__url-row):not([data-content-delete-hidden="true"])').length;
                    const title = table.querySelector('[data-document-count]');

                    if (title) title.textContent = count + ' adet dosya bulundu.';
                    if (count === 0) table.dataset.contentDeleteHidden = 'true';
                });
        },
        delete(button) {
            if (! confirm('Bu dosya listeden kaldırılsın mı?')) return;

            const marked = this.markDeleted(button);
            this.hideRows(button);
            this.removeContentReferences(button);
            this.toast(marked ? 'Dosya silinmek üzere işaretlendi. Değişiklikleri kaydedince silinecek.' : 'Silme başarısız');
        },
        bind() {
            if (window.__contentAttachmentTableBound) return;
            window.__contentAttachmentTableBound = true;

            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-content-file-action]');

                if (! button) return;

                event.preventDefault();

                if (button.dataset.contentFileAction === 'copy') window.contentAttachmentTable.copy(button);
                if (button.dataset.contentFileAction === 'insert') window.contentAttachmentTable.insert(button);
                if (button.dataset.contentFileAction === 'delete') window.contentAttachmentTable.delete(button);
            });

            document.addEventListener('livewire:navigated', () => window.contentAttachmentTable.applyPendingDeletes());

            if (window.Livewire?.hook) {
                window.Livewire.hook('morph.updated', ({ el }) => {
                    window.contentAttachmentTable.applyPendingDeletes(el);
                });
            }
        },
    };

    window.contentAttachmentTable.bind();
</script>

@if ($attachments->isNotEmpty())
    <div class="content-files-table bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-100">
        <div class="content-files-table__title" data-document-count>
            {{ $attachments->count() }} adet doküman bulundu.
        </div>

        <div class="content-files-table__scroll">
            <table>
                <thead>
                    <tr>
                        <th style="width: 52%;">Dosya</th>
                        <th style="width: 20%;">Değiştirilme tarihi</th>
                        <th style="width: 12%;">Boyutu</th>
                        <th style="width: 16%; text-align: right;">İşlemler</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($attachments as $asset)
                        @php
                            $url = $asset->url;
                            $relativeUrl = $asset->path ? '/storage/' . ltrim($asset->path, '/') : (parse_url($url ?? '', PHP_URL_PATH) ?: $url);
                            $displayPath = $relativeUrl ?: $asset->path;
                            $isImage = str_starts_with($asset->mime_type, 'image/');
                        @endphp

                        <tr data-content-asset="{{ $asset->id }}">
                            <td>
                                <div class="content-files-table__file-cell">
                                    <button
                                        type="button"
                                        class="content-files-table__btn content-files-table__btn--delete"
                                        title="Sil"
                                        data-content-file-action="delete"
                                        data-asset-id="{{ $asset->id }}"
                                        data-path="{{ $asset->path }}"
                                        data-url="{{ $relativeUrl }}"
                                        data-public-url="{{ $url }}"
                                        data-name="{{ $asset->original_name }}"
                                    >
                                        ×
                                    </button>

                                    @if ($url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener" class="content-files-table__file-link" title="{{ $asset->original_name }}">
                                            {{ $asset->original_name }}
                                        </a>
                                    @else
                                        <span class="content-files-table__file-link" title="{{ $asset->original_name }}">
                                            {{ $asset->original_name }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <td class="content-files-table__nowrap">
                                {{ $asset->updated_at?->format('d.m.Y H:i') }}
                            </td>

                            <td class="content-files-table__nowrap">
                                {{ $asset->human_size }}
                            </td>

                            <td class="content-files-table__actions">
                                @if ($url)
                                    <a href="{{ $url }}" target="_blank" rel="noopener" class="content-files-table__btn content-files-table__btn--gray">
                                        Dosya aç
                                    </a>
                                @endif
                            </td>
                        </tr>

                        <tr class="content-files-table__url-row" data-content-asset="{{ $asset->id }}">
                            <td colspan="4">
                                <div class="content-files-table__url-wrap">
                                    <button
                                        type="button"
                                        class="content-files-table__btn content-files-table__btn--copy"
                                        data-content-file-action="copy"
                                        data-asset-id="{{ $asset->id }}"
                                        data-url="{{ $relativeUrl }}"
                                        data-name="{{ $asset->original_name }}"
                                        data-is-image="{{ $isImage ? '1' : '0' }}"
                                        @disabled(! $displayPath)
                                    >
                                        Kopyala
                                    </button>

                                    <input
                                        type="text"
                                        value="{{ $displayPath }}"
                                        readonly
                                        class="content-files-table__url-input"
                                        onfocus="this.select()"
                                    >

                                    <button
                                        type="button"
                                        class="content-files-table__btn content-files-table__btn--insert"
                                        data-content-file-action="insert"
                                        data-asset-id="{{ $asset->id }}"
                                        data-url="{{ $relativeUrl }}"
                                        data-name="{{ $asset->original_name }}"
                                        data-is-image="{{ $isImage ? '1' : '0' }}"
                                        @disabled(! $relativeUrl)
                                    >
                                        İçeriğe ekle
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
