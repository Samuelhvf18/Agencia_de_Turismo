import { test } from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { JSDOM } from 'jsdom';
const script = readFileSync(new URL('../../public/js/consentimiento.js', import.meta.url), 'utf8');
function page(saved = null) {
    const dom = new JSDOM(`<section id="privacidad-banner" hidden><button data-consent-reject>Rechazar</button><button data-consent-settings>Configurar</button><button data-consent-accept>Aceptar</button></section><dialog id="privacidad-config"><form id="privacidad-form"><input type="checkbox" name="analytics"><input type="checkbox" name="external"><button data-consent-reject>Rechazar</button><button data-consent-close>Cerrar</button></form></dialog><script type="text/plain" data-consent="external">window.externalExecutions=(window.externalExecutions||0)+1;</script><script type="text/plain" data-consent="analytics">window.analyticsExecutions=(window.analyticsExecutions||0)+1;</script>`, {url:'https://pipocas.test', runScripts:'dangerously'});
    const { window } = dom;
    window.HTMLDialogElement.prototype.showModal = function () { this.open = true; };
    window.HTMLDialogElement.prototype.close = function () { this.open = false; };
    if (saved) window.localStorage.setItem('pipocas-consent-v1', JSON.stringify(saved));
    window.eval(script);
    return window;
}
test('sin permiso no se ejecutan recursos opcionales', () => {
    const w = page();
    assert.equal(w.externalExecutions, undefined);
    assert.equal(w.analyticsExecutions, undefined);
    assert.equal(w.document.getElementById('privacidad-banner').hidden, false);
    w.close();
});
test('rechazar guarda la decisión y se respeta al volver a entrar', () => {
    const w = page();
    w.document.querySelector('[data-consent-reject]').click();
    const consent = JSON.parse(w.localStorage.getItem('pipocas-consent-v1'));
    assert.equal(consent.external, false);
    assert.equal(consent.analytics, false);
    const next = page(consent);
    assert.equal(next.document.getElementById('privacidad-banner').hidden, true);
    assert.equal(next.externalExecutions, undefined);
    w.close(); next.close();
});
test('configurar permite aceptar una categoría y mantener la otra desactivada', () => {
    const w = page();
    w.document.querySelector('[data-consent-settings]').click();
    assert.equal(w.document.getElementById('privacidad-config').open, true);
    const form = w.document.getElementById('privacidad-form');
    form.elements.external.checked = true;
    form.dispatchEvent(new w.Event('submit', {bubbles:true, cancelable:true}));
    assert.equal(w.externalExecutions, 1);
    assert.equal(w.analyticsExecutions, undefined);
    assert.equal(w.pipocasConsent.allowed('external'), true);
    w.close();
});
test('el consentimiento vencido se vuelve a solicitar', () => {
    const w = page({version:1, at:Date.now()-181*864e5, external:true, analytics:true});
    assert.equal(w.document.getElementById('privacidad-banner').hidden, false);
    assert.equal(w.externalExecutions, undefined);
    w.close();
});
test('aceptar dos veces no duplica la ejecución de recursos', () => {
    const w = page();
    w.document.querySelector('[data-consent-accept]').click();
    w.document.querySelector('[data-consent-accept]').click();
    assert.equal(w.externalExecutions, 1);
    assert.equal(w.analyticsExecutions, 1);
    w.close();
});
