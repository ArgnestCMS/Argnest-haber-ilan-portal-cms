<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900">
                    Bildirim Merkezi
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Forum, topluluk ve moderasyon bildirimlerinizi buradan takip edin.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <form method="POST" action="{{ route('user.notifications.readAll') }}">
                    @csrf

                    <button type="submit" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-800">
                        Tumunu Okundu Yap
                    </button>
                </form>

                <a href="/dashboard" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-bold text-white transition hover:bg-slate-800">
                    Panele Don
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-6xl px-4">
            <div
                class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                x-data="pushSubscriptionSettings()"
                x-init="init()"
            >
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="text-xs font-black uppercase text-slate-400">Push Bildirimleri</div>
                        <h3 class="mt-1 text-xl font-black text-slate-950">Tarayici bildirim ayarlari</h3>
                        <p class="mt-2 max-w-2xl text-sm font-semibold leading-6 text-slate-500" x-text="statusText"></p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            x-show="!subscribed"
                            @click="enablePush()"
                            :disabled="busy || !supported || !canSubscribe"
                            class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-black text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:bg-slate-300"
                        >
                            Push Ac
                        </button>

                        <button
                            type="button"
                            x-show="subscribed"
                            @click="disablePush()"
                            :disabled="busy"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-black text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-300"
                        >
                            Push Kapat
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-black uppercase text-slate-400">Tarayici izni</div>
                        <div class="mt-2 text-sm font-black text-slate-900" x-text="permissionLabel"></div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-black uppercase text-slate-400">Subscription</div>
                        <div class="mt-2 text-sm font-black text-slate-900" x-text="subscribed ? 'Kayitli' : 'Kayitli degil'"></div>
                    </div>

                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                        <div class="text-xs font-black uppercase text-slate-400">Gercek gonderim</div>
                        <div class="mt-2 text-sm font-black text-slate-900" x-text="sendEnabled ? 'Aktif' : 'Kapali'"></div>
                    </div>
                </div>

                <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4 text-xs font-bold leading-6 text-slate-500">
                    <div>Origin: <span class="text-slate-800" x-text="origin"></span></div>
                    <div>Secure context: <span class="text-slate-800" x-text="secureContext ? 'Evet' : 'Hayir'"></span></div>
                    <div>Service worker scope: <span class="text-slate-800" x-text="serviceWorkerScope || 'Kontrol ediliyor'"></span></div>
                    <div>VAPID key: <span class="text-slate-800" x-text="vapidStatus"></span></div>
                    <div x-show="technicalError" class="mt-2 rounded-lg bg-red-50 p-3 text-red-700" x-text="technicalError"></div>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-5" x-show="subscribed">
                    <label class="flex items-center gap-3 rounded-xl bg-slate-50 p-4">
                        <input type="checkbox" x-model="preferences.enabled" class="rounded border-slate-300 text-blue-700">
                        <span>
                            <span class="block text-sm font-black text-slate-900">Push bildirimlerini etkin tut</span>
                            <span class="block text-xs font-semibold text-slate-500">Kapandiginda subscription korunur, ancak bildirim gonderilmez.</span>
                        </span>
                    </label>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <template x-for="[type, label] in Object.entries(types)" :key="type">
                            <label class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 text-sm font-bold text-slate-700">
                                <input type="checkbox" x-model="preferences.types[type]" class="rounded border-slate-300 text-blue-700">
                                <span x-text="label"></span>
                            </label>
                        </template>
                    </div>

                    <button
                        type="button"
                        @click="savePreferences()"
                        :disabled="busy"
                        class="mt-4 rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                    >
                        Push Ayarlarini Kaydet
                    </button>
                </div>
            </div>

            <div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <a href="{{ route('user.notifications') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ ! $selectedStatus && ! $selectedGroup && ! $selectedType ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Tum</div>
                    <div class="mt-2 text-2xl font-black text-slate-950">{{ $counts['all'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['status' => 'unread']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedStatus === 'unread' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Okunmamis</div>
                    <div class="mt-2 text-2xl font-black text-blue-700">{{ $counts['unread'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['status' => 'read']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedStatus === 'read' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Okundu</div>
                    <div class="mt-2 text-2xl font-black text-slate-950">{{ $counts['read'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['group' => 'forum']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedGroup === 'forum' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Forum</div>
                    <div class="mt-2 text-2xl font-black text-red-700">{{ $counts['forum'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['group' => 'community']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedGroup === 'community' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Topluluk</div>
                    <div class="mt-2 text-2xl font-black text-green-700">{{ $counts['community'] }}</div>
                </a>

                <a href="{{ route('user.notifications', ['group' => 'moderation']) }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $selectedGroup === 'moderation' ? 'ring-2 ring-blue-600' : '' }}">
                    <div class="text-xs font-black uppercase text-slate-400">Moderasyon</div>
                    <div class="mt-2 text-2xl font-black text-yellow-700">{{ $counts['moderation'] }}</div>
                </a>
            </div>

            <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('user.notifications') }}" class="grid gap-3 md:grid-cols-[1fr_1fr_auto]">
                    <select name="status" class="rounded-xl border-slate-300 text-sm">
                        <option value="">Tum okundu durumlari</option>
                        <option value="unread" @selected($selectedStatus === 'unread')>Okunmamis</option>
                        <option value="read" @selected($selectedStatus === 'read')>Okundu</option>
                    </select>

                    <select name="type" class="rounded-xl border-slate-300 text-sm">
                        <option value="">Tum bildirim tipleri</option>
                        @foreach($typeLabels as $type => $label)
                            <option value="{{ $type }}" @selected($selectedType === $type)>{{ $label }}</option>
                        @endforeach
                    </select>

                    @if($selectedGroup)
                        <input type="hidden" name="group" value="{{ $selectedGroup }}">
                    @endif

                    <button type="submit" class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white transition hover:bg-blue-700">
                        Filtrele
                    </button>
                </form>

                @if($selectedStatus || $selectedGroup || $selectedType)
                    <a href="{{ route('user.notifications') }}" class="mt-3 inline-flex text-sm font-black text-slate-500 transition hover:text-blue-700">
                        Filtreleri temizle
                    </a>
                @endif
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <h3 class="text-xl font-black text-slate-950">
                        Bildirim Gecmisi
                    </h3>

                    <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-black text-blue-700">
                        {{ $notifications->total() }} bildirim
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($notifications as $notification)
                        <a href="{{ route('notifications.read', $notification) }}" class="block p-6 transition hover:bg-slate-50 {{ ! $notification->is_read ? 'bg-blue-50/60' : '' }}">
                            <div class="flex items-start gap-4">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-xl font-black text-white {{ match($notification->group()) { 'forum' => 'bg-red-700', 'community' => 'bg-green-700', 'moderation' => 'bg-yellow-600', default => 'bg-blue-700' } }}">
                                    {{ match($notification->group()) { 'forum' => '#', 'community' => 'C', 'moderation' => 'M', default => '!' } }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-black text-slate-900">
                                                {{ $notification->title }}
                                            </h4>

                                            <span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-black text-slate-600">
                                                {{ $notification->typeLabel() }}
                                            </span>

                                            @if(! $notification->is_read)
                                                <span class="rounded-full bg-blue-700 px-2 py-1 text-xs font-black text-white">
                                                    Yeni
                                                </span>
                                            @endif
                                        </div>

                                        <span class="text-sm font-bold text-slate-400">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                    </div>

                                    <p class="mt-3 leading-7 text-slate-600">
                                        {{ $notification->message }}
                                    </p>

                                    @if($notification->url)
                                        <div class="mt-3 text-sm font-black text-blue-700">
                                            Detaya git
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-12 text-center">
                            <h3 class="text-xl font-black text-slate-900">
                                Bildirim bulunmuyor
                            </h3>

                            <p class="mt-2 text-slate-500">
                                Secili filtreler icin bildirim yok.
                            </p>
                        </div>
                    @endforelse
                </div>

                @if($notifications->hasPages())
                    <div class="border-t border-slate-100 p-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function pushSubscriptionSettings() {
            return {
                busy: false,
                supported: false,
                canSubscribe: false,
                sendEnabled: false,
                subscribed: false,
                permission: 'default',
                publicKey: null,
                origin: window.location.origin,
                secureContext: window.isSecureContext,
                serviceWorkerScope: null,
                vapidStatus: 'Kontrol ediliyor',
                technicalError: null,
                statusText: 'Push durumu kontrol ediliyor.',
                types: {},
                preferences: {
                    enabled: true,
                    types: {},
                },

                get permissionLabel() {
                    return {
                        granted: 'Izin verildi',
                        denied: 'Engellendi',
                        default: 'Sorulmadi',
                    }[this.permission] || 'Bilinmiyor';
                },

                async init() {
                    this.supported = 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
                    this.permission = this.supported ? Notification.permission : 'denied';

                    await this.loadConfig();

                    if (!this.supported) {
                        this.statusText = 'Bu tarayici push notification desteklemiyor.';
                        return;
                    }

                    await this.ensureServiceWorker();
                    await this.refreshBrowserSubscription();
                    this.refreshStatusText();

                    window.addEventListener('push-subscription:updated', () => {
                        this.loadConfig().then(() => this.refreshBrowserSubscription()).then(() => this.refreshStatusText());
                    });
                },

                async loadConfig() {
                    const response = await fetch('{{ route('push.config') }}', {
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    if (!response.ok) {
                        throw new Error(await this.responseMessage(response, 'Push config alinamadi.'));
                    }

                    const config = await response.json();

                    this.publicKey = config.public_key || config.vapidPublicKey || null;
                    this.canSubscribe = Boolean(config.can_subscribe && this.publicKey);
                    this.vapidStatus = this.describeVapidKey(this.publicKey);
                    this.sendEnabled = Boolean(config.send_enabled);
                    this.types = config.types || {};
                    this.preferences = {
                        enabled: config.preferences?.enabled ?? true,
                        types: {},
                    };

                    Object.keys(this.types).forEach((type) => {
                        this.preferences.types[type] = config.preferences?.types?.[type] ?? true;
                    });
                },

                async ensureServiceWorker() {
                    if (!this.supported) {
                        return null;
                    }

                    const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });

                    if (registration.scope && !registration.scope.endsWith('/')) {
                        throw new Error(`Service worker scope beklenenden farkli: ${registration.scope}`);
                    }

                    const readyRegistration = await navigator.serviceWorker.ready;
                    this.serviceWorkerScope = readyRegistration.scope;

                    return readyRegistration;
                },

                async refreshBrowserSubscription() {
                    const registration = await this.ensureServiceWorker();
                    const subscription = await registration?.pushManager.getSubscription();

                    this.subscribed = Boolean(subscription);
                    this.permission = Notification.permission;
                },

                async enablePush() {
                    this.busy = true;
                    this.statusText = 'Push izni isteniyor.';

                    try {
                        this.technicalError = null;

                        if (!this.supported) {
                            this.statusText = 'Bu tarayici push notification desteklemiyor.';
                            return;
                        }

                        if (!window.isSecureContext) {
                            this.statusText = 'Push subscription icin secure context gerekli.';
                            this.technicalError = 'Local testte http://localhost veya http://127.0.0.1 kullanin; farkli local domainlerde HTTPS/ngrok gerekebilir.';
                            return;
                        }

                        await this.loadConfig();

                        if (!this.canSubscribe) {
                            this.statusText = 'VAPID public key hazir degil. Once local test anahtarlari gerekli.';
                            return;
                        }

                        const permission = await Notification.requestPermission().catch(() => 'denied');
                        this.permission = permission;

                        if (permission !== 'granted') {
                            this.statusText = 'Tarayici push izni verilmedi.';
                            return;
                        }

                        const applicationServerKey = this.urlBase64ToUint8Array(this.publicKey);
                        const registration = await this.ensureServiceWorker();

                        if (!registration.pushManager) {
                            this.statusText = 'Bu service worker registration PushManager desteklemiyor.';
                            return;
                        }

                        let subscription = await registration.pushManager.getSubscription();

                        if (!subscription) {
                            try {
                                subscription = await registration.pushManager.subscribe({
                                    userVisibleOnly: true,
                                    applicationServerKey,
                                });
                            } catch (error) {
                                throw new Error(this.subscribeErrorMessage(error));
                            }
                        }

                        await this.storeSubscription(subscription);
                        this.subscribed = true;
                        this.refreshStatusText('Subscription DB kaydi guncellendi.');
                    } catch (error) {
                        this.statusText = error?.message || 'Push subscription kaydi sirasinda hata olustu.';
                        this.technicalError = this.statusText;
                    } finally {
                        this.busy = false;
                    }
                },

                async disablePush() {
                    this.busy = true;

                    try {
                        const registration = await this.ensureServiceWorker();
                        const subscription = await registration?.pushManager.getSubscription();

                        if (subscription) {
                            const response = await fetch('{{ route('push.subscriptions.destroy') }}', {
                                method: 'DELETE',
                                headers: this.jsonHeaders(),
                                body: JSON.stringify({ endpoint: subscription.endpoint }),
                            });

                            if (!response.ok) {
                                throw new Error(await this.responseMessage(response, 'Push subscription kapatilamadi.'));
                            }

                            await subscription.unsubscribe().catch(() => false);
                        }

                        this.subscribed = false;
                        this.statusText = 'Push subscription kapatildi.';
                    } catch (error) {
                        this.statusText = error?.message || 'Push subscription kapatilirken hata olustu.';
                    } finally {
                        this.busy = false;
                    }
                },

                async savePreferences() {
                    this.busy = true;

                    try {
                        const registration = await this.ensureServiceWorker();
                        const subscription = await registration?.pushManager.getSubscription();

                        if (!subscription) {
                            this.statusText = 'Ayar kaydetmek icin once push subscription acilmali.';
                            return;
                        }

                        const response = await fetch('{{ route('push.subscriptions.preferences') }}', {
                            method: 'PATCH',
                            headers: this.jsonHeaders(),
                            body: JSON.stringify({
                                endpoint: subscription.endpoint,
                                preferences: this.preferences,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error(await this.responseMessage(response, 'Push tercihleri kaydedilemedi.'));
                        }

                        this.statusText = 'Push tercihleri kaydedildi.';
                    } catch (error) {
                        this.statusText = error?.message || 'Push tercihleri kaydedilirken hata olustu.';
                    } finally {
                        this.busy = false;
                    }
                },

                async storeSubscription(subscription) {
                    const payload = subscription.toJSON();

                    const response = await fetch('{{ route('push.subscriptions.store') }}', {
                        method: 'POST',
                        headers: this.jsonHeaders(),
                        body: JSON.stringify({
                            endpoint: payload.endpoint,
                            keys: payload.keys,
                            content_encoding: 'aes128gcm',
                            preferences: this.preferences,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error(await this.responseMessage(response, 'Push subscription DB kaydi basarisiz.'));
                    }
                },

                refreshStatusText(prefix = null) {
                    if (!this.canSubscribe) {
                        this.statusText = 'VAPID public key bulunamadi; abonelik baslatilamaz.';
                        return;
                    }

                    if (this.subscribed && this.sendEnabled) {
                        this.statusText = prefix || 'Push aktif. WEBPUSH_ENABLED acik oldugu icin gercek gonderim denenebilir.';
                        return;
                    }

                    if (this.subscribed) {
                        this.statusText = prefix || 'Push subscription kayitli. Gercek gonderim icin WEBPUSH_ENABLED henuz kapali.';
                        return;
                    }

                    this.statusText = 'Push izni vererek bu tarayici icin subscription kaydi olusturabilirsiniz.';
                },

                jsonHeaders() {
                    return {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    };
                },

                urlBase64ToUint8Array(base64String) {
                    if (!/^[A-Za-z0-9_-]+$/.test(base64String || '')) {
                        throw new Error('VAPID public key base64url formatinda degil.');
                    }

                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);

                    if (rawData.length !== 65) {
                        throw new Error(`VAPID public key decode uzunlugu hatali: ${rawData.length} byte. Beklenen: 65 byte.`);
                    }

                    const outputArray = new Uint8Array(rawData.length);

                    for (let i = 0; i < rawData.length; ++i) {
                        outputArray[i] = rawData.charCodeAt(i);
                    }

                    return outputArray;
                },

                describeVapidKey(publicKey) {
                    if (!publicKey) {
                        return 'Eksik';
                    }

                    try {
                        this.urlBase64ToUint8Array(publicKey);

                        return 'Gecerli base64url, 65 byte';
                    } catch (error) {
                        return error?.message || 'Gecersiz';
                    }
                },

                subscribeErrorMessage(error) {
                    const name = error?.name || 'Error';
                    const message = error?.message || 'Bilinmeyen subscribe hatasi';
                    const detail = `${name}: ${message}`;

                    if (message.includes('push service error') || message.includes('Registration failed')) {
                        return `Tarayici push servisi subscription olusturamadi. ${detail}. Chrome/Edge'de Google/Microsoft push servisi kapali, profil/izin sorunu var veya local origin desteklenmiyor olabilir. Local test icin http://localhost, http://127.0.0.1 ya da HTTPS/ngrok deneyin.`;
                    }

                    if (name === 'NotAllowedError') {
                        return `Bildirim izni engellenmis. ${detail}`;
                    }

                    if (name === 'InvalidStateError') {
                        return `Service worker henuz aktif degil veya mevcut subscription uyumsuz. ${detail}`;
                    }

                    return `Push subscribe hatasi. ${detail}`;
                },

                async responseMessage(response, fallback) {
                    const status = `HTTP ${response.status}`;

                    try {
                        const data = await response.clone().json();

                        if (data.message) {
                            return `${fallback} ${status}: ${data.message}`;
                        }

                        if (data.errors) {
                            const firstError = Object.values(data.errors).flat()[0];

                            if (firstError) {
                                return `${fallback} ${status}: ${firstError}`;
                            }
                        }
                    } catch (error) {
                        const text = await response.text().catch(() => '');

                        if (text) {
                            return `${fallback} ${status}: ${text.slice(0, 160)}`;
                        }
                    }

                    return `${fallback} ${status}.`;
                },
            };
        }
    </script>

</x-app-layout>
