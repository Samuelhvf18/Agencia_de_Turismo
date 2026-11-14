/* Only static public libraries are cached. Authenticated HTML/JSON must never
 * be served from another account's cache. In-page scans still work offline. */
const CACHE = 'pipocas-checkin-static-v2';
const CDN = ['unpkg.com', 'cdn.tailwindcss.com', 'cdn.jsdelivr.net', 'cdnjs.cloudflare.com'];
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', e => e.waitUntil(
    caches.keys().then(keys => Promise.all(keys.filter(k => k.startsWith('pipocas-checkin') && k !== CACHE).map(k => caches.delete(k))))
        .then(() => self.clients.claim())
));
self.addEventListener('fetch', e => {
    const url = new URL(e.request.url);
    if (e.request.method !== 'GET' || !CDN.includes(url.hostname)) return;
    e.respondWith(caches.open(CACHE).then(async cache => {
        const saved = await cache.match(e.request);
        if (saved) return saved;
        const response = await fetch(e.request);
        if (response.ok) await cache.put(e.request, response.clone());
        return response;
    }));
});
