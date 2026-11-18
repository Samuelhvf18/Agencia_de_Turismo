export const soloLetras = valor => String(valor).replace(/[^\p{L}\p{M} ]/gu, '');
export const soloNumeros = valor => String(valor).replace(/[^0-9]/g, '');
export function errorClave(valor) {
    if ([...valor].length < 8) return 'Usa al menos 8 caracteres.';
    if ([...valor].length > 128) return 'Usa como máximo 128 caracteres.';
    if (!/\p{L}/u.test(valor)) return 'Incluye al menos una letra.';
    if (!/\p{N}/u.test(valor)) return 'Incluye al menos un número.';
    return '';
}
export function errorImagenes(archivos, maxKb = 4096, maxArchivos = 10) {
    if (archivos.length > maxArchivos) return `Selecciona como máximo ${maxArchivos} imágenes.`;
    for (const archivo of archivos) {
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(archivo.type)) return 'Solo se admiten imágenes JPG, PNG o WebP.';
        if (archivo.size > maxKb * 1024) return `Cada imagen debe pesar como máximo ${maxKb / 1024} MB.`;
        if (!archivo.size) return 'La imagen seleccionada está vacía.';
    }
    return '';
}

if (typeof document !== 'undefined') {
    const selectorClave = '[data-clave-nueva],input[autocomplete="new-password"]:not([id*="confirmation"]):not([name*="confirmation"])';
    function comprobar(campo) {
        if (!(campo instanceof HTMLInputElement)) return;
        let error = '';
        if (campo.matches(selectorClave)) {
            campo.minLength = 8; campo.maxLength = 128;
            error = campo.value ? errorClave(campo.value) : '';
            let ayuda = campo.parentElement.querySelector('[data-reglas-clave]');
            if (!ayuda) { ayuda = document.createElement('p'); ayuda.dataset.reglasClave = ''; ayuda.className = 'mt-2 text-xs text-slate-500'; campo.parentElement.append(ayuda); }
            ayuda.textContent = campo.value ? (error || 'La contraseña cumple los requisitos.') : 'Mínimo 8 caracteres, una letra y un número. Máximo 128 caracteres.';
        }
        if (campo.matches('[data-clave-confirmacion],[id*="password_confirmation"],[name="password_confirmation"]')) {
            const original = campo.form?.querySelector(selectorClave);
            error = original && campo.value && original.value !== campo.value ? 'Las contraseñas no coinciden.' : '';
        }
        if (campo.matches('[data-letras]')) {
            campo.pattern = '[\\p{L}\\p{M}]+(?: +[\\p{L}\\p{M}]+)*';
            if (campo.value && !/^[\p{L}\p{M}]+(?: +[\p{L}\p{M}]+)*$/u.test(campo.value.trim())) error = 'Escribe solo letras y espacios.';
        }
        if (!campo.matches('input[type=file]')) campo.setCustomValidity(error);
    }
    document.addEventListener('input', evento => {
        const campo = evento.target;
        if (!(campo instanceof HTMLInputElement)) return;
        if (campo.matches('[data-letras]')) campo.value = soloLetras(campo.value);
        if (campo.matches('[data-numeros]')) campo.value = soloNumeros(campo.value);
        comprobar(campo);
        if (campo.matches(selectorClave)) campo.form?.querySelectorAll('[id*="password_confirmation"],[name="password_confirmation"]').forEach(comprobar);
    }, true);
    document.addEventListener('blur', evento => {
        const campo = evento.target;
        if (campo instanceof HTMLInputElement && campo.type === 'email') campo.value = campo.value.trim().toLowerCase();
    }, true);
    document.addEventListener('change', evento => {
        const campo = evento.target;
        if (!(campo instanceof HTMLInputElement) || !campo.matches('input[type=file][data-imagenes]')) return;
        const error = errorImagenes([...campo.files], Number(campo.dataset.maxKb || 4096), Number(campo.dataset.maxFiles || 10));
        campo.setCustomValidity(error);
        let ayuda = campo.parentElement.querySelector('[data-error-imagenes]');
        if (!ayuda) { ayuda = document.createElement('p'); ayuda.dataset.errorImagenes = ''; ayuda.setAttribute('role', 'alert'); ayuda.className = 'mt-2 text-sm text-red-700'; campo.after(ayuda); }
        ayuda.textContent = error;
        if (error) { evento.preventDefault(); evento.stopImmediatePropagation(); campo.reportValidity(); }
    }, true);
    document.addEventListener('submit', evento => {
        const form = evento.target;
        if (!(form instanceof HTMLFormElement)) return;
        form.querySelectorAll('input').forEach(comprobar);
        if (!form.checkValidity()) { evento.preventDefault(); evento.stopImmediatePropagation(); form.reportValidity(); }
    }, true);
    document.addEventListener('error', evento => {
        const img = evento.target;
        if (img instanceof HTMLImageElement && img.hasAttribute('data-imagen-tour')) {
            img.removeAttribute('data-imagen-tour'); img.src = '/images/tour-sin-imagen.svg';
        }
    }, true);
}
