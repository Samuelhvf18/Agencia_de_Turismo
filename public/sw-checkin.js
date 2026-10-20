/*
 * Service worker del check-in (RNF-15): permite reabrir las pantallas de
 * /guias/ sin conexión. Las páginas van "red primero" (si no hay red se
 * sirve la última copia) y las librerías de CDN "caché primero".
 * Los escaneos sin conexión los guarda la página en IndexedDB.
 */
const CACHE = 'pipocas-checkin-v1';
const CDN = ['unpkg.com', 'cdn.tailwindcss.com', 'cdn.jsdelivr.net', 'cdnjs.cloudflare.com'];

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', e => e.waitUntil(
    caches.keys().then(claves => Promise.all(claves.filter(c => c !== CACHE).map(c => caches.delete(c)))).then(() => self.clients.claim())
));

self.addEventListener('fetch', evento => {
    const req = evento.request;
    if (req.method !== 'GET') return;
    const url = new URL(req.url);

    if (CDN.includes(url.hostname)) {
        evento.respondWith(caches.match(req).then(c => c || fetch(req).then(r => {
            const copia = r.clone();
            caches.open(CACHE).then(cache => cache.put(req, copia));
            return r;
        })));
        return;
    }

    if (url.origin === self.location.origin && url.pathname.startsWith('/guias/')) {
        evento.respondWith(fetch(req).then(r => {
            if (r.ok) {
                const copia = r.clone();
                caches.open(CACHE).then(cache => cache.put(req, copia));
            }
            return r;
        }).catch(() => caches.match(req)));
    }
});
