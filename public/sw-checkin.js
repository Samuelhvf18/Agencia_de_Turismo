const CACHE='pipocas-checkin-static-v3';
const ASSETS=['/checkin-offline.html','/js/checkin-offline.js','/js/checkin-store.js'];
self.addEventListener('install',e=>e.waitUntil(caches.open(CACHE).then(c=>c.addAll(ASSETS)).then(()=>self.skipWaiting())));
self.addEventListener('activate',e=>e.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>k.startsWith('pipocas-checkin')&&k!==CACHE).map(k=>caches.delete(k)))).then(()=>self.clients.claim())));
self.addEventListener('fetch',e=>{const u=new URL(e.request.url);if(e.request.method!=='GET'||u.origin!==self.location.origin)return;
 if(ASSETS.includes(u.pathname)){e.respondWith(caches.open(CACHE).then(async c=>await c.match(u.pathname)||fetch(e.request)));return}
 if(e.request.mode==='navigate'&&u.pathname.startsWith('/guias/'))e.respondWith(fetch(e.request).catch(()=>caches.open(CACHE).then(c=>c.match('/checkin-offline.html'))));
});
