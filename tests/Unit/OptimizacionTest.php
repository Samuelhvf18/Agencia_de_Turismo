<?php

use App\Services\Io\EoqService;
use App\Services\Io\MetodoHungaro;

test('el método húngaro encuentra la asignación de costo mínimo', function () {
    // La asignación óptima es (0→1, 1→0, 2→2) con costo 1 + 2 + 2 = 5.
    $c = [
        [4, 1, 3],
        [2, 0, 5],
        [3, 2, 2],
    ];
    $h = new MetodoHungaro;
    $a = $h->resolver($c);

    expect($a)->toBe([0 => 1, 1 => 0, 2 => 2])
        ->and($h->costo($c, $a))->toEqual(5.0);
});

test('resuelve matrices rectangulares (más salidas que guías)', function () {
    $c = [
        [10, 3],
        [2, 8],
        [7, 7],
    ];
    $a = (new MetodoHungaro)->resolver($c);

    // Dos guías para tres salidas: se cubren las dos asignaciones más baratas (3 + 2).
    expect($a)->toBe([0 => 1, 1 => 0]);
});

test('evita las celdas prohibidas (costo M) cuando hay alternativa', function () {
    $M = 1_000_000;
    $c = [
        [1, $M],
        [2, 3],
    ];

    expect((new MetodoHungaro)->resolver($c))->toBe([0 => 0, 1 => 1]);
});

test('EOQ: Q* = √(2DS/H) y punto de reorden R = d·L + SS', function () {
    $eoq = (new EoqService)->calcular(1200, 100, 6, 0, 7);

    expect(round($eoq['q'], 4))->toEqual(200.0)
        ->and(round($eoq['r'], 2))->toEqual(round(1200 / 365 * 7, 2))
        ->and($eoq['q_redondeado'])->toBe(200)
        ->and(round($eoq['costo_total'], 2))->toEqual(1200.0); // (D/Q)·S + (Q/2)·H = 600 + 600
});

test('EOQ: el stock de seguridad usa z·σ·√L', function () {
    $eoq = (new EoqService)->calcular(365, 50, 10, 2.0, 4);

    expect(round($eoq['ss'], 4))->toEqual(round(1.65 * 2.0 * 2, 4))
        ->and(round($eoq['r'], 4))->toEqual(round(1 * 4 + 1.65 * 2.0 * 2, 4));
});

test('EOQ rechaza parámetros inválidos', function () {
    (new EoqService)->calcular(0, 100, 5);
})->throws(InvalidArgumentException::class);
