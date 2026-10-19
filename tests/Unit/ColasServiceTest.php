<?php

use App\Services\Io\ColasService;

// Valores de referencia del marco teórico (Informe, sección 3.5.1):
// tour de 5 h (μ = 4,8), λ = 8 reservas/día, K = 14, Ce = Bs 15/h, Cs = Bs 25/h.

test('M/M/2/14 coincide con el ejemplo del informe', function () {
    $m = (new ColasService)->metricas(8, 4.8, 2, 14);

    expect($m['rho'])->toEqualWithDelta(0.8333, 1e-4)
        ->and($m['p0'])->toEqualWithDelta(0.0978, 1e-4)
        ->and($m['lq'])->toEqualWithDelta(2.7049, 1e-4)
        ->and($m['l'])->toEqualWithDelta(4.3462, 1e-4)
        ->and($m['wq_hrs'])->toEqualWithDelta(8.2403, 1e-3)
        ->and($m['w_hrs'])->toEqualWithDelta(13.2403, 1e-3)
        ->and($m['pq'])->toEqualWithDelta(0.7239, 1e-4)
        ->and($m['p_rec'])->toEqualWithDelta(0.0152, 1e-4)
        ->and(array_sum($m['pn']))->toEqualWithDelta(1.0, 1e-12);
});

test('W = Wq + 1/μ y λe = λ(1 − P_K)', function () {
    $m = (new ColasService)->metricas(8, 4.8, 3, 14);

    expect($m['w_hrs'] - $m['wq_hrs'])->toEqualWithDelta(24 / 4.8, 1e-9)
        ->and($m['lambda_e'])->toEqualWithDelta(8 * (1 - $m['p_rec']), 1e-12);
});

test('el costo total recomienda tres guías en el ejemplo', function () {
    $svc = new ColasService;
    $esc = collect($svc->escenarios(8, 4.8, 14, 15, 25, 4));

    expect($esc->firstWhere('c', 2)['ct'])->toEqualWithDelta(49.54, 0.01)
        ->and($esc->firstWhere('c', 3)['ct'])->toEqualWithDelta(38.91, 0.01)
        ->and($esc->firstWhere('optimo', true)['c'])->toBe(3);
});

test('con un servidor y K = 1 se reduce a M/M/1/1 (Erlang B)', function () {
    $m = (new ColasService)->metricas(2, 4, 1, 1);

    expect($m['p_rec'])->toEqualWithDelta(2 / 6, 1e-12)
        ->and($m['lq'])->toBe(0.0);
});

test('sin llegadas el sistema está vacío', function () {
    $m = (new ColasService)->metricas(0, 4.8, 2, 10);

    expect($m['p0'])->toBe(1.0)->and($m['wq_hrs'])->toBe(0.0);
});

test('rechaza parámetros inválidos', function () {
    (new ColasService)->metricas(5, 4, 3, 2);
})->throws(InvalidArgumentException::class);

test('clasifica el estado de la cola', function () {
    $svc = new ColasService;

    expect($svc->estado(['rho' => 1.2, 'p_rec' => 0.0]))->toBe('saturado')
        ->and($svc->estado(['rho' => 0.5, 'p_rec' => 0.08]))->toBe('saturado')
        ->and($svc->estado(['rho' => 0.85, 'p_rec' => 0.01]))->toBe('atencion')
        ->and($svc->estado(['rho' => 0.4, 'p_rec' => 0.0]))->toBe('estable');
});
