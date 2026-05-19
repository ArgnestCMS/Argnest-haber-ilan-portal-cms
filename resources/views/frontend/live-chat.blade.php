@extends('frontend.layout')

@section('title', 'Canlı Sohbet | ' . ($siteSetting?->site_name ?? 'ilanhaber.net'))

@section(
    'meta_description',
    'ilanhaber.net canlı sohbet alanı ile üyeler gündem, ilanlar ve haberler hakkında anlık etkileşim kurabilir.'
)

@section('meta_keywords', 'canlı sohbet, topluluk sohbeti, ilan sohbet, haber sohbet')

@section('canonical', route('live-chat.index'))

@section('schema')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Canlı Sohbet',
            'description' => 'ilanhaber.net canlı sohbet alanı ile üyeler anlık etkileşim kurabilir.',
            'url' => route('live-chat.index'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('content')

<style>
    [x-cloak] {
        display: none !important;
    }

    @media (max-width: 767px) {
        .mobile-chat-shell {
            padding-bottom: calc(82px + env(safe-area-inset-bottom, 0px));
        }

        .mobile-chat-list {
            height: min(58vh, 520px);
        }

        .mobile-chat-composer {
            position: sticky;
            bottom: calc(74px + env(safe-area-inset-bottom, 0px));
            z-index: 20;
        }
    }
</style>

<section
    x-data="liveChat({
        messagesUrl: '{{ route('live-chat.messages') }}',
        onlineUrl: '{{ route('live-chat.online') }}',
        typingUrl: '{{ route('live-chat.typing') }}',
        typingStoreUrl: '{{ route('live-chat.typing.store') }}',
        storeUrl: '{{ route('live-chat.messages.store') }}',
        csrf: '{{ csrf_token() }}',
        initialMessages: @js($messages->map(fn ($message) => [
            'id' => $message->id,
            'user_id' => $message->user_id,
            'user' => $message->user?->name ?? 'Sistem',
            'message' => e($message->message),
            'time' => $message->created_at?->format('H:i'),
            'is_online' => $message->user?->isOnline() ?? false,
            'reputation' => $message->user?->forum_reputation ?? 0,
            'report_url' => route('reports.live-chat-messages.store', $message),
        ])),
        initialOnline: @js($onlineUsers->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'reputation' => $user->forum_reputation ?? 0,
            'is_online' => $user->isOnline(),
            'last_seen' => $user->last_seen_at?->diffForHumans(),
        ])),
        canSend: @js(auth()->check() && ($siteSetting?->live_chat_enabled ?? false)),
        authUserId: @js(auth()->id()),
    })"
    x-init="init()"
    class="mobile-chat-shell mx-auto grid max-w-7xl gap-5 px-3 py-6 md:px-4 md:py-10 lg:grid-cols-[1fr_340px]"
>
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 bg-slate-950 px-4 py-4 text-white md:px-6 md:py-5">
            <div>
                <h1 class="text-2xl font-black">Canlı Sohbet</h1>
                <p class="mt-1 text-sm text-slate-300">Polling tabanlı topluluk sohbeti</p>
            </div>

            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-black">
                {{ $siteSetting?->live_chat_enabled ? 'Aktif' : 'Kapalı' }}
            </span>
        </div>

        <div class="mobile-chat-list h-[520px] overflow-y-auto bg-slate-50 p-3 md:p-6">
            <template x-if="messages.length === 0">
                <div class="flex h-full items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white text-center">
                    <div>
                        <div class="text-xl font-black text-slate-950">Henüz mesaj yok</div>
                        <p class="mt-2 text-sm text-slate-500">İlk mesaj geldiğinde burada görünecek.</p>
                    </div>
                </div>
            </template>

            <div class="space-y-4">
                <template x-for="message in messages" :key="message.id">
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="font-black text-slate-950" x-text="message.user"></span>
                                <span
                                    class="rounded-full px-2 py-0.5 text-[11px] font-black"
                                    :class="message.is_online ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500'"
                                    x-text="message.is_online ? 'Online' : 'Offline'"
                                ></span>
                                <span class="text-xs font-bold text-slate-400" x-text="message.reputation + ' itibar'"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    x-show="config.authUserId && message.user_id !== config.authUserId"
                                    type="button"
                                    @click="openReport(message)"
                                    class="rounded-lg border border-slate-200 px-2 py-1 text-xs font-black text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700"
                                >
                                    Raporla
                                </button>
                                <div class="text-xs font-bold text-slate-400" x-text="message.time"></div>
                            </div>
                        </div>

                        <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-700" x-html="message.message"></p>
                    </div>
                </template>
            </div>

            <div x-show="typingUsers.length > 0" x-cloak class="mt-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-xs font-black text-red-700">
                <span x-text="typingText()"></span>
            </div>
        </div>

        <div class="mobile-chat-composer border-t border-slate-200 bg-white p-3 md:p-4">
            @if($siteSetting?->live_chat_enabled)
                @auth
                    <form @submit.prevent="send()" class="space-y-3">
                        <div class="flex flex-col gap-2 sm:flex-row sm:gap-3">
                            <input
                                type="text"
                                x-model="draft"
                                @input="notifyTyping()"
                                maxlength="500"
                                placeholder="Mesajınızı yazın..."
                                class="min-w-0 flex-1 rounded-lg border-slate-300 py-3 text-base md:text-sm"
                            >
                            <button
                                type="submit"
                                class="rounded-lg bg-red-600 px-5 py-3 text-sm font-black text-white transition hover:bg-red-700 disabled:opacity-60 sm:py-2"
                                :disabled="sending || draft.trim().length < 2"
                            >
                                Gönder
                            </button>
                        </div>

                        <p x-show="notice" x-text="notice" class="text-xs font-bold text-slate-500"></p>
                    </form>
                @else
                    <div class="rounded-xl border border-blue-100 bg-blue-50 p-4 text-sm font-bold text-blue-800">
                        Mesaj yazmak için giriş yapın. Sohbeti okumak herkese açık.
                    </div>
                @endauth
            @else
                <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 text-sm font-bold text-yellow-800">
                    Canlı sohbet panelden kapalı.
                </div>
            @endif
        </div>
    </div>

    <aside class="space-y-5 lg:order-none">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-black text-slate-950">Online Kullanıcılar</h2>
                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-black text-green-700" x-text="onlineUsers.length"></span>
            </div>

            <div class="mt-4 space-y-3">
                <template x-for="user in onlineUsers" :key="user.id">
                    <div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 p-3">
                        <div>
                            <div class="text-sm font-black text-slate-900" x-text="user.name"></div>
                            <div class="text-xs font-bold text-slate-500">
                                <span x-text="user.reputation + ' itibar'"></span>
                                <span> · </span>
                                <span x-text="user.last_seen || 'su an aktif'"></span>
                            </div>
                        </div>
                        <span class="h-2.5 w-2.5 rounded-full bg-green-500"></span>
                    </div>
                </template>

                <div x-show="onlineUsers.length === 0" class="text-sm text-slate-500">
                    Şu an online kullanıcı görünmüyor.
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-black text-slate-950">Sohbet Kuralları</h2>
            <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600">
                <p>Saygılı dil kullanımı, spam yapmama ve konu dışına çıkmama temel kuraldır.</p>
                <p>Mesajlar moderasyon uyumlu kaydedilir; gerekirse panelden yönetim katmanı eklenebilir.</p>
            </div>
        </div>

        <div class="rounded-2xl border border-red-100 bg-red-50 p-6 shadow-sm">
            <h2 class="text-lg font-black text-red-800">Canlı Aktivite</h2>
            <p class="mt-3 text-sm leading-6 text-red-700/80">Yayın, duyuru ve forum akışlarını tek merkezden takip edin.</p>
            <a href="{{ route('live-activity.index') }}" class="mt-5 inline-flex rounded-lg bg-red-600 px-4 py-2 text-sm font-black text-white transition hover:bg-red-700">
                Merkeze Dön
            </a>
        </div>
    </aside>

    @auth
        <div
            x-show="reportMessage"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4"
        >
            <form method="POST" :action="reportMessage?.report_url" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                @csrf
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-black text-slate-950">Mesaji Raporla</h2>
                        <p class="mt-1 text-sm text-slate-500" x-text="reportMessage?.user"></p>
                    </div>
                    <button type="button" @click="reportMessage = null" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-black text-slate-600">
                        Kapat
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    <select name="reason" required class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="">Sebep secin</option>
                        <option value="spam">Spam</option>
                        <option value="insult">Hakaret</option>
                        <option value="inappropriate">Uygunsuz icerik</option>
                        <option value="misinformation">Yanlis bilgi</option>
                        <option value="advertising">Reklam</option>
                        <option value="other">Diger</option>
                    </select>
                    <textarea name="details" rows="3" maxlength="1000" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Ek aciklama"></textarea>
                    <button type="submit" class="w-full rounded-lg bg-red-600 px-4 py-3 text-sm font-black text-white transition hover:bg-red-700">
                        Rapor Gonder
                    </button>
                </div>
            </form>
        </div>
    @endauth
</section>

<script>
function liveChat(config) {
    return {
        config,
        messages: config.initialMessages || [],
        onlineUsers: config.initialOnline || [],
        draft: '',
        notice: '',
        sending: false,
        reportMessage: null,
        typingUsers: [],
        typingChannel: null,
        typingTimer: null,
        lastTypingPing: 0,
        init() {
            this.fetchMessages();
            this.fetchOnlineUsers();
            this.fetchTypingUsers();
            this.listenForMessages();
            this.listenForPresence();
            setInterval(() => this.fetchMessages(), 5000);
            setInterval(() => this.fetchOnlineUsers(), 10000);
            setInterval(() => this.fetchTypingUsers(), 3000);
        },
        listenForMessages() {
            if (!window.Echo) {
                return;
            }

            window.Echo.channel('live.chat')
                .listen('.live-chat.message.created', (message) => {
                    if (this.messages.some((item) => item.id === message.id)) {
                        return;
                    }

                    this.messages = [...this.messages, message].slice(-50);
                    this.fetchOnlineUsers();
                });
        },
        listenForPresence() {
            if (!window.Echo || !config.authUserId) {
                return;
            }

            this.typingChannel = window.Echo.join('live.chat.presence')
                .here((users) => {
                    this.onlineUsers = users.map((user) => ({
                        id: user.id,
                        name: user.name,
                        reputation: user.reputation || 0,
                        is_online: true,
                        last_seen: 'su an aktif',
                    }));
                })
                .joining((user) => {
                    if (this.onlineUsers.some((item) => item.id === user.id)) {
                        return;
                    }

                    this.onlineUsers = [...this.onlineUsers, {
                        id: user.id,
                        name: user.name,
                        reputation: user.reputation || 0,
                        is_online: true,
                        last_seen: 'su an aktif',
                    }];
                })
                .leaving((user) => {
                    this.onlineUsers = this.onlineUsers.filter((item) => item.id !== user.id);
                })
                .listenForWhisper('typing', (event) => {
                    if (!event?.id || event.id === config.authUserId) {
                        return;
                    }

                    this.markTyping({ id: event.id, name: event.name });
                });
        },
        fetchMessages() {
            fetch(config.messagesUrl)
                .then(response => response.json())
                .then(data => {
                    this.messages = data.messages || [];
                });
        },
        fetchOnlineUsers() {
            fetch(config.onlineUrl)
                .then(response => response.json())
                .then(data => {
                    this.onlineUsers = data.users || [];
                });
        },
        fetchTypingUsers() {
            fetch(config.typingUrl)
                .then(response => response.json())
                .then(data => {
                    this.typingUsers = (data.users || []).filter((user) => user.id !== config.authUserId);
                });
        },
        notifyTyping() {
            if (!config.canSend || !this.draft.trim()) {
                return;
            }

            const now = Date.now();

            if (now - this.lastTypingPing < 1800) {
                return;
            }

            this.lastTypingPing = now;

            if (this.typingChannel) {
                this.typingChannel.whisper('typing', {
                    id: config.authUserId,
                    name: @js(auth()->user()?->name),
                });
            }

            fetch(config.typingStoreUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': config.csrf,
                },
            }).catch(() => {});
        },
        markTyping(user) {
            this.typingUsers = [
                ...this.typingUsers.filter((item) => item.id !== user.id),
                user,
            ];

            window.clearTimeout(this.typingTimer);
            this.typingTimer = window.setTimeout(() => {
                this.typingUsers = [];
            }, 4000);
        },
        typingText() {
            if (this.typingUsers.length === 1) {
                return `${this.typingUsers[0].name} yaziyor...`;
            }

            return `${this.typingUsers.length} kisi yaziyor...`;
        },
        send() {
            const message = this.draft.trim();

            if (!message || this.sending || !config.canSend) {
                return;
            }

            this.sending = true;
            this.notice = '';

            fetch(config.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': config.csrf,
                },
                body: JSON.stringify({ message }),
            })
                .then(async response => {
                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Mesaj gönderilemedi.');
                    }

                    this.draft = '';
                    this.notice = data.message || 'Mesaj gönderildi.';
                    this.fetchMessages();
                    this.fetchOnlineUsers();
                })
                .catch(error => {
                    this.notice = error.message;
                })
                .finally(() => {
                    this.sending = false;
                });
        },
        openReport(message) {
            this.reportMessage = message;
        },
    }
}
</script>

@endsection
