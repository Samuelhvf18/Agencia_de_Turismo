<?php

use App\Models\ItemEquipo;
use App\Models\PerfilGuia;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Services\CodeGeneratorService;
use App\Services\Operaciones\SalidaService;
use App\Services\Pagos\PagoService;
use App\Services\Reservas\CupoAgotadoException;
use App\Services\Reservas\ReservaService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
if (! app()->environment('testing') || ! str_ends_with(config('database.connections.pgsql.database'), '_qa')) {
    throw new RuntimeException('Solo APP_ENV=testing y base desechable terminada en _qa.');
}
Queue::fake();
$action = $argv[1] ?? '';
if ($action === 'setup') {
    $tour = Tour::where('rta_amg', 'valle-de-la-luna-y-telefericos')->firstOrFail();
    $make = fn ($cap, $days) => SalidaTour::create(['tur_id' => $tour->id, 'fec_sal' => today()->addDays($days), 'hra_sal' => '08:00', 'cap_max' => $cap, 'est' => 'programada']);
    $cap = $make(1, 110);
    $equipment = $make(10, 111);
    $tipo = $tour->equipos()->firstOrFail();
    $item = ItemEquipo::create(['tip_eqp_id' => $tipo->id, 'tal' => 'QA-'.bin2hex(random_bytes(2)), 'cnt_tot' => 1, 'cnt_dis' => 1]);
    $r = Reserva::create(['cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $cap->id,
        'nom_ctc' => 'QA carrera', 'cor_ctc' => 'qa@example.test', 'tel_ctc' => '70000000', 'cnt_ptc' => 1, 'pre_tot' => 100]);
    $r->participantes()->create(['nom_cpl' => 'QA carrera', 'tip_doc' => 'PASAPORTE', 'num_doc' => 'RACE-'.bin2hex(random_bytes(4)), 'eda' => 30]);
    $p = app(PagoService::class)->iniciar($r, 'qr', 100);
    $r2 = $make(1, 112);
    $r3 = $make(1, 112);
    echo json_encode(['cap' => $cap->id, 'eqp' => $equipment->id, 'item' => $item->id, 'tipo' => $tipo->id,
        'rsv' => $r->id, 'pago' => $p->ref_trx, 'salidas' => [$r2->id, $r3->id], 'guia' => PerfilGuia::firstOrFail()->id]);
} elseif ($action === 'reservar') {
    try {
        $b = ['sal_tur_id' => (int) $argv[2], 'contacto' => ['nom_ctc' => 'QA concurrente', 'cor_ctc' => 'qa@example.test', 'tel_ctc' => '70000000'],
            'participantes' => [['nom_cpl' => 'QA concurrente', 'tip_doc' => 'PASAPORTE', 'num_doc' => 'QA-'.bin2hex(random_bytes(6)), 'eda' => 30, 'pai_id' => null]]];
        if (! empty($argv[3])) {
            $b['alquileres'] = [[(int) $argv[3] => (int) $argv[4]]];
        }
        echo json_encode(['creada' => app(ReservaService::class)->confirmar($b, null)->id]).PHP_EOL;
    } catch (ValidationException|CupoAgotadoException $e) {
        echo json_encode(['rechazada' => class_basename($e)]).PHP_EOL;
    }
} elseif ($action === 'asignar') {
    try {
        app(SalidaService::class)->asignar(SalidaTour::findOrFail($argv[2]), ['prf_gui_id' => (int) $argv[3]]);
        echo "asignada\n";
    } catch (ValidationException $e) {
        echo "rechazada por conflicto\n";
    }
} elseif ($action === 'cancelar') {
    app(ReservaService::class)->cancelar(Reserva::findOrFail($argv[2]), 'QA carrera');
    echo "cancelada\n";
} elseif ($action === 'pagar') {
    $p = app(PagoService::class)->procesarNotificacion(['ref_trx' => $argv[2], 'est' => 'completado', 'mto' => 100]);
    echo 'pago: '.$p->est.PHP_EOL;
} elseif ($action === 'verify') {
    $f = json_decode(file_get_contents($argv[2]), true, flags: JSON_THROW_ON_ERROR);
    $cap = Reserva::where('sal_tur_id', $f['cap'])->where('id', '<>', $f['rsv'])->count();
    // La reserva de pago/cancelación se excluye de la prueba de cupos y se cancela antes de ella.
    $eqp = Reserva::where('sal_tur_id', $f['eqp'])->count();
    $stock = ItemEquipo::findOrFail($f['item'])->cnt_dis;
    $asignadas = SalidaTour::whereIn('id', $f['salidas'])->where('prf_gui_id', $f['guia'])->count();
    $r = Reserva::findOrFail($f['rsv']);
    $activos = $r->boletos()->where('boletos.est', '<>', 'cancelado')->count();
    if ($cap !== 1 || $eqp !== 1 || $stock !== 0 || $asignadas !== 1 || $r->est !== 'cancelada' || $activos !== 0) {
        throw new RuntimeException('Invariantes de concurrencia incumplidas.');
    }
    echo json_encode(compact('cap', 'eqp', 'stock', 'asignadas', 'activos'), JSON_PRETTY_PRINT).PHP_EOL;
}
