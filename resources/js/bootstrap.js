import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Función de respaldo para copiar al portapapeles (para navegadores sin soporte de clipboard API)
window.copyToClipboardFallback = function(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
    } catch (err) {
        console.error('No se pudo copiar al portapapeles:', err);
    }
    document.body.removeChild(textarea);
};
