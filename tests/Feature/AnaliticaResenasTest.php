<?php

use App\Http\Controllers\Reservas\AccesoReserva;
use App\Models\ProyeccionDemanda;
use App\Models\Reserva;
use App\Models\Resena;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\User;
use App\Services\Analitica\ProyeccionDemandaService;
use App\Services\CodeGeneratorService;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class]);
    Queue::fake();
});

function reservaEn(Tour $tour, string $fecha, string $est, int $pax = 2): Reserva
{
    $salida = SalidaTour::create(['tur_id' => $tour->id, 'fec_sal' => $fecha, 'hra_sal' => '08:00:00', 'cap_max' => 20, 'est' => 'completada']);

    return Reserva::create(['cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id, 'nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'ana@example.com',
        'tel_ctc' => '70000000', 'cnt_ptc' => $pax, 'pre_tot' => 100, 'est' => $est]);
}

test('solo una reserva completada puede dejar una reseña, y una sola vez', function () {
    $tour = Tour::first();
    $pendiente = reservaEn($tour, today()->addDays(3)->toDateString(), Reserva::EST_CONFIRMADA);
    $completada = reservaEn($tour, today()->subDays(3)->toDateString(), Reserva::EST_COMPLETADA);

    $this->withSession(['reservas.acceso' => [$pendiente->cod, $completada->cod]]);

    $this->post(route('reservas.resena', $pendiente), ['cal' => 5])->assertSessionHasErrors('cal');
    $this->get(route('reservas.estado', $completada))->assertOk()->assertSee('¿Qué tal estuvo el tour?');

    $this->post(route('reservas.resena', $completada), ['cal' => 4, 'com' => 'Excelente guía <b>muy</b> atento'])->assertSessionHasNoErrors();
    $this->post(route('reservas.resena', $completada), ['cal' => 1])->assertSessionHasErrors('cal');

    $resena = Resena::first();
    expect(Resena::count())->toBe(1)
        ->and($resena->com)->toBe('Excelente guía muy atento')
        ->and($resena->mdt['verificada'])->toBeTrue();

    $this->get(route('tours.show', $tour))->assertOk()->assertSee('Reseñas verificadas')->assertSee('4,0')->assertSee('Excelente guía muy atento');
});

test('sin acceso a la reserva no se puede reseñar', function () {
    $completada = reservaEn(Tour::first(), today()->subDay()->toDateString(), Reserva::EST_COMPLETADA);

    $this->post(route('reservas.resena', $completada), ['cal' => 5])->assertForbidden();
});

test('la proyección usa regresión lineal con historial y promedio móvil sin él', function () {
    $servicio = app(ProyeccionDemandaService::class);

    [$valores, $modelo] = $servicio->proyectar([2, 4, 6, 8, 10, 12], 2);
    expect($modelo)->toBe(ProyeccionDemandaService::MODELO_REGRESION)
        ->and(round($valores[0], 4))->toEqual(14.0)
        ->and(round($valores[1], 4))->toEqual(16.0);

    [$valores, $modelo] = $servicio->proyectar([0, 0, 0, 0, 3, 5], 2);
    expect($modelo)->toBe(ProyeccionDemandaService::MODELO_PROMEDIO)->and($valores)->toBe([4.0, 4.0]);
});

test('el comando proyecta la demanda por tour desde vista_demanda_por_tour_mes y guarda el modelo', function () {
    $tour = Tour::where('rta_amg', 'valle-de-la-luna-y-telefericos')->first();
    foreach ([6, 5, 4, 3, 2, 1] as $i => $hace) {
        for ($k = 0; $k <= $i; $k++) {
            reservaEn($tour, now()->startOfMonth()->subMonths($hace)->addDays(3 + $k)->toDateString(), Reserva::EST_COMPLETADA);
        }
    }

    $this->artisan('io:proyectar-demanda', ['--meses' => 3])->assertSuccessful();

    $p = ProyeccionDemanda::where('tur_id', $tour->id)->orderBy('ani_per')->orderBy('mes_per')->get();
    expect($p)->toHaveCount(3)
        ->and($p->first()->ver_mdl)->toBe(ProyeccionDemandaService::MODELO_REGRESION)
        ->and($p->first()->rsv_pry)->toBe(7); // serie 1..6 → siguiente mes = 7
});

test('el dashboard y la analítica muestran datos de las vistas SQL', function () {
    $tour = Tour::first();
    $r = reservaEn($tour, today()->toDateString(), Reserva::EST_COMPLETADA, 3);
    $r->participantes()->create(['nom_cpl' => 'Hans Müller', 'tip_doc' => 'PASAPORTE', 'num_doc' => 'DE1', 'eda' => 40, 'pai_id' => \App\Models\Pais::where('cod_iso', 'DE')->value('id')]);
    app(\App\Services\Pagos\PagoService::class)->registrarEfectivo(reservaEn($tour, today()->addDays(5)->toDateString(), Reserva::EST_PENDIENTE), 100);
    $this->artisan('io:proyectar-demanda')->assertSuccessful();

    $this->actingAs(User::factory()->rol(User::ROL_ADMIN)->create());
    $this->get(route('admin.dashboard'))->assertOk()->assertSee('Bs. 100,00')->assertSee('Alertas del sistema');
    $this->get(route('admin.analitica'))->assertOk()
        ->assertSee('Alemania')
        ->assertSee($tour->nom)
        ->assertSee(ProyeccionDemandaService::MODELO_PROMEDIO);
});
