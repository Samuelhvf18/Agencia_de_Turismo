import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

function formulario(valores = {}) {
    const contexto = {};
    vm.runInNewContext(readFileSync(new URL('../../public/js/io-ajustes.js', import.meta.url), 'utf8'), contexto);
    const actual = { horas: 7, guias: 1, capacidad: 12, demanda: 2, espera: 15, ocioso: 25, mu: 3.4286 };
    return contexto.ioAjustes({ actual, valores: { ...actual, ...valores }, duracion: 8, observado: 0, configurado: true });
}

test('el resumen enumera solo los cambios y deshacer recupera todos los valores guardados', () => {
    const f = formulario();
    f.guias = '3';
    f.horas = '8.5';
    f.espera = '20';
    f.motivo = 'Otro equipo';
    f.revisado = true;
    assert.equal(f.cambios.length, 3);
    assert.ok(f.cambios.some(c => c.includes('Guías: 1 → 3')));
    f.restablecer();
    assert.equal(f.horas, 7);
    assert.equal(f.guias, 1);
    assert.equal(f.espera, 15);
    assert.equal(f.motivo, '');
    assert.equal(f.revisado, false);
    assert.equal(f.cambios.length, 0);
});

test('usar duración publicada requiere revisar otra vez y no modifica los demás supuestos', () => {
    const f = formulario();
    f.revisado = true;
    f.usarDuracion();
    assert.equal(f.horas, 8);
    assert.equal(f.revisado, false);
    assert.equal(f.capacidad, 12);
    assert.equal(f.demanda, 2);
    assert.equal(f.config.observado, 0);
});

test('impide guardar datos inválidos o sin revisión explícita', () => {
    for (const valor of [{ horas: 0 }, { horas: '' }, { guias: 1.5 }, { guias: 61 }, { guias: '20', capacidad: '12' }, { capacidad: 201 }, { demanda: -1 }, { espera: '' }, { ocioso: -2 }]) {
        const f = formulario(valor);
        assert.notEqual(f.error, '');
        f.revisado = true;
        let bloqueado = false;
        f.enviar({ preventDefault() { bloqueado = true; } });
        assert.equal(bloqueado, true);
    }
    const f = formulario();
    let bloqueado = false;
    f.enviar({ preventDefault() { bloqueado = true; } });
    assert.equal(bloqueado, true);
    f.revisado = true;
    f.enviar({ preventDefault() { assert.fail('Formulario válido y revisado'); } });
});

test('la referencia de demanda cero y costos cero son válidos', () => {
    const f = formulario({ demanda: 0, espera: 0, ocioso: 0 });
    assert.equal(f.error, '');
    assert.equal(f.numero(''), '—');
});
