(() => {
    const KEY = 'pipocas-consent-v1';
    const banner = document.getElementById('privacidad-banner');
    const dialog = document.getElementById('privacidad-config');
    const form = document.getElementById('privacidad-form');
    const read = () => {
        try {
            const value = JSON.parse(localStorage.getItem(KEY));
            return value?.version === 1 && Date.now() - value.at < 180 * 864e5 ? value : null;
        } catch { return null; }
    };
    let current = read();
    const allowed = category => current?.[category] === true;
    const activate = async () => {
        for (const script of document.querySelectorAll('script[data-consent]')) {
            if (!allowed(script.dataset.consent) || script.dataset.activated) continue;
            script.dataset.activated = '1';
            const run = document.createElement('script');
            if (script.dataset.src) run.src = script.dataset.src;
            else run.textContent = script.textContent;
            run.async = false;
            if (script.dataset.src) {
                await new Promise(resolve => { run.onload = resolve; run.onerror = resolve; script.after(run); });
            } else script.after(run);
        }
        window.dispatchEvent(new CustomEvent('pipocas:consent', {detail: current}));
    };
    const save = (analytics, external) => {
        const revoke = (allowed('external') && !external) || (allowed('analytics') && !analytics);
        current = {version:1, at:Date.now(), analytics, external};
        try { localStorage.setItem(KEY, JSON.stringify(current)); } catch { /* La decisión sigue vigente. */ }
        banner.hidden = true;
        dialog.close();
        if (revoke) { location.reload(); return; }
        activate();
    };
    window.pipocasConsent = {allowed, configure() {
        form.elements.analytics.checked = allowed('analytics');
        form.elements.external.checked = allowed('external');
        dialog.showModal();
    }};
    document.addEventListener('click', event => {
        if (event.target.closest('[data-consent-settings]')) { event.preventDefault(); window.pipocasConsent.configure(); }
        if (event.target.closest('[data-consent-reject]')) save(false, false);
        if (event.target.closest('[data-consent-accept]')) save(true, true);
        if (event.target.closest('[data-consent-close]')) dialog.close();
    });
    form.addEventListener('submit', event => { event.preventDefault(); save(form.elements.analytics.checked, form.elements.external.checked); });
    banner.hidden = current !== null;
    activate();
})();
