import test from 'node:test';
import assert from 'node:assert/strict';
import { soloLetras, soloNumeros, errorClave, errorImagenes } from '../../resources/js/validacion-formularios.js';

test('nombres conservan tildes y marcas Unicode y eliminan números y signos', () => {
    assert.equal(soloLetras('María José 123!'), 'María José ');
    assert.equal(soloLetras('A\u0301lvaro'), 'A\u0301lvaro');
    assert.equal(soloNumeros('+591 7ABC-123'), '5917123');
});
test('contraseñas coinciden con las reglas Unicode del servidor', () => {
    for (const clave of ['abc', 'abcdefgh', '12345678', 'a'.repeat(129)+'1']) assert.notEqual(errorClave(clave), '');
    assert.equal(errorClave('Clave2026'), '');
    assert.equal(errorClave('Clavese١٢'), '');
});
test('imágenes rechazan extensiones simuladas, tamaño y cantidad excesiva', () => {
    assert.equal(errorImagenes([{type:'image/png',size:2048}]), '');
    assert.notEqual(errorImagenes([{type:'text/plain',size:200}]), '');
    assert.notEqual(errorImagenes([{type:'image/svg+xml',size:200}]), '');
    assert.notEqual(errorImagenes([{type:'image/jpeg',size:5*1024*1024}]), '');
    assert.notEqual(errorImagenes(Array.from({length:11},()=>({type:'image/png',size:1}))), '');
});
