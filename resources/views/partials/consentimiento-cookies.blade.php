<style>
.privacidad-panel{position:fixed;bottom:16px;left:16px;right:16px;max-width:760px;margin:auto;z-index:90;background:white;color:#343a40;border:1px solid #ddd;border-radius:18px;padding:20px;box-shadow:0 12px 40px #0003;font:14px/1.6 system-ui}.privacidad-panel[hidden]{display:none}.privacidad-panel button,.privacidad-dialog button{border:1px solid #1f5f8b;border-radius:10px;padding:10px 16px;margin:6px 4px 0 0;color:#1f5f8b;background:white;cursor:pointer}.privacidad-panel button:last-child,.privacidad-dialog button[type=submit]{background:#1f5f8b;color:white}.privacidad-dialog{width:min(540px,calc(100% - 32px));margin:auto;border:0;border-radius:20px;padding:24px;color:#343a40}.privacidad-dialog::backdrop{background:#0008}.privacidad-dialog label{display:block;margin:16px 0}.privacidad-dialog h2{font-size:24px;font-weight:bold}
.privacidad-abrir{position:fixed;right:12px;bottom:10px;z-index:70;border:1px solid #ddd;border-radius:9px;padding:6px 10px;background:white;color:#1f5f8b;font:12px system-ui}
</style>
<button type="button" class="privacidad-abrir" data-consent-settings>Configurar cookies</button>
<section id="privacidad-banner" class="privacidad-panel" hidden aria-label="Preferencias de cookies">
    <strong>Tu privacidad importa</strong><p>Las cookies de sesión y seguridad son necesarias para reservar. Analítica y contenido externo permanecen desactivados hasta que los autorices.</p>
    <button type="button" data-consent-reject>Rechazar opcionales</button><button type="button" data-consent-settings>Configurar cookies</button><button type="button" data-consent-accept>Aceptar opcionales</button>
</section>
<dialog id="privacidad-config" class="privacidad-dialog" aria-labelledby="privacidad-titulo">
    <form id="privacidad-form">
        <h2 id="privacidad-titulo">Configurar cookies</h2>
        <label><input type="checkbox" checked disabled> Esenciales (siempre activas): sesión, seguridad y esta decisión.</label>
        <label><input type="checkbox" name="analytics"> Analítica: estadísticas opcionales. Actualmente no hay un proveedor de analítica instalado.</label>
        <label><input type="checkbox" name="external"> Contenido externo: cargar mapas de OpenStreetMap y sus recursos.</label>
        <p>Puedes retirar tu permiso en cualquier momento desde «Configurar cookies».</p>
        <button type="button" data-consent-reject>Rechazar opcionales</button><button type="submit">Guardar preferencias</button><button type="button" data-consent-close>Cerrar</button>
    </form>
</dialog>
<script src="{{ asset('js/consentimiento.js') }}" defer></script>
