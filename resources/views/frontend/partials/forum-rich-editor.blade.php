@php
    $editorId = $id ?? 'forum-editor-' . \Illuminate\Support\Str::random(8);
    $fieldName = $name ?? 'content';
    $initialValue = \App\Support\ForumContent::sanitize($value ?? '');
@endphp

<div
    class="forum-rich-editor rounded-xl border border-slate-200 bg-white"
    data-upload-url="{{ route('forum.images.store') }}"
    data-csrf="{{ csrf_token() }}"
>
    <div class="flex flex-wrap gap-1 border-b border-slate-200 bg-slate-50 p-2">
        <button type="button" data-command="bold" class="rounded px-3 py-1.5 text-sm font-black text-slate-700 hover:bg-white">B</button>
        <button type="button" data-command="italic" class="rounded px-3 py-1.5 text-sm italic text-slate-700 hover:bg-white">I</button>
        <button type="button" data-command="insertUnorderedList" class="rounded px-3 py-1.5 text-sm font-bold text-slate-700 hover:bg-white">Liste</button>
        <button type="button" data-command="formatBlock" data-value="blockquote" class="rounded px-3 py-1.5 text-sm font-bold text-slate-700 hover:bg-white">Alinti</button>
        <button type="button" data-action="link" class="rounded px-3 py-1.5 text-sm font-bold text-slate-700 hover:bg-white">Link</button>
        <button type="button" data-action="video" class="rounded px-3 py-1.5 text-sm font-bold text-slate-700 hover:bg-white">Video</button>
        <label class="cursor-pointer rounded px-3 py-1.5 text-sm font-bold text-slate-700 hover:bg-white">
            Resim
            <input type="file" accept="image/jpeg,image/png,image/webp,image/gif" data-action="image" class="hidden">
        </label>
    </div>

    <div
        id="{{ $editorId }}"
        contenteditable="true"
        class="forum-editor-surface prose min-h-44 max-w-none p-4 text-sm leading-7 text-slate-800 outline-none"
        data-placeholder="{{ $placeholder ?? 'Iceriginizi yazin...' }}"
    >{!! $initialValue !!}</div>

    <textarea name="{{ $fieldName }}" class="hidden" required>{{ $initialValue }}</textarea>
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.forum-rich-editor').forEach((wrapper) => {
                const editor = wrapper.querySelector('[contenteditable="true"]');
                const textarea = wrapper.querySelector('textarea');
                const uploadUrl = wrapper.dataset.uploadUrl;
                const csrf = wrapper.dataset.csrf;

                const sync = () => {
                    textarea.value = editor.innerHTML.trim();
                };

                const insertHtml = (html) => {
                    editor.focus();
                    document.execCommand('insertHTML', false, html);
                    sync();
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

                wrapper.querySelector('[data-action="image"]')?.addEventListener('change', async (event) => {
                    const file = event.target.files?.[0];
                    event.target.value = '';

                    if (! file || ! file.type.match(/^image\/(jpeg|png|webp|gif)$/) || file.size > 2 * 1024 * 1024) {
                        window.alert('Resim JPG, PNG, WEBP veya GIF olmali ve 2 MB sinirini asmamalidir.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('image', file);

                    const response = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (! response.ok) {
                        window.alert('Resim yuklenemedi.');
                        return;
                    }

                    const data = await response.json();
                    insertHtml(`<p><img src="${data.url}" alt="" loading="lazy"></p>`);
                });

                editor.addEventListener('input', sync);
                editor.closest('form')?.addEventListener('submit', sync);
                sync();
            });
        });
    </script>
@endonce
