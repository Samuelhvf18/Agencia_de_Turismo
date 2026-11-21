import '@fortawesome/fontawesome-free/css/all.min.css';
import './bootstrap';

// Drop field data before logging out on shared devices.
document.addEventListener('submit', async (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || !['/cerrar-sesion', '/logout'].includes(new URL(form.action).pathname) || form.dataset.offlinePurged) return;
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

import { Passkeys } from '@laravel/passkeys';
import './validacion-formularios';
document.addEventListener('click', async event => {
    const toggle = event.target.closest('[data-toggle-password]');
    if (toggle) { const field = document.getElementById(toggle.dataset.togglePassword); field.type = field.type === 'password' ? 'text' : 'password'; toggle.textContent = field.type === 'password' ? 'Mostrar contraseña' : 'Ocultar contraseña'; }
    const button = event.target.closest('[data-passkey-login], [data-passkey-register]');
    if (!button) return;
    const status = document.querySelector('[data-passkey-status]');
    if (!Passkeys.isSupported() || !window.isSecureContext) { status.textContent='Tu navegador necesita HTTPS (o localhost) y soporte para claves de acceso.'; return; }
    button.disabled = true;
    try {
        if (button.hasAttribute('data-passkey-register')) {
            await Passkeys.register({name: document.getElementById('passkey-name').value || 'Mi dispositivo'});
            location.reload();
        } else {
            const result = await Passkeys.verify();
            location.assign(result.redirect || '/mi-panel');
        }
    } catch (error) { status.textContent = 'No se completó la operación. Si necesitas confirmar tu contraseña, hazlo desde el enlace del perfil. También puedes usar tu contraseña habitual.'; }
    finally { button.disabled=false; }
});
