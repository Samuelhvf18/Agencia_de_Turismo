<?php

use App\Jobs\NotificacionJob;
use App\Models\BitacoraViajeVehiculo;
use App\Models\Boleto;
use App\Models\Idioma;
use App\Models\ItemEquipo;
use App\Models\PerfilChofer;
use App\Models\PerfilGuia;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\TipoEquipo;
use App\Models\Tour;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\CodeGeneratorService;
use App\Services\Pagos\PagoService;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\IoColasMarkovSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class, IoColasMarkovSeeder::class]);
    Queue::fake();
    $this->operaciones = User::where('cor', 'operaciones@pipocas.test')->first();
    $this->guia = PerfilGuia::first();
    $this->chofer = PerfilChofer::first();
    $this->tour = Tour::where('rta_amg', 'valle-de-la-luna-y-telefericos')->first(); // 4 h
});

/** Reserva pagada en una salida, creada directamente (sin el wizard). */
function reservaPagada(SalidaTour $salida, int $pax = 2): Reserva
{
    $reserva = Reserva::create([
        'cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id,
        'nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'ana@example.com', 'tel_ctc' => '70000000',
        'cnt_ptc' => $pax, 'pre_tot' => 100 * $pax, 'est' => Reserva::EST_PENDIENTE,
    ]);
    for ($i = 0; $i < $pax; $i++) {
        $reserva->participantes()->create(['nom_cpl' => "Pasajero $i", 'tip_doc' => 'PASAPORTE', 'num_doc' => "P{$reserva->id}{$i}", 'eda' => 30]);
    }
    app(PagoService::class)->registrarEfectivo($reserva, (float) $reserva->pre_tot);

    return $reserva->refresh();
}

function datosSalida(array $extra = []): array
{
    return $extra + [
        'tur_id' => test()->tour->id, 'fec_sal' => today()->addDays(40)->toDateString(), 'hra_sal' => '09:00',
        'cap_max' => 10, 'prf_gui_id' => test()->guia->id, 'prf_cho_id' => test()->chofer->id, 'veh_id' => Vehiculo::where('cap_asi', '>=', 12)->first()->id,
    ];
}

test('las pantallas de operación cargan', function () {
    $this->actingAs($this->operaciones);
    $salida = SalidaTour::first();

    $this->get(route('admin.operaciones'))->assertOk()->assertSee('Programar salida');
    $this->get(route('admin.salidas.edit', $salida))->assertOk();
    $this->get(route('admin.personal'))->assertOk()->assertSee('Guía Demo');
    $this->get(route('admin.personal.edit', ['guia', $this->guia->id]))->assertOk();
    $this->get(route('admin.transporte'))->assertOk()->assertSee('2345-LPZ');
    $this->get(route('admin.inventario'))->assertOk()->assertSee('Casco de seguridad');
});

test('programa una salida válida con guía, chofer y vehículo', function () {
    $this->actingAs($this->operaciones)->post(route('admin.salidas.store'), datosSalida())->assertSessionHasNoErrors();

    expect(SalidaTour::where('fec_sal', today()->addDays(40)->toDateString())->where('prf_gui_id', $this->guia->id)->exists())->toBeTrue();
});

test('rechaza el choque de horario del guía, del chofer y del vehículo', function () {
    $this->actingAs($this->operaciones)->post(route('admin.salidas.store'), datosSalida());

    // Mismo día, 2 h después: el tour de 4 h aún no terminó.
    $this->post(route('admin.salidas.store'), datosSalida(['hra_sal' => '11:00']))
        ->assertSessionHasErrors(['prf_gui_id', 'prf_cho_id', 'veh_id']);

    // Al terminar el primero ya no hay choque.
    $this->post(route('admin.salidas.store'), datosSalida(['hra_sal' => '13:30']))->assertSessionHasNoErrors();
});

test('respeta el límite semanal de horas del guía', function () {
    $this->guia->update(['lim_hrs_sem' => 6]);
    $this->actingAs($this->operaciones)->post(route('admin.salidas.store'), datosSalida(['prf_cho_id' => null, 'veh_id' => null]))->assertSessionHasNoErrors();

    $this->post(route('admin.salidas.store'), datosSalida(['fec_sal' => today()->addDays(40)->toDateString(), 'hra_sal' => '15:00', 'prf_cho_id' => null, 'veh_id' => null]))
        ->assertSessionHasErrors('prf_gui_id');
});

test('valida licencia vigente, vehículo activo y asientos suficientes', function () {
    $this->chofer->update(['fec_ven_lic' => today()->addDays(10)]);
    $vehiculo = Vehiculo::first();
    $vehiculo->update(['est' => Vehiculo::EST_MANTENIMIENTO]);
    $chico = Vehiculo::create(['pla' => '9999-ZZZ', 'tip' => 'van', 'cap_asi' => 4, 'est' => 'activo']);

    $this->actingAs($this->operaciones)
        ->post(route('admin.salidas.store'), datosSalida(['prf_gui_id' => null, 'veh_id' => $vehiculo->id]))
        ->assertSessionHasErrors(['prf_cho_id', 'veh_id']);

    $this->post(route('admin.salidas.store'), datosSalida(['prf_gui_id' => null, 'prf_cho_id' => null, 'veh_id' => $chico->id]))
        ->assertSessionHasErrors('veh_id');
});

test('no se desactiva personal ni vehículos con salidas futuras', function () {
    $this->actingAs($this->operaciones);

    $this->post(route('admin.personal.estado', ['guia', $this->guia->id]))->assertSessionHasErrors('personal');
    expect($this->guia->fresh()->act)->toBeTrue();

    $vehiculo = SalidaTour::whereNotNull('veh_id')->first()->vehiculo;
    $this->put(route('admin.transporte.update', $vehiculo), $vehiculo->only(['pla', 'mar', 'mdl', 'tip', 'cap_asi', 'tip_cmb']) + ['est' => 'mantenimiento'])
        ->assertSessionHasErrors('flota');
});

test('registra un guía nuevo con sus idiomas y un chofer con licencia', function () {
    $this->actingAs($this->operaciones);
    $idiomas = Idioma::whereIn('cod_iso', ['es', 'fr'])->pluck('id')->all();

    $this->post(route('admin.personal.store', 'guia'), [
        'nom' => 'Rubén Choque', 'cor' => 'ruben@pipocas.test', 'password' => 'Clave2026', 'ani_exp' => 8, 'lim_hrs_sem' => 36, 'idiomas' => $idiomas,
    ])->assertSessionHasNoErrors();
    $this->post(route('admin.personal.store', 'chofer'), [
        'nom' => 'Jorge Quispe', 'cor' => 'jorge@pipocas.test', 'password' => 'Clave2026', 'num_lic' => 'LP-998877', 'tip_lic' => 'C', 'fec_ven_lic' => today()->addYear()->toDateString(),
    ])->assertSessionHasNoErrors();

    $ruben = User::where('cor', 'ruben@pipocas.test')->first();
    expect($ruben->nombreRol())->toBe('guia')
        ->and($ruben->perfilGuia->idiomas)->toHaveCount(2)
        ->and(User::where('cor', 'jorge@pipocas.test')->first()->perfilChofer->num_lic)->toBe('LP-998877');
});

test('el inventario por talla no permite quitar unidades alquiladas', function () {
    $this->actingAs($this->operaciones);
    $item = ItemEquipo::where('tal', 'M')->first();
    $item->update(['cnt_tot' => 10, 'cnt_dis' => 7]); // 3 alquiladas

    $this->post(route('admin.inventario.item', $item->tipo), ['tal' => 'M', 'cnt_tot' => 2])->assertSessionHasErrors('inventario');
    $this->post(route('admin.inventario.item', $item->tipo), ['tal' => 'M', 'cnt_tot' => 12])->assertSessionHasNoErrors();

    expect($item->fresh()->cnt_dis)->toBe(9);
});

test('reprogramar avisa a cada reserva y cancelar la salida cancela sus reservas', function () {
    $this->actingAs($this->operaciones);
    $salida = SalidaTour::where('tur_id', $this->tour->id)->first();
    $reserva = reservaPagada($salida);
    expect($reserva->est)->toBe(Reserva::EST_CONFIRMADA);

    $this->post(route('admin.salidas.reprogramar', $salida), ['fec_sal' => today()->addDays(45)->toDateString(), 'hra_sal' => '07:30', 'motivo' => 'Clima'])
        ->assertSessionHasNoErrors();
    expect($salida->fresh()->hra_sal)->toBe('07:30:00')
        ->and(RegistroNotificacion::where('rsv_id', $reserva->id)->where('tip', 'salida_reprogramada')->exists())->toBeTrue();
    Queue::assertPushed(NotificacionJob::class);

    $this->post(route('admin.salidas.cancelar', $salida), ['motivo' => 'Bloqueo de caminos'])->assertSessionHasNoErrors();
    expect($salida->fresh()->est)->toBe(SalidaTour::EST_CANCELADA)
        ->and($reserva->fresh()->est)->toBe(Reserva::EST_CANCELADA)
        ->and($reserva->fresh()->est_pag)->toBe(Reserva::PAG_REEMBOLSADO)
        ->and(RegistroNotificacion::where('rsv_id', $reserva->id)->where('tip', 'salida_cancelada')->exists())->toBeTrue();
});

test('el guía hace el check-in por QR, inicia y finaliza el recorrido', function () {
    $salida = SalidaTour::where('tur_id', $this->tour->id)->orderBy('fec_sal')->orderBy('id')->first();
    $salida->update(['prf_gui_id' => $this->guia->id, 'veh_id' => Vehiculo::where('cap_asi', '>=', $salida->cap_max)->orderBy('id')->value('id')]);
    $reserva = reservaPagada($salida, 2);
    $otra = reservaPagada(SalidaTour::where('tur_id', $this->tour->id)->where('id', '<>', $salida->id)->first(), 1);
    $boletos = $reserva->boletos()->pluck('boletos.cod');
    $tipo = TipoEquipo::first();
    $item = $tipo->items()->first();

    $guiaUsuario = $this->guia->usuario;
    $this->actingAs($guiaUsuario);
    $this->get(route('guias.checkin'))->assertOk()->assertSee($this->tour->nom);
    $this->get(route('guias.salida', $salida))->assertOk()->assertSee($boletos[0]);

    $this->postJson(route('guias.validar', $salida), ['cod' => strtolower($boletos[0])])->assertOk()->assertJson(['ok' => true]);
    $this->postJson(route('guias.validar', $salida), ['cod' => $boletos[0]])->assertOk()->assertJson(['repetido' => true]);
    $this->postJson(route('guias.validar', $salida), ['cod' => $otra->boletos()->first()->cod])->assertStatus(422)->assertJson(['ok' => false]);

    // Escaneo hecho sin conexión y sincronizado después.
    $this->postJson(route('guias.sincronizar', $salida), ['escaneos' => [['cod' => $boletos[1], 'vld_en' => now()->subMinutes(5)->toIso8601String()]]])
        ->assertOk()->assertJsonPath('resultados.0.ok', true);
    expect(Boleto::whereIn('cod', $boletos)->where('est', Boleto::EST_VALIDADO)->count())->toBe(2)
        ->and(Boleto::where('cod', $boletos[0])->first()->vld_por)->toBe($guiaUsuario->id);

    $this->post(route('guias.iniciar', $salida))->assertSessionHasNoErrors();
    expect($salida->fresh()->est)->toBe(SalidaTour::EST_EN_CURSO)->and($salida->fresh()->hra_ini_rea)->not->toBeNull();

    $this->post(route('guias.finalizar', $salida), ['dst_km' => 42.5, 'cos_cmb' => 120])->assertRedirect(route('guias.checkin'));
    expect($salida->fresh()->est)->toBe(SalidaTour::EST_COMPLETADA)
        ->and($reserva->fresh()->est)->toBe(Reserva::EST_COMPLETADA)
        ->and(BitacoraViajeVehiculo::where('sal_tur_id', $salida->id)->value('dst_km'))->toBe('42.50');
});

test('un guía no puede operar salidas que no le asignaron', function () {
    $otroGuia = User::factory()->rol(User::ROL_GUIA)->create();
    $salida = SalidaTour::first();

    $this->actingAs($otroGuia)->get(route('guias.salida', $salida))->assertForbidden();
    $this->postJson(route('guias.validar', $salida), ['cod' => 'BOL-X'])->assertForbidden();
});

test('retirar una asignación revoca HTML JSON validación y sincronización', function (string $rol) {
    $salida = SalidaTour::where('tur_id', $this->tour->id)->firstOrFail();
    $salida->update(['prf_gui_id' => $this->guia->id, 'prf_cho_id' => $this->chofer->id]);
    $reserva = reservaPagada($salida, 1);
    $codigo = $reserva->boletos()->first()->cod;
    $perfil = $rol === 'guia' ? $this->guia : $this->chofer;
    $campo = $rol === 'guia' ? 'prf_gui_id' : 'prf_cho_id';
    $this->actingAs($perfil->usuario, 'web')->getJson(route('guias.manifiesto', $salida))->assertOk();
    $salida->update([$campo => null]);
    $this->get(route('guias.salida', $salida))->assertForbidden();
    $this->getJson(route('guias.manifiesto', $salida))->assertForbidden();
    $this->postJson(route('guias.validar', $salida), ['cod' => $codigo])->assertForbidden();
    $this->postJson(route('guias.sincronizar', $salida), ['escaneos' => [['cod' => $codigo, 'vld_en' => now()->toIso8601String()]]])->assertForbidden();
})->with(['guia', 'chofer']);
