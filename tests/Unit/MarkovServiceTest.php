<?php

use App\Services\Io\MarkovService;

// Matriz mensual inicial del seeder; resultados de referencia en el Informe, sección 3.5.2.

function matrizInicial(): array
{
    $svc = new MarkovService;

    return $svc->normalizar(MarkovService::MATRIZ_INICIAL);
}

test('la matriz inicial es estocástica', function () {
    expect((new MarkovService)->esEstocastica(matrizInicial()))->toBeTrue();
});

test('probabilidades de absorción y tiempos coinciden con el informe', function () {
    $an = (new MarkovService)->analizar(matrizInicial());

    expect($an['B']['prospecto']['fidelizado'])->toEqualWithDelta(0.3883, 1e-4)
        ->and($an['B']['prospecto']['perdido'])->toEqualWithDelta(0.6117, 1e-4)
        ->and($an['B']['reservado']['fidelizado'])->toEqualWithDelta(0.6214, 1e-4)
        ->and($an['B']['completado']['fidelizado'])->toEqualWithDelta(0.6990, 1e-4)
        ->and($an['t']['prospecto'])->toEqualWithDelta(4.3811, 1e-4)
        ->and($an['t']['reservado'])->toEqualWithDelta(3.0097, 1e-4)
        ->and($an['t']['completado'])->toEqualWithDelta(2.1359, 1e-4)
        ->and($an['N']['prospecto']['prospecto'])->toEqualWithDelta(2.5, 1e-9);
});

test('cada fila de B suma 1', function () {
    $an = (new MarkovService)->analizar(matrizInicial());

    foreach ($an['B'] as $fila) {
        expect(array_sum($fila))->toEqualWithDelta(1.0, 1e-9);
    }
});

test('la inversa por Gauss-Jordan es correcta', function () {
    $inv = (new MarkovService)->inversa([[4, 7], [2, 6]]);

    expect($inv[0][0])->toEqualWithDelta(0.6, 1e-12)
        ->and($inv[0][1])->toEqualWithDelta(-0.7, 1e-12)
        ->and($inv[1][0])->toEqualWithDelta(-0.2, 1e-12)
        ->and($inv[1][1])->toEqualWithDelta(0.4, 1e-12);
});

test('la proyección a 12 meses desde prospecto coincide con el informe', function () {
    $p = (new MarkovService)->proyectar(matrizInicial(), 'prospecto', 12);

    expect($p['series']['fidelizado'][12])->toEqualWithDelta(0.3790, 1e-4)
        ->and($p['series']['perdido'][12])->toEqualWithDelta(0.6062, 1e-4)
        ->and($p['series']['perdido'][1])->toEqualWithDelta(0.15, 1e-9);
});

test('la estimación conserva la fila previa sin datos y fija los absorbentes', function () {
    $svc = new MarkovService;
    $p = $svc->estimarMatriz(['prospecto' => ['prospecto' => 6, 'reservado' => 3, 'perdido' => 1]], matrizInicial());

    expect($p['prospecto']['reservado'])->toEqualWithDelta(0.3, 1e-12)
        ->and($p['reservado']['completado'])->toEqualWithDelta(0.8, 1e-12)
        ->and($p['fidelizado']['fidelizado'])->toBe(1.0)
        ->and($svc->esEstocastica($p))->toBeTrue();
});
