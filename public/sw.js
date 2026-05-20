const CACHE_VERSION = 'pwa-foundation-v3';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const RUNTIME_CACHE = `${CACHE_VERSION}-runtime`;

const STATIC_ASSETS = [
    '/offline.html',
    '/site.webmanifest',
    '/pwa/icon.svg',
    '/pwa/maskable-icon.svg'
];

const ADMIN_PREFIX = '/admin';
const REALTIME_PREFIXES = [
    '/bildirimler/count',
    '/bildirimler/latest',
    '/push/',
    '/mesajlar/count',
    '/presence/',
    '/canli-aktivite/latest',
    '/canli-sohbet/mesajlar',
    '/canli-sohbet/online',
    '/canli-sohbet/typing'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => ! key.startsWith(CACHE_VERSION))
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const request = event.request;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin || shouldBypass(url.pathname)) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => caches.match('/offline.html'))
        );

        return;
    }

    if (isCacheableAsset(url.pathname)) {
        event.respondWith(staleWhileRevalidate(request));
    }
});

self.addEventListener('push', (event) => {
    let payload = {};

    try {
        payload = event.data ? event.data.json() : {};
    } catch (error) {
        payload = {
            title: 'Yeni bildirim',
            body: event.data?.text() || 'Yeni bir bildiriminiz var.'
        };
    }

    const title = payload.title || 'ilanhaber.net';
    const options = {
        body: payload.body || payload.message || 'Yeni bir bildiriminiz var.',
        icon: '/pwa/icon.svg',
        badge: '/pwa/maskable-icon.svg',
        tag: payload.tag || 'ilanhaber-notification',
        renotify: true,
        data: {
            url: payload.url || '/',
            notification_id: payload.notification_id || null
        }
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                for (const client of clientList) {
                    if (client.url === targetUrl && 'focus' in client) {
                        return client.focus();
                    }
                }

                return clients.openWindow(targetUrl);
            })
    );
});

function shouldBypass(pathname) {
    return pathname.startsWith(ADMIN_PREFIX)
        || REALTIME_PREFIXES.some((prefix) => pathname.startsWith(prefix));
}

function isCacheableAsset(pathname) {
    return pathname.startsWith('/build/')
        || pathname.startsWith('/css/')
        || pathname.startsWith('/js/')
        || pathname.startsWith('/fonts/')
        || pathname.startsWith('/pwa/')
        || pathname === '/site.webmanifest'
        || pathname === '/favicon.ico';
}

async function staleWhileRevalidate(request) {
    const cache = await caches.open(RUNTIME_CACHE);
    const cached = await cache.match(request);
    const network = fetch(request)
        .then((response) => {
            if (response.ok) {
                cache.put(request, response.clone());
            }

            return response;
        })
        .catch(() => cached);

    return cached || network;
}
