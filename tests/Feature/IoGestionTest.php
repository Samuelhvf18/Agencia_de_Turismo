<?php

use App\Models\AlquilerEquipoReserva;
use App\Models\ColaMetricaDiaria;
use App\Models\ColaParametroTour;
use App\Models\Idioma;
use App\Models\MarkovClienteEstado;
use App\Models\MarkovCohort;
use App\Models\MarkovCohortDistribucion;
use App\Models\Pais;
use App\Models\PerfilGuia;
use App\Models\PropuestaAsignacion;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\TipoEquipo;
use App\Models\Tour;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\CodeGeneratorService;
use App\Services\Io\ColasService;
use App\Services\Io\EoqService;
use App\Services\Io\MarkovService;
use App\Services\Io\OptimizadorService;
use Carbon\Carbon;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\IoColasMarkovSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class, IoColasMarkovSeeder::class]);
    Queue::fake();
    $this->admin = User::factory()->rol(User::ROL_ADMIN)->create();
});

function nuevoGuia(string $nombre, array $idiomas, int $exp, int $limite = 40): PerfilGuia
{
    $u = User::factory()->rol(User::ROL_GUIA)->create(['nom' => $nombre]);
    $g = PerfilGuia::create(['usr_id' => $u->id, 'ani_exp' => $exp, 'lim_hrs_sem' => $limite, 'act' => true]);
    $g->idiomas()->sync(Idioma::whereIn('cod_iso', $idiomas)->pluck('id'));

    return $g;
}

test('las pantallas de IO II cargan', function () {
    $this->actingAs($this->admin);
    $this->get(route('admin.io.index'))->assertOk()->assertSee('Planificación inteligente');
    $this->get(route('admin.io.parametros'))->assertOk()->assertSee('Ajustes del análisis');
    $this->get(route('admin.io.propuestas'))->assertOk()->assertSee('Elige qué necesitas organizar');
    $this->get(route('admin.io.eoq'))->assertOk()->assertSee('Casco de seguridad');
    $this->get(route('admin.io.colas'))->assertOk();
});

test('RF-40: valida μ > 0 y K ≥ c y deja un solo juego activo por tour', function () {
    $this->actingAs($this->admin);
    $tour = Tour::first();

    $this->post(route('admin.io.parametros.store', $tour), ['lmb' => 2, 'mu' => 0, 'c_srv' => 3, 'k_cap' => 2])
        ->assertSessionHasErrors(['mu', 'k_cap']);

    $this->post(route('admin.io.parametros.store', $tour), ['lmb' => 2.5, 'mu' => 3, 'c_srv' => 2, 'k_cap' => 12])->assertSessionHasNoErrors();
    $this->post(route('admin.io.parametros.store', $tour), ['lmb' => 3, 'mu' => 3, 'c_srv' => 3, 'k_cap' => 12])->assertSessionHasNoErrors();

    expect(ColaParametroTour::where('tur_id', $tour->id)->where('act', true)->count())->toBe(1)
        ->and(ColaParametroTour::where('tur_id', $tour->id)->where('act', true)->value('c_srv'))->toBe(3)
        ->and(ColaParametroTour::where('tur_id', $tour->id)->count())->toBeGreaterThanOrEqual(3);
});

test('RF-22: el húngaro prefiere al guía con el idioma de los pasajeros y respeta los choques', function () {
    SalidaTour::query()->update(['prf_gui_id' => null]);
    PerfilGuia::query()->update(['act' => false]);
    $frances = nuevoGuia('Guía Francés', ['es', 'fr'], 3);
    $novato = nuevoGuia('Guía Novato', ['es'], 1);

    $tour = Tour::where('rta_amg', 'valle-de-la-luna-y-telefericos')->first();
    $salida = $tour->salidas()->orderBy('fec_sal')->first();
    // Pasajeros franceses en la salida.
    $r = Reserva::create(['cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id, 'nom_ctc' => 'Marie', 'cor_ctc' => 'm@example.com', 'tel_ctc' => '70000000', 'cnt_ptc' => 1, 'pre_tot' => 100]);
    $r->participantes()->create(['nom_cpl' => 'Marie Curie', 'tip_doc' => 'PASAPORTE', 'num_doc' => 'FR1', 'eda' => 40, 'pai_id' => Pais::where('cod_iso', 'FR')->value('id')]);

    $res = app(OptimizadorService::class)->proponerGuias(today(), $salida->fec_sal->copy());

    $propuesta = PropuestaAsignacion::where('sal_tur_id', $salida->id)->where('tip', 'guia')->first();
    expect($res['propuestas'])->toBeGreaterThan(0)
        ->and($propuesta->rec_pro_id)->toBe($frances->id)
        ->and($propuesta->det['idiomas_faltantes'])->toBe([])
        ->and($propuesta->est)->toBe(PropuestaAsignacion::EST_PENDIENTE)
        // Nunca se aplica sola.
        ->and($salida->fresh()->prf_gui_id)->toBeNull();

    // Un mismo día, un guía no recibe dos salidas a la vez.
    $porDia = PropuestaAsignacion::where('tip', 'guia')->get()->groupBy(fn ($p) => $p->det['dia']);
    foreach ($porDia as $lista) {
        expect($lista->pluck('rec_pro_id')->duplicates())->toBeEmpty();
    }
});

test('RF-24: confirmar aplica con validaciones, ajustar cambia el recurso y rechazar no toca la salida', function () {
    $this->actingAs($this->admin);
    SalidaTour::query()->update(['prf_gui_id' => null]);
    app(OptimizadorService::class)->proponerGuias(today(), today()->addDays(10));

    [$p1, $p2] = PropuestaAsignacion::where('est', 'pendiente')->orderBy('id')->take(2)->get()->all();

    $this->post(route('admin.io.propuestas.aplicar', $p1))->assertSessionHasNoErrors();
    expect($p1->fresh()->est)->toBe(PropuestaAsignacion::EST_APLICADA)
        ->and($p1->salida->fresh()->prf_gui_id)->toBe($p1->rec_pro_id)
        ->and($p1->fresh()->rev_por)->toBe($this->admin->id);

    $this->post(route('admin.io.propuestas.rechazar', $p2))->assertSessionHasNoErrors();
    expect($p2->fresh()->est)->toBe(PropuestaAsignacion::EST_RECHAZADA)
        ->and($p2->salida->fresh()->prf_gui_id)->toBeNull();
});

test('RF-24: un ajuste que viola las reglas no se aplica', function () {
    $this->actingAs($this->admin);
    SalidaTour::query()->update(['prf_gui_id' => null]);
    $limitado = nuevoGuia('Guía Limitado', ['es'], 5, 1); // 1 h/semana: ningún tour cabe
    app(OptimizadorService::class)->proponerGuias(today(), today()->addDays(10));
    $p = PropuestaAsignacion::where('est', 'pendiente')->first();

    $this->post(route('admin.io.propuestas.aplicar', $p), ['recurso' => $limitado->id])->assertSessionHasErrors('prf_gui_id');
    expect($p->fresh()->est)->toBe(PropuestaAsignacion::EST_PENDIENTE);
});

test('RF-23: propone vehículos con asientos suficientes y menor costo', function () {
    SalidaTour::query()->update(['veh_id' => null]);
    $res = app(OptimizadorService::class)->proponerVehiculos(today(), today()->addDays(10));

    expect($res['propuestas'])->toBeGreaterThan(0);
    foreach (PropuestaAsignacion::where('tip', 'vehiculo')->with('salida')->get() as $p) {
        $vehiculo = Vehiculo::find($p->rec_pro_id);
        expect($vehiculo->cap_asi)->toBeGreaterThanOrEqual($p->salida->cap_max)
            ->and($p->det)->toHaveKeys(['km_estimados', 'costo_km', 'combustible']);
    }
});

test('RF-25 a RF-27: EOQ avisa historial insuficiente y alerta la reposición del equipo crítico', function () {
    $this->actingAs($this->admin);
    $tipo = TipoEquipo::where('nom', 'Casco de seguridad')->first();
    $item = $tipo->items()->first();

    // Sin historial: aviso.
    $this->get(route('admin.io.eoq'))->assertSee('Historial insuficiente');

    // 4 meses con alquileres confirmados.
    $tour = Tour::first();
    foreach ([1, 2, 3, 4] as $hace) {
        $salida = SalidaTour::create(['tur_id' => $tour->id, 'fec_sal' => now()->subMonths($hace)->startOfMonth()->addDays(5)->toDateString(), 'hra_sal' => '08:00:00', 'cap_max' => 20, 'est' => 'completada']);
        $r = Reserva::create(['cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id, 'nom_ctc' => 'X', 'cor_ctc' => 'x@example.com', 'tel_ctc' => '70000000', 'cnt_ptc' => 5, 'pre_tot' => 100, 'est' => 'completada']);
        for ($i = 0; $i < 5; $i++) {
            $p = $r->participantes()->create(['nom_cpl' => "P $i", 'tip_doc' => 'PASAPORTE', 'num_doc' => "X{$hace}{$i}", 'eda' => 30]);
            AlquilerEquipoReserva::create(['ptc_rsv_id' => $p->id, 'itm_eqp_id' => $item->id, 'pre' => 15]);
        }
    }
    // Stock casi agotado.
    $tipo->items()->update(['cnt_dis' => 0]);

    $this->post(route('admin.io.eoq.alertas'))->assertSessionHasNoErrors();
    expect(RegistroNotificacion::where('tip', 'alerta_reposicion')->where('cnd->tip_eqp_id', $tipo->id)->exists())->toBeTrue();

    // No se duplica la alerta mientras sigue pendiente.
    $antes = RegistroNotificacion::where('tip', 'alerta_reposicion')->count();
    $this->artisan('io:alertas-eoq')->assertSuccessful();
    expect(RegistroNotificacion::where('tip', 'alerta_reposicion')->count())->toBe($antes);

    $this->get(route('admin.io.eoq'))->assertSee('Revisar reposición');
});

test('el planificador n8n exige token y ejecuta las tareas', function () {
    config(['services.n8n.token' => 'secreto-n8n']);

    $this->postJson('/api/n8n/tareas/metricas-colas')->assertStatus(401);
    $this->postJson('/api/n8n/tareas/metricas-colas', [], ['X-Pipocas-Token' => 'otro'])->assertStatus(401);

    $this->postJson('/api/n8n/tareas/metricas-colas', ['dias' => 1], ['X-Pipocas-Token' => 'secreto-n8n'])
        ->assertOk()->assertJson(['ok' => true, 'comando' => 'io:metricas-colas']);
    expect(ColaMetricaDiaria::count())->toBeGreaterThan(0);

    $this->postJson('/api/n8n/tareas/propuestas-guias', ['dias' => 7], ['X-Pipocas-Token' => 'secreto-n8n'])->assertOk();
    $this->postJson('/api/n8n/tareas/no-existe', [], ['X-Pipocas-Token' => 'secreto-n8n'])->assertNotFound();
});

test('las métricas diarias usan la misma ventana de 30 días del panel y cero llegadas no sustituye demanda', function () {
    $par = ColaParametroTour::where('act', true)->firstOrFail();
    $servicio = app(ColasService::class);
    $servicio->registrarLlegada($par->tur_id, null, null, 3);
    $servicio->generarMetricasDiarias(today());
    $fila = ColaMetricaDiaria::where('tur_id', $par->tur_id)->whereDate('fec', today())->firstOrFail();
    expect((float) $fila->lmb_obs)->toBe(round(1 / 30, 4));
    expect($servicio->lambdaObservado($par->tur_id, today()->subDays(60), today()->subDays(31)))->toBe(0.0);
});

test('la simulación compara con la demanda actual y no cambia la configuración', function () {
    $this->actingAs($this->admin);
    $par = ColaParametroTour::where('act', true)->firstOrFail();
    app(ColasService::class)->registrarLlegada($par->tur_id, null, null, 1);
    $actual = app(ColasService::class)->metricas(1 / 30, (float) $par->mu, $par->c_srv, $par->k_cap);
    $simulado = app(ColasService::class)->metricas(50, (float) $par->mu, 3, 20);
    $respuesta = $this->get(route('admin.io.colas', ['tour' => $par->tur_id, 'lambda' => 50, 'c' => 3, 'k' => 20]));
    $respuesta->assertOk()->assertViewHas('m', fn ($m) => abs($m['rho'] - $actual['rho']) < 1e-8)
        ->assertViewHas('sim', fn ($m) => abs($m['rho'] - $simulado['rho']) < 1e-8)
        ->assertSee('Situación actual')->assertSee('Tu escenario');
    expect($par->fresh()->getAttributes())->toEqual($par->getAttributes());

    // Cambiar solo la demanda también debe producir una comparación.
    $this->get(route('admin.io.colas', ['tour' => $par->tur_id, 'lambda' => 20]))->assertOk()
        ->assertViewHas('sim', fn ($m) => $m !== null);
    $this->get(route('admin.io.colas', ['tour' => $par->tur_id, 'c' => 5, 'k' => 2]))
        ->assertSessionHasErrors('k');
});

test('el cálculo histórico conserva parámetros y ventana aunque cambie la configuración', function () {
    $par = ColaParametroTour::where('act', true)->firstOrFail();
    $service = app(ColasService::class);
    $service->registrarLlegada($par->tur_id, null, null, 2);
    $service->generarMetricasDiarias(today());
    $row = ColaMetricaDiaria::where('tur_id', $par->tur_id)->whereDate('fec', today())->firstOrFail();
    $par->update(['mu' => 9, 'c_srv' => 1]);
    $ctx = $row->fresh()->contexto;
    $m = $service->metricas($ctx['lambda'], $ctx['mu'], $ctx['c'], $ctx['k']);
    expect($row->lq)->toEqualWithDelta($m['lq'], 0.0001)
        ->and($ctx['desde'])->toBe(today()->subDays(29)->toDateString())
        ->and($ctx['hasta'])->toBe(today()->toDateString())
        ->and($ctx['unidad_llegada'])->toBe('solicitud');
});

test('la demanda diaria EOQ incluye ceros y coincide con la fuente anual sin mezclar años', function () {
    $this->travelTo(Carbon::parse('2026-06-15'));
    $item = TipoEquipo::first()->items()->first();
    foreach (['2025-12-31', '2026-01-01', '2026-04-30', '2026-06-15', '2027-01-01'] as $i => $dia) {
        $salida = SalidaTour::create(['tur_id' => Tour::first()->id, 'fec_sal' => $dia, 'hra_sal' => '08:00', 'cap_max' => 10, 'est' => 'completada']);
        $r = Reserva::create(['cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id, 'nom_ctc' => 'QA', 'cor_ctc' => 'qa@example.test', 'tel_ctc' => '70000000', 'cnt_ptc' => 1, 'pre_tot' => 100, 'est' => 'completada']);
        $p = $r->participantes()->create(['nom_cpl' => 'QA Uno', 'tip_doc' => 'PASAPORTE', 'num_doc' => "EOQ{$i}", 'eda' => 30]);
        AlquilerEquipoReserva::create(['ptc_rsv_id' => $p->id, 'itm_eqp_id' => $item->id, 'pre' => 15]);
    }
    $service = app(EoqService::class);
    $diaria = $service->demandaDiaria($item->tip_eqp_id);
    expect($diaria)->toHaveCount(365)->and($diaria['2026-01-02'])->toBe(0)
        ->and(array_sum($diaria))->toBe(3)->and(array_sum($service->demandaMensual($item->tip_eqp_id)))->toBe(3)
        ->and($service->demandaAnualVista($item->tip_eqp_id, 2026))->toBe(3);
    $fila = collect($service->analizar())->firstWhere('tipo.id', $item->tip_eqp_id);
    // Muestra de 365 días: tres días con un uso y 362 días sin usos.
    $sigma = sqrt((3 * (1 - 3 / 365) ** 2 + 362 * (3 / 365) ** 2) / 364);
    expect($fila['eoq']['ss'])->toEqualWithDelta(1.65 * $sigma * sqrt(7), 1e-9);
    $this->travelBack();
});

test('una propuesta aplicada no puede volver a aplicarse ni rechazarse con una página antigua', function () {
    SalidaTour::query()->update(['prf_gui_id' => null]);
    $svc = app(OptimizadorService::class);
    $svc->proponerGuias(today(), today()->addDays(10));
    $p = PropuestaAsignacion::where('est', 'pendiente')->firstOrFail();
    $svc->aplicar($p, $this->admin);
    expect(fn () => $svc->aplicar($p, $this->admin))->toThrow(ValidationException::class);
    expect(fn () => $svc->rechazar($p, $this->admin))->toThrow(ValidationException::class);
    expect($p->fresh()->est)->toBe(PropuestaAsignacion::EST_APLICADA);
});

test('Markov recalibra solo meses completos y las cohortes conservan sus integrantes', function () {
    $this->travelTo(Carbon::parse('2026-07-15'));
    MarkovClienteEstado::query()->delete();
    $svc = app(MarkovService::class);
    for ($i = 0; $i < 7; $i++) {
        $u = User::factory()->rol(User::ROL_TURISTA)->create();
        $svc->registrarCambio($u->id, 'prospecto', fecha: Carbon::parse('2026-03-01'));
        $svc->registrarCambio($u->id, ['reservado', 'completado', 'perdido'][$i % 3], fecha: Carbon::parse('2026-04-01'));
        // Julio aún no terminó; esta fidelización no debe contaminar la matriz mensual.
        if ($i % 3 === 1) {
            $svc->registrarCambio($u->id, 'fidelizado', fecha: now());
        }
    }
    $svc->recalibrar(3);
    $p = $svc->matrizVigente()['P'];
    expect($svc->esEstocastica($p))->toBeTrue()->and($p['completado']['fidelizado'])->toBe(0.0)
        ->and($p['fidelizado']['fidelizado'])->toBe(1.0)->and($p['perdido']['perdido'])->toBe(1.0);
    $svc->construirCohortes();
    $cohort = MarkovCohort::whereDate('per_cap', '2026-03-01')->firstOrFail();
    foreach ([0, 1, 2, 3, 4] as $rel) {
        expect((int) MarkovCohortDistribucion::where('coh_id', $cohort->id)->where('per_rel', $rel)->sum('cnt'))->toBe(7);
    }
    $this->travelBack();
});
