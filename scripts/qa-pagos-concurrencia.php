<?php

use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Services\CodeGeneratorService;
use App\Services\Pagos\PagoService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

// Run only against a disposable, migrated and seeded PostgreSQL QA database.
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (! app()->environment('testing') || ! str_ends_with(config('database.connections.pgsql.database'), '_qa')) {
    throw new RuntimeException('Use APP_ENV=testing and a disposable database ending in _qa.');
}
Queue::fake();
$service = app(PagoService::class);
$action = $argv[1] ?? '';
if ($action === 'setup') {
    $r = Reserva::create(['cod' => CodeGeneratorService::generate('RES'),
        'sal_tur_id' => SalidaTour::firstOrFail()->id, 'nom_ctc' => 'QA concurrency',
        'cor_ctc' => 'qa@example.test', 'tel_ctc' => '70000000', 'cnt_ptc' => 1, 'pre_tot' => 100, 'est' => 'pendiente']);
    echo $r->id;
} elseif ($action === 'iniciar') {
    try {
        $p = $service->iniciar(Reserva::findOrFail($argv[2]), 'qr', 100);
        echo json_encode(['creado' => $p->id]).PHP_EOL;
    } catch (ValidationException $e) {
        echo json_encode(['rechazado' => true]).PHP_EOL;
    }
} elseif ($action === 'legacy') {
    $r = Reserva::findOrFail($argv[2]);
    $p = $r->comprobante->pagos()->firstOrFail();
    $p2 = $p->replicate();
    $p2->ref_trx = 'QA-'.bin2hex(random_bytes(10));
    $p2->save();
    echo $p->ref_trx.PHP_EOL.$p2->ref_trx.PHP_EOL;
} elseif ($action === 'webhook') {
    $p = $service->procesarNotificacion(['ref_trx' => $argv[2], 'est' => 'completado', 'mto' => 100]);
    echo json_encode(['estado' => $p->est]).PHP_EOL;
} elseif ($action === 'verify') {
    $r = Reserva::findOrFail($argv[2]);
    $pagos = $r->comprobante->pagos();
    $completados = (clone $pagos)->where('est', 'completado')->count();
    $total = (float) (clone $pagos)->where('est', 'completado')->sum('mto');
    if ($completados !== 1 || $total !== 100.0) {
        throw new RuntimeException('Sobrepago o resultado inesperado');
    }
    echo json_encode(['completados' => $completados, 'total' => $total, 'reserva' => $r->id]).PHP_EOL;
}
