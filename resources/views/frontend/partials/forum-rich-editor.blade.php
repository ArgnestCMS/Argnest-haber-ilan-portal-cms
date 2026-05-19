@php
    $editorId = $id ?? 'forum-editor-' . \Illuminate\Support\Str::random(8);
    $fieldName = $name ?? 'content';
    $initialValue = \App\Support\ForumContent::sanitize($value ?? '');
    $selectedMediaIds = collect(old('media_asset_ids', $mediaAssetIds ?? []))
        ->map(fn ($id) => (int) $id)
        ->filter()
        ->unique()
        ->values();
    $mediaLimits = config('media.images.limits', []);
    $mediaLimitMb = auth()->user()?->isAdmin() || auth()->user()?->isModerator()
        ? (int) ($mediaLimits['moderator_admin_mb'] ?? 50)
        : (((int) (auth()->user()?->forum_reputation ?? 0) >= (int) ($mediaLimits['trusted_reputation'] ?? 100))
            ? (int) ($mediaLimits['trusted_mb'] ?? 20)
            : (int) ($mediaLimits['default_mb'] ?? 15));
@endphp

<div
    class="forum-rich-editor rounded-xl border border-slate-200 bg-white"
    data-upload-url="{{ route('forum.images.store') }}"
    data-csrf="{{ csrf_token() }}"
    data-max-image-bytes="{{ $mediaLimitMb * 1024 * 1024 }}"
    data-max-image-label="{{ $mediaLimitMb }} MB"
>
    <div class="forum-editor-toolbar flex gap-1 overflow-x-auto border-b border-slate-200 bg-slate-50 p-2 md:flex-wrap">
        <button type="button" data-command="bold" class="shrink-0 rounded px-3 py-2 text-sm font-black text-slate-700 hover:bg-white">B</button>
        <button type="button" data-command="italic" class="shrink-0 rounded px-3 py-2 text-sm italic text-slate-700 hover:bg-white">I</button>
        <button type="button" data-command="insertUnorderedList" class="shrink-0 rounded px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white">Liste</button>
        <button type="button" data-command="formatBlock" data-value="blockquote" class="shrink-0 rounded px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white">Alinti</button>
        <button type="button" data-action="link" class="shrink-0 rounded px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white">Link</button>
        <button type="button" data-action="video" class="shrink-0 rounded px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white">Video</button>
        <label class="shrink-0 cursor-pointer rounded px-3 py-2 text-sm font-bold text-slate-700 hover:bg-white">
            Resim
            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" data-action="image" class="hidden" multiple>
        </label>
    </div>

    <div class="forum-media-drop-zone m-3 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-3 text-xs font-bold leading-5 text-slate-500 transition md:text-sm">
        Gorselleri buraya surukleyin veya Resim dugmesiyle secin. JPG, PNG, WEBP, GIF; dosya basina {{ $mediaLimitMb }} MB, en fazla 8 gorsel.
    </div>

    <div class="forum-media-preview hidden border-y border-slate-100 bg-white px-3 py-3">
        <div class="flex flex-wrap gap-3" data-media-preview-list></div>
    </div>

    <div
        id="{{ $editorId }}"
        contenteditable="true"
        class="forum-editor-surface prose min-h-52 max-w-none p-4 text-base leading-7 text-slate-800 outline-none md:min-h-44 md:text-sm"
        data-placeholder="{{ $placeholder ?? 'Iceriginizi yazin...' }}"
    >{!! $initialValue !!}</div>

    <textarea name="{{ $fieldName }}" class="hidden" required>{{ $initialValue }}</textarea>
    <div data-media-hidden-inputs>
        @foreach($selectedMediaIds as $mediaId)
            <input type="hidden" name="media_asset_ids[]" value="{{ $mediaId }}">
        @endforeach
    </div>
</div>

@once
    <style>
        .forum-editor-surface:empty::before {
            color: #94a3b8;
            content: attr(data-placeholder);
        }

        .forum-rich-content iframe,
        .forum-editor-surface iframe {
            aspect-ratio: 16 / 9;
            border: 0;
            border-radius: 0.75rem;
            display: block;
            max-width: 100%;
            width: 100%;
        }

        .forum-rich-content img,
        .forum-editor-surface img {
            border-radius: 0.75rem;
            height: auto;
            max-width: 100%;
        }

        .forum-media-drop-zone.is-dragging {
            background: #fff1f2;
            border-color: #ef4444;
            color: #b91c1c;
        }

        .forum-editor-toolbar {
            scrollbar-width: thin;
        }

        @media (max-width: 767px) {
            .forum-editor-toolbar button,
            .forum-editor-toolbar label {
                min-height: 40px;
            }
        }
    </style>

    <script>
        window.initForumRichEditors = function () {
            document.querySelectorAll('.forum-rich-editor').forEach((wrapper) => {
                if (wrapper.dataset.editorReady === '1') {
                    return;
                }

                wrapper.dataset.editorReady = '1';

                const editor = wrapper.querySelector('[contenteditable="true"]');
                const textarea = wrapper.querySelector('textarea');
                const uploadUrl = wrapper.dataset.uploadUrl;
                const csrf = wrapper.dataset.csrf;
                const maxImageLabel = wrapper.dataset.maxImageLabel || '15 MB';
                const fileInput = wrapper.querySelector('[data-action="image"]');
                const dropZone = wrapper.querySelector('.forum-media-drop-zone');
                const preview = wrapper.querySelector('.forum-media-preview');
                const previewList = wrapper.querySelector('[data-media-preview-list]');
                const hiddenInputs = wrapper.querySelector('[data-media-hidden-inputs]');
                const mediaIds = new Set(Array.from(hiddenInputs?.querySelectorAll('input') || []).map((input) => input.value));
                const maxFiles = 8;
                const maxBytes = Number(wrapper.dataset.maxImageBytes || 15 * 1024 * 1024);
                const allowedTypes = new Set(['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

                const sync = () => {
                    if (! textarea || ! editor) {
                        return;
                    }

                    textarea.value = editor.innerHTML.trim();
                };

                const insertHtml = (html) => {
                    if (! editor) {
                        return;
                    }

                    editor.focus();
                    document.execCommand('insertHTML', false, html);
                    sync();
                };

                const refreshHiddenInputs = () => {
                    if (! hiddenInputs) {
                        return;
                    }

                    hiddenInputs.innerHTML = '';
                    mediaIds.forEach((id) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'media_asset_ids[]';
                        input.value = id;
                        hiddenInputs.appendChild(input);
                    });
                };

                const refreshPreviewState = () => {
                    if (! preview || ! previewList) {
                        return;
                    }

                    preview.classList.toggle('hidden', previewList.children.length === 0);
                };

                const addPreviewItem = (file) => {
                    const item = document.createElement('div');
                    item.className = 'relative h-24 w-24 overflow-hidden rounded-lg border border-slate-200 bg-slate-100';
                    item.innerHTML = `
                        <img alt="" class="h-full w-full object-cover">
                        <div class="absolute inset-x-0 bottom-0 bg-slate-950/70 px-2 py-1 text-[10px] font-black text-white">Yukleniyor</div>
                    `;

                    item.querySelector('img').src = URL.createObjectURL(file);
                    previewList?.appendChild(item);
                    refreshPreviewState();

                    return item;
                };

                const markPreviewItem = (item, text, failed = false) => {
                    const label = item.querySelector('div');
                    label.textContent = text;
                    label.className = failed
                        ? 'absolute inset-x-0 bottom-0 bg-red-700/85 px-2 py-1 text-[10px] font-black text-white'
                        : 'absolute inset-x-0 bottom-0 bg-green-700/85 px-2 py-1 text-[10px] font-black text-white';
                };

                const removeMediaId = (id) => {
                    mediaIds.delete(String(id));
                    refreshHiddenInputs();
                };

                const uploadFile = async (file) => {
                    if (! allowedTypes.has(file.type) || file.size > maxBytes) {
                        window.alert(`Gorsel JPG, PNG, WEBP veya GIF olmali ve ${maxImageLabel} sinirini asmamalidir.`);
                        return;
                    }

                    if (mediaIds.size >= maxFiles) {
                        window.alert('Bir icerige en fazla 8 gorsel eklenebilir.');
                        return;
                    }

                    const item = addPreviewItem(file);
                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const response = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        if (! response.ok) {
                            markPreviewItem(item, 'Hata', true);
                            window.alert('Gorsel yuklenemedi.');
                            return;
                        }

                        const data = await response.json();
                        mediaIds.add(String(data.id));
                        refreshHiddenInputs();
                        markPreviewItem(item, 'Hazir');

                        const removeButton = document.createElement('button');
                        removeButton.type = 'button';
                        removeButton.className = 'absolute right-1 top-1 rounded bg-white/90 px-1.5 py-0.5 text-[10px] font-black text-slate-800';
                        removeButton.textContent = 'Sil';
                        removeButton.addEventListener('click', () => {
                            removeMediaId(data.id);
                            item.remove();
                            refreshPreviewState();
                        });
                        item.appendChild(removeButton);

                        insertHtml(`<p><img src="${data.url}" alt="" loading="lazy"></p>`);
                    } catch (error) {
                        console.error('Forum image upload failed', error);
                        markPreviewItem(item, 'Hata', true);
                        window.alert('Gorsel yuklenemedi.');
                    }
                };

                const uploadFiles = async (files) => {
                    for (const file of Array.from(files || [])) {
                        await uploadFile(file);
                    }
                };

                const youtubeEmbedUrl = (url) => {
                    try {
                        const parsed = new URL(url);
                        let id = null;

                        if (parsed.hostname.includes('youtu.be')) {
                            id = parsed.pathname.replace('/', '');
                        } else if (parsed.hostname.includes('youtube.com')) {
                            id = parsed.searchParams.get('v');

                            if (! id && parsed.pathname.startsWith('/shorts/')) {
                                id = parsed.pathname.split('/')[2];
                            }
                        }

                        return id && /^[A-Za-z0-9_-]{6,20}$/.test(id)
                            ? `https://www.youtube-nocookie.com/embed/${id}`
                            : null;
                    } catch (error) {
                        return null;
                    }
                };

                wrapper.querySelectorAll('[data-command]').forEach((button) => {
                    button.addEventListener('click', () => {
                        document.execCommand(button.dataset.command, false, button.dataset.value || null);
                        sync();
                    });
                });

                wrapper.querySelector('[data-action="link"]')?.addEventListener('click', () => {
                    const url = window.prompt('Link adresi');

                    if (! url || ! /^https?:\/\//i.test(url)) {
                        return;
                    }

                    document.execCommand('createLink', false, url);
                    sync();
                });

                wrapper.querySelector('[data-action="video"]')?.addEventListener('click', () => {
                    const url = window.prompt('YouTube linki');
                    const embedUrl = youtubeEmbedUrl(url || '');

                    if (! embedUrl) {
                        return;
                    }

                    insertHtml(`<p><iframe src="${embedUrl}" title="YouTube video" loading="lazy" allowfullscreen></iframe></p>`);
                });

                fileInput?.addEventListener('change', async (event) => {
                    const files = Array.from(event.target.files || []);
                    event.target.value = '';

                    await uploadFiles(files);
                });

                dropZone?.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    dropZone.classList.add('is-dragging');
                });

                dropZone?.addEventListener('dragleave', () => {
                    dropZone.classList.remove('is-dragging');
                });

                dropZone?.addEventListener('drop', async (event) => {
                    event.preventDefault();
                    dropZone.classList.remove('is-dragging');
                    await uploadFiles(event.dataTransfer?.files || []);
                });

                editor.addEventListener('input', sync);
                wrapper.addEventListener('forum-editor:set-html', (event) => {
                    editor.innerHTML = event.detail?.html || '';
                    sync();
                    editor.focus();
                });
                wrapper.addEventListener('forum-editor:append-html', (event) => {
                    insertHtml(event.detail?.html || '');
                });
                editor.closest('form')?.addEventListener('submit', sync);
                refreshHiddenInputs();
                refreshPreviewState();
                sync();
            });
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', window.initForumRichEditors, { once: true });
        } else {
            window.initForumRichEditors();
        }
    </script>
@endonce
