const CACHE_NAME = 'dramacina-v2';

// Hanya static assets yang di-cache
const STATIC_ASSETS = [
    '/css/app.css',
    '/fonts/Bargain-Regular.woff2',
    '/fonts/Bargain-Bold.woff2',
    '/fonts/Bargain-SemiBold.woff2',
    '/fonts/Bargain-Medium.woff2',
];

// Install: pre-cache static assets
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE_NAME)
            .then(c => c.addAll(STATIC_ASSETS))
            .then(() => self.skipWaiting())
    );
});

// Activate: hapus cache versi lama
self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys()
            .then(names => Promise.all(
                names
                    .filter(n => n !== CACHE_NAME)
                    .map(n => caches.delete(n))
            ))
            .then(() => self.clients.claim())
    );
});

// Fetch: strategi berbeda per tipe request
self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;

    const url = new URL(e.request.url);

    // ─── JANGAN cache HTML / halaman dynamic ────────────────────────
    // HTML Laravel di-generate server-side, selalu harus fresh dari server
    const isHtml = e.request.headers.get('accept')?.includes('text/html');
    if (isHtml) {
        // Network only — tidak pernah cache HTML
        return;
    }

    // ─── Jangan cache request ke API eksternal ───────────────────────
    if (url.hostname !== self.location.hostname) return;
    if (url.pathname.startsWith('/api/')) return;

    // ─── Static assets: Cache First ─────────────────────────────────
    // CSS, fonts, images → serve dari cache, fallback ke network
    const isStatic = /\.(css|js|woff2?|ttf|png|jpg|jpeg|svg|webp|ico)$/.test(url.pathname);
    if (isStatic) {
        e.respondWith(
            caches.open(CACHE_NAME).then(cache =>
                cache.match(e.request).then(cached => {
                    if (cached) return cached;
                    return fetch(e.request).then(response => {
                        if (response.ok) cache.put(e.request, response.clone());
                        return response;
                    });
                })
            )
        );
        return;
    }

    // ─── Request lain: langsung ke network ──────────────────────────
    return;
});
