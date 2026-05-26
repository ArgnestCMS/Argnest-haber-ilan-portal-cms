<div class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="text-sm font-black text-blue-950">Forum AI Assistant</div>
            <p class="mt-1 text-xs font-bold text-blue-800/80">Local rule-based oneriler: baslik, ozet, etiket, benzer konu ve moderasyon sinyali.</p>
        </div>
        <button type="button" @click="analyze()" class="rounded-lg bg-blue-700 px-4 py-2 text-xs font-black text-white transition hover:bg-blue-800" :disabled="loading">
            <span x-text="loading ? 'Analiz ediliyor...' : 'Oneri Al'"></span>
        </button>
    </div>

    <p class="mt-3 rounded-lg bg-red-50 p-3 text-xs font-bold text-red-700" x-show="error" x-text="error" style="display:none;"></p>

    <div class="mt-4 grid gap-4 md:grid-cols-2" x-show="hasResult" style="display:none;">
        <div x-show="(result.title_suggestions || []).length">
            <div class="text-xs font-black uppercase text-blue-900">Baslik Onerileri</div>
            <div class="mt-2 space-y-2">
                <template x-for="title in result.title_suggestions || []" :key="title">
                    <button type="button" @click="applyTitle(title)" class="block w-full rounded-lg bg-white px-3 py-2 text-left text-xs font-bold text-slate-700 ring-1 ring-blue-100 transition hover:bg-blue-50" x-text="title"></button>
                </template>
            </div>
        </div>

        <div x-show="(result.tag_suggestions || []).length">
            <div class="text-xs font-black uppercase text-blue-900">Etiket Onerileri</div>
            <div class="mt-2 flex flex-wrap gap-2">
                <template x-for="tag in result.tag_suggestions || []" :key="tag">
                    <button type="button" @click="appendTag(tag)" class="rounded-full bg-white px-3 py-1.5 text-xs font-black text-blue-800 ring-1 ring-blue-100" x-text="'#' + tag"></button>
                </template>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="text-xs font-black uppercase text-blue-900">Ozet</div>
            <p class="mt-2 rounded-lg bg-white p-3 text-sm leading-6 text-slate-700" x-text="result.summary"></p>
        </div>

        <div>
            <div class="text-xs font-black uppercase text-blue-900">Yazim Onerileri</div>
            <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-slate-700">
                <template x-for="item in result.writing_suggestions || []" :key="item">
                    <li x-text="item"></li>
                </template>
            </ul>
        </div>

        <div>
            <div class="text-xs font-black uppercase text-blue-900">AI Moderasyon Aciklamasi</div>
            <p class="mt-2 text-sm leading-6 text-slate-700" x-text="result.moderation_explanation"></p>
            <p class="mt-2 text-xs font-bold text-slate-500" x-text="'Mod risk ozeti: ' + (result.moderator_risk_summary || '-')"></p>
        </div>

        <div class="md:col-span-2" x-show="(result.similar_topics || []).length">
            <div class="text-xs font-black uppercase text-blue-900">Benzer Konu Uyarisi</div>
            <div class="mt-2 grid gap-2">
                <template x-for="topic in result.similar_topics || []" :key="topic.url">
                    <a :href="topic.url" target="_blank" class="rounded-lg bg-white p-3 text-sm font-bold text-slate-700 ring-1 ring-blue-100 transition hover:bg-blue-50">
                        <span x-text="topic.title"></span>
                        <span class="ml-2 text-xs text-slate-400" x-text="'(' + (topic.replies || 0) + ' cevap)'"></span>
                    </a>
                </template>
            </div>
        </div>
    </div>
</div>

@once
    <script>
    function forumAssistant(config) {
        return {
            loading: false,
            error: '',
            result: {},
            get hasResult() {
                return Object.keys(this.result || {}).length > 0;
            },
            analyze() {
                this.loading = true;
                this.error = '';

                const form = this.$el;
                const payload = {
                    type: config.type,
                    title: form.querySelector('[name="title"]')?.value || '',
                    content: form.querySelector('[name="content"]')?.value || '',
                    forum_category_id: form.querySelector('[name="forum_category_id"]')?.value || null,
                    topic_id: config.topicId || null,
                };

                fetch(config.url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrf,
                    },
                    body: JSON.stringify(payload),
                })
                    .then(async response => {
                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Oneri alinamadi.');
                        }

                        this.result = data;
                    })
                    .catch(error => {
                        this.error = error.message || 'Oneri alinamadi.';
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },
            applyTitle(title) {
                const input = this.$el.querySelector('[name="title"]');

                if (input) {
                    input.value = title;
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }
            },
            appendTag(tag) {
                const input = this.$el.querySelector('[name="tag_names"]');

                if (!input) {
                    return;
                }

                const current = input.value
                    .split(',')
                    .map(item => item.trim())
                    .filter(Boolean);

                if (!current.some(item => item.toLowerCase() === String(tag).toLowerCase())) {
                    current.push(tag);
                }

                input.value = current.slice(0, 5).join(', ');
            },
        };
    }
    </script>
@endonce




