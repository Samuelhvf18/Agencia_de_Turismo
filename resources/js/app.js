import './bootstrap';

// Drop field data before logging out on shared devices.
document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || new URL(form.action).pathname !== '/logout' || form.dataset.offlinePurged) return;
    event.preventDefault();
    try {
    localStorage.removeItem('pipocas-offline-activo');
    indexedDB.deleteDatabase('pipocas-offline-v1');
    if (indexedDB.databases) {
        for (const database of await indexedDB.databases()) {
            if (database.name?.startsWith('pipocas-checkin')) indexedDB.deleteDatabase(database.name);
        }
    }
    if ('caches' in window) {
        for (const key of await caches.keys()) {
            if (key.startsWith('pipocas-checkin')) await caches.delete(key);
        }
    }
    } finally {
        form.dataset.offlinePurged = '1';
        form.requestSubmit();
    }
}, true);

const account = document.querySelector('meta[name="offline-account"]')?.content;
try {
    const active = JSON.parse(localStorage.getItem('pipocas-offline-activo') || 'null');
    if (account && active && String(active.usuario) !== account) {
        indexedDB.deleteDatabase('pipocas-offline-v1');
        localStorage.removeItem('pipocas-offline-activo');
    }
} catch { localStorage.removeItem('pipocas-offline-activo'); }
