<?php

use App\Jobs\EnviarBoletosReserva;
use App\Mail\BoletosReservaMail;
use App\Models\BitacoraAuditoria;
use App\Models\Boleto;
use App\Models\ColaEventoLlegada;
use App\Models\ComprobantePago;
use App\Models\DepartamentoBolivia;
use App\Models\ItemEquipo;
use App\Models\MarkovClienteEstado;
use App\Models\Pago;
use App\Models\Pais;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\User;
use App\Services\CodeGeneratorService;
use App\Services\Io\MarkovService;
use App\Services\Pagos\PagoService;
use App\Services\Pagos\PasarelaSimulada;
use App\Services\Reservas\ReservaService;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\IoColasMarkovSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

const FIRMA_PNG = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class, IoColasMarkovSeeder::class]);
    Storage::fake('local');
    Queue::fake();
});

function tourBajoRiesgo(): Tour
{
    return Tour::where('rta_amg', 'valle-de-la-luna-y-telefericos')->firstOrFail();
}

function tourAltoRiesgo(): Tour
{
    return Tour::where('rta_amg', 'glaciar-huayna-potosi')->firstOrFail();
}

function participante(array $extra = []): array
{
    static $n = 1000000;

    return $extra + [
        'nom_cpl' => 'Ana Quispe', 'tip_doc' => 'CI', 'num_doc' => (string) $n++, 'ci_cmp' => '',
        'ci_dep_id' => DepartamentoBolivia::where('cod', 'LP')->value('id'), 'eda' => 30,
        'pai_id' => Pais::where('cod_iso', 'BO')->value('id'), 'es_ctc_pri' => 0,
    ];
}

/** Recorre los pasos 1 y 2 del wizard y devuelve la salida elegida. */
function hastaParticipantes($test, Tour $tour, int $cantidad = 2): SalidaTour
{
    $salida = $tour->salidas()->orderBy('fec_sal')->firstOrFail();
    $test->post(route('reservas.salida.store', $tour), ['sal_tur_id' => $salida->id, 'cnt_ptc' => $cantidad])
        ->assertRedirect(route('reservas.participantes'));

    $participantes = [];
    for ($i = 0; $i < $cantidad; $i++) {
        $participantes[] = participante(['nom_cpl' => 'Persona '.['Uno', 'Dos', 'Tres', 'Cuatro', 'Cinco'][$i], 'es_ctc_pri' => $i === 0 ? 1 : 0]);
    }
    $test->post(route('reservas.participantes.store'), [
        'nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'Ana@Example.com', 'tel_ctc' => '+591 70000000',
        'participantes' => $participantes,
    ])->assertSessionHasNoErrors()->assertRedirect(route('reservas.equipo'));

    return $salida;
}

function reservaConfirmada($test, ?Tour $tour = null, int $cantidad = 2): Reserva
{
    $tour ??= tourBajoRiesgo();
    hastaParticipantes($test, $tour, $cantidad);
    $test->post(route('reservas.equipo.store'), ['alquileres' => []]);
    $test->post(route('reservas.ficha.store'), ['fichas' => []]);
    $test->post(route('reservas.confirmar'), ['acp_ter' => 1])->assertSessionHasNoErrors();

    return Reserva::latest('id')->firstOrFail();
}

function webhook($test, array $evento)
{
    $cuerpo = json_encode($evento);

    return $test->call('POST', route('webhooks.pasarela'), [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_X_PASARELA_FIRMA' => PasarelaSimulada::firmar($cuerpo),
    ], $cuerpo);
}

test('las vistas del wizard cargan con datos reales', function () {
    $tour = tourBajoRiesgo();
    $this->get(route('reservas.salida', $tour))->assertOk()->assertSee($tour->nom);

    hastaParticipantes($this, $tour);
    $this->get(route('reservas.participantes'))->assertOk()->assertSee('Persona Uno');
    $this->get(route('reservas.equipo'))->assertOk()->assertSee('Chaqueta impermeable');
    $this->get(route('reservas.ficha'))->assertOk();
    $this->get(route('reservas.resumen'))->assertOk()->assertSee('Revisa tu reserva');
});

test('un invitado completa una reserva con equipo y queda pendiente de pago', function () {
    $tour = tourBajoRiesgo();
    $salida = hastaParticipantes($this, $tour);
    $item = ItemEquipo::whereHas('tipo', fn ($q) => $q->where('nom', 'Chaqueta impermeable'))->where('tal', 'M')->firstOrFail();
    $tipoId = $item->tip_eqp_id;
    $stock = $item->cnt_dis;

    $this->post(route('reservas.equipo.store'), ['alquileres' => [0 => [$tipoId => $item->id]]])->assertRedirect(route('reservas.ficha'));
    $this->post(route('reservas.ficha.store'), ['fichas' => []])->assertRedirect(route('reservas.resumen'));
    $respuesta = $this->post(route('reservas.confirmar'), ['acp_ter' => 1]);

    $reserva = Reserva::with('participantes.alquileres')->latest('id')->firstOrFail();
    $respuesta->assertRedirect(route('reservas.confirmada', $reserva));

    expect($reserva->cod)->toMatch('/^RES-[2-9A-HJKMNP-Z]{6}$/')
        ->and($reserva->usr_id)->toBeNull()
        ->and($reserva->cor_ctc)->toBe('ana@example.com')
        ->and($reserva->est)->toBe(Reserva::EST_PENDIENTE)
        ->and((float) $reserva->pre_tot)->toBe((float) $tour->pre_bas * 2 + 25.0)
        ->and($reserva->participantes)->toHaveCount(2)
        ->and($item->fresh()->cnt_dis)->toBe($stock - 1);

    // IO II: la llegada admitida queda registrada para el modelo de colas.
    expect(ColaEventoLlegada::where('rsv_id', $reserva->id)->where('est', 'admitido')->where('cnt_sol', 2)->exists())->toBeTrue();

    $this->get(route('reservas.confirmada', $reserva))->assertOk()->assertSee($reserva->cod);
});

test('no se sobrevende: sin cupos la solicitud se rechaza y se registra como llegada rechazada', function () {
    $tour = tourBajoRiesgo();
    $salida = $tour->salidas()->orderBy('fec_sal')->firstOrFail();
    $salida->update(['cap_max' => 2]);

    $this->post(route('reservas.salida.store', $tour), ['sal_tur_id' => $salida->id, 'cnt_ptc' => 3])
        ->assertSessionHasErrors('cnt_ptc');

    expect(ColaEventoLlegada::where('sal_tur_id', $salida->id)->where('est', 'rechazado')->exists())->toBeTrue();
});

test('si otra reserva ocupa los cupos antes de confirmar, la confirmación se rechaza', function () {
    $tour = tourBajoRiesgo();
    $salida = hastaParticipantes($this, $tour, 2);
    $this->post(route('reservas.equipo.store'), ['alquileres' => []]);
    $this->post(route('reservas.ficha.store'), ['fichas' => []]);

    Reserva::create([
        'cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id, 'nom_ctc' => 'Otro', 'cor_ctc' => 'otro@example.com',
        'tel_ctc' => '70000000', 'cnt_ptc' => $salida->cap_max - 1, 'pre_tot' => 100,
    ]);

    $this->post(route('reservas.confirmar'), ['acp_ter' => 1])
        ->assertRedirect(route('reservas.salida', $tour))
        ->assertSessionHasErrors('sal_tur_id');

    expect(Reserva::where('cor_ctc', 'ana@example.com')->exists())->toBeFalse()
        ->and(ColaEventoLlegada::where('sal_tur_id', $salida->id)->where('est', 'rechazado')->exists())->toBeTrue();
});

test('en un tour de alto riesgo la ficha médica firmada es obligatoria', function () {
    $tour = tourAltoRiesgo();
    expect($tour->esAltoRiesgo())->toBeTrue();
    hastaParticipantes($this, $tour, 1);
    $this->post(route('reservas.equipo.store'), ['alquileres' => []]);

    $this->post(route('reservas.ficha.store'), ['fichas' => []])->assertSessionHasErrors('fichas.0.fir_b64');

    $this->post(route('reservas.ficha.store'), ['fichas' => [0 => [
        'con_med' => 'Ninguna', 'nom_ctc_eme' => 'Luis Quispe', 'tel_ctc_eme' => '71111111',
        'acp_ver' => 1, 'acp_rie' => 1, 'fir_b64' => FIRMA_PNG,
    ]]])->assertSessionHasNoErrors()->assertRedirect(route('reservas.resumen'));

    $this->post(route('reservas.confirmar'), ['acp_ter' => 1])->assertSessionHasNoErrors();

    $reserva = Reserva::with('participantes.fichaMedica')->latest('id')->firstOrFail();
    $ficha = $reserva->participantes->first()->fichaMedica;
    expect($ficha)->not->toBeNull()
        ->and($ficha->rta_fir)->toStartWith("firmas/{$reserva->cod}/")
        ->and($ficha->dir_ip)->not->toBeNull();
    Storage::disk('local')->assertExists($ficha->rta_fir);
});

test('una firma que no es PNG se rechaza', function () {
    hastaParticipantes($this, tourAltoRiesgo(), 1);

    $this->post(route('reservas.ficha.store'), ['fichas' => [0 => [
        'con_med' => 'Ninguna', 'nom_ctc_eme' => 'Luis Quispe', 'tel_ctc_eme' => '71111111',
        'acp_ver' => 1, 'acp_rie' => 1, 'fir_b64' => 'data:image/png;base64,'.base64_encode('<svg/>'),
    ]]])->assertSessionHasErrors('fichas.0.fir_b64');
});

test('la regla CI/pasaporte se valida por participante', function () {
    $tour = tourBajoRiesgo();
    $salida = $tour->salidas()->firstOrFail();
    $this->post(route('reservas.salida.store', $tour), ['sal_tur_id' => $salida->id, 'cnt_ptc' => 2]);

    $this->post(route('reservas.participantes.store'), [
        'nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'ana@example.com', 'tel_ctc' => '70000000',
        'participantes' => [
            participante(['es_ctc_pri' => 1, 'ci_dep_id' => null, 'num_doc' => 'ABC']),
            participante(['tip_doc' => 'PASAPORTE', 'num_doc' => 'XK482910', 'ci_dep_id' => null, 'nom_cpl' => 'Lucas 3']),
        ],
    ])->assertSessionHasErrors(['participantes.0.ci_dep_id', 'participantes.0.num_doc', 'participantes.1.nom_cpl']);
});

test('pago parcial y luego total por webhook: confirma, emite boletos QR y envía el correo', function () {
    $turista = User::where('cor', 'turista@pipocas.test')->first();
    $this->actingAs($turista);
    $reserva = reservaConfirmada($this);
    expect($reserva->usr_id)->toBe($turista->id);

    $total = (float) $reserva->pre_tot;
    $anticipo = round($total * 0.5, 2);

    // Primer intento rechazado.
    $this->post(route('pagos.store', $reserva), ['mtd' => 'qr', 'mto' => $anticipo])->assertRedirect();
    $fallido = Pago::latest('id')->first();
    $this->post(route('pasarela.resolver', $fallido->ref_trx), ['resultado' => 'rechazar'])->assertRedirect(route('reservas.estado', $reserva));
    expect($fallido->fresh()->est)->toBe(Pago::EST_FALLIDO);

    // Anticipo aprobado → parcial.
    $this->post(route('pagos.store', $reserva), ['mtd' => 'tarjeta', 'mto' => $anticipo]);
    $pago1 = Pago::latest('id')->first();
    $this->post(route('pasarela.resolver', $pago1->ref_trx), ['resultado' => 'aprobar']);
    expect($reserva->fresh()->est_pag)->toBe(Reserva::PAG_PARCIAL)
        ->and($reserva->fresh()->est)->toBe(Reserva::EST_PENDIENTE)
        ->and(Boleto::count())->toBe(0);

    // Saldo pagado por el webhook firmado.
    $this->post(route('pagos.store', $reserva), ['mtd' => 'transferencia', 'mto' => round($total - $anticipo, 2)]);
    $pago2 = Pago::latest('id')->first();
    webhook($this, ['ref_trx' => $pago2->ref_trx, 'est' => 'completado', 'mto' => (float) $pago2->mto])->assertOk();

    $reserva->refresh();
    expect($reserva->est_pag)->toBe(Reserva::PAG_PAGADO)
        ->and($reserva->est)->toBe(Reserva::EST_CONFIRMADA)
        ->and($reserva->comprobante->est)->toBe(ComprobantePago::EST_PAGADO)
        ->and((float) $reserva->comprobante->sub + (float) $reserva->comprobante->imp)->toBe($total)
        ->and($reserva->comprobante->pagos)->toHaveCount(3)
        ->and($reserva->boletos()->count())->toBe(2);

    Queue::assertPushed(EnviarBoletosReserva::class, fn ($job) => $job->reservaId === $reserva->id);

    // IO II · Markov: el turista pasa a "reservado".
    expect(MarkovClienteEstado::where('usr_id', $turista->id)->latest('id')->first()->estado->cod)->toBe('reservado');

    // El webhook es idempotente.
    webhook($this, ['ref_trx' => $pago2->ref_trx, 'est' => 'completado', 'mto' => (float) $pago2->mto])->assertOk();
    expect($reserva->boletos()->count())->toBe(2);

    // Estado con QR y descargas en PDF.
    $this->get(route('reservas.estado', $reserva))->assertOk()->assertSee('data:image/svg+xml;base64', false);
    $this->get(route('reservas.boletos', $reserva))->assertOk()->assertHeader('content-type', 'application/pdf');
    $this->get(route('reservas.comprobante', $reserva))->assertOk()->assertHeader('content-type', 'application/pdf');
});

test('el monto del pago debe respetar el mínimo y el saldo', function () {
    $reserva = reservaConfirmada($this);

    $this->post(route('pagos.store', $reserva), ['mtd' => 'qr', 'mto' => 1])->assertSessionHasErrors('mto');
    $this->post(route('pagos.store', $reserva), ['mtd' => 'qr', 'mto' => (float) $reserva->pre_tot + 1])->assertSessionHasErrors('mto');
    expect(Pago::count())->toBe(0);
});

test('el webhook rechaza firmas inválidas', function () {
    $this->call('POST', route('webhooks.pasarela'), [], [], [], [
        'CONTENT_TYPE' => 'application/json', 'HTTP_X_PASARELA_FIRMA' => 'falsa',
    ], json_encode(['ref_trx' => 'SIM-X', 'est' => 'completado']))->assertStatus(401);
});

test('cancelar libera cupos y equipo, cancela boletos y registra el abandono', function () {
    $reserva = reservaConfirmada($this);
    $this->post(route('pagos.store', $reserva), ['mtd' => 'qr', 'mto' => (float) $reserva->pre_tot]);
    $pago = Pago::latest('id')->first();
    $this->post(route('pasarela.resolver', $pago->ref_trx), ['resultado' => 'aprobar']);
    expect($reserva->boletos()->count())->toBe(2);

    $this->post(route('reservas.cancelar', $reserva))->assertRedirect(route('reservas.estado', $reserva));

    $reserva->refresh();
    expect($reserva->est)->toBe(Reserva::EST_CANCELADA)
        ->and($reserva->est_pag)->toBe(Reserva::PAG_REEMBOLSADO)
        ->and($reserva->boletos()->where('boletos.est', Boleto::EST_CANCELADO)->count())->toBe(2)
        ->and(ColaEventoLlegada::where('rsv_id', $reserva->id)->where('est', 'cancelado')->exists())->toBeTrue()
        ->and($reserva->salida->cuposOcupados())->toBe(0);
});

test('solo quien tiene el código y el correo puede ver la reserva', function () {
    $reserva = reservaConfirmada($this);

    // Otra sesión (otro navegador) sin acceso.
    $this->flushSession();
    $this->get(route('reservas.estado', $reserva))->assertForbidden();

    $this->post(route('reservas.consultar.buscar'), ['cod' => strtolower($reserva->cod), 'cor_ctc' => 'otro@example.com'])
        ->assertSessionHasErrors('cod');

    $this->post(route('reservas.consultar.buscar'), ['cod' => strtolower($reserva->cod), 'cor_ctc' => 'ANA@example.com'])
        ->assertRedirect(route('reservas.estado', $reserva));
    $this->get(route('reservas.estado', $reserva))->assertOk()->assertSee($reserva->cod);
});

test('las reservas impagas vencen y liberan su cupo', function () {
    $reserva = reservaConfirmada($this);
    Reserva::whereKey($reserva->id)->update(['cre_en' => now()->subHours(25)]);

    $this->artisan('reservas:expirar')->assertSuccessful();

    expect($reserva->fresh()->est)->toBe(Reserva::EST_CANCELADA);
});

test('los códigos generados son únicos y con el formato esperado', function () {
    $codigos = collect(range(1, 200))->map(fn () => CodeGeneratorService::generate('RES'));

    expect($codigos->unique())->toHaveCount(200)
        ->and(CodeGeneratorService::generate('TUR'))->toMatch('/^TUR-\d{4}-[2-9A-HJKMNP-Z]{5}$/');
});

test('el personal ve la reserva, cobra en efectivo y la lectura de datos sensibles queda auditada', function () {
    $reserva = reservaConfirmada($this);
    $operaciones = User::where('cor', 'operaciones@pipocas.test')->first();
    $this->actingAs($operaciones);

    $this->get(route('admin.reservas'))->assertOk()->assertSee($reserva->cod);
    $this->get(route('admin.reservas.show', $reserva))->assertOk()->assertSee('Persona Uno');
    expect(BitacoraAuditoria::where('acc', 'READ')->where('nom_tbl', 'participantes_reserva')->where('usr_id', $operaciones->id)->count())->toBe(2);

    $this->post(route('admin.finanzas.efectivo', $reserva), ['mto' => (float) $reserva->pre_tot])->assertSessionHasNoErrors();
    expect($reserva->fresh()->est)->toBe(Reserva::EST_CONFIRMADA)
        ->and($reserva->comprobante->pagos()->where('mtd', 'efectivo')->where('est', 'completado')->exists())->toBeTrue();

    $this->get(route('admin.finanzas'))->assertForbidden();
    $this->flushSession();
    $this->actingAs(User::factory()->rol(User::ROL_ADMIN)->create())->get(route('admin.finanzas'))->assertOk()->assertSee($reserva->comprobante->cod);
});

test('solo el administrador anula comprobantes; la anulación cancela sin borrar', function () {
    $reserva = reservaConfirmada($this);
    $this->post(route('pagos.store', $reserva), ['mtd' => 'qr', 'mto' => (float) $reserva->pre_tot]);
    $this->post(route('pasarela.resolver', Pago::latest('id')->first()->ref_trx), ['resultado' => 'aprobar']);
    $comprobante = $reserva->fresh()->comprobante;

    $this->actingAs(User::where('cor', 'operaciones@pipocas.test')->first())
        ->post(route('admin.finanzas.anular', $comprobante), ['motivo' => 'Prueba'])->assertForbidden();

    // Otra sesión: AuthenticateSession cierra la sesión si cambia el usuario en la misma.
    $this->flushSession();
    $this->actingAs(User::factory()->rol(User::ROL_ADMIN)->create() ?? User::factory()->rol(User::ROL_ADMIN)->create())
        ->post(route('admin.finanzas.anular', $comprobante), ['motivo' => 'Error de cobro'])->assertSessionHas('estado');

    expect($comprobante->fresh()->est)->toBe(ComprobantePago::EST_ANULADO)
        ->and($reserva->fresh()->est)->toBe(Reserva::EST_CANCELADA)
        ->and($reserva->fresh()->est_pag)->toBe(Reserva::PAG_REEMBOLSADO)
        ->and(ComprobantePago::whereKey($comprobante->id)->exists())->toBeTrue();
});

test('el correo de confirmación adjunta boletos y comprobante y queda registrado', function () {
    Mail::fake();
    $reserva = reservaConfirmada($this);
    $this->post(route('pagos.store', $reserva), ['mtd' => 'qr', 'mto' => (float) $reserva->pre_tot]);
    $this->post(route('pasarela.resolver', Pago::latest('id')->first()->ref_trx), ['resultado' => 'aprobar']);

    (new EnviarBoletosReserva($reserva->id))->handle();

    Mail::assertSent(BoletosReservaMail::class, function ($mail) use ($reserva) {
        $nombres = collect($mail->attachments())->map(fn ($a) => $a->as)->all();

        return $mail->hasTo($reserva->cor_ctc)
            && in_array("boletos-{$reserva->cod}.pdf", $nombres, true)
            && count($nombres) === 2;
    });
    expect(RegistroNotificacion::where('rsv_id', $reserva->id)->where('est', 'enviado')->where('can', 'email')->exists())->toBeTrue();
});

test('completar la reserva devuelve el equipo y lleva al turista a completado', function () {
    $turista = User::where('cor', 'turista@pipocas.test')->first();
    $this->actingAs($turista);
    $reserva = reservaConfirmada($this);
    $this->post(route('pagos.store', $reserva), ['mtd' => 'qr', 'mto' => (float) $reserva->pre_tot]);
    $this->post(route('pasarela.resolver', Pago::latest('id')->first()->ref_trx), ['resultado' => 'aprobar']);

    app(ReservaService::class)->completar($reserva->fresh());

    expect($reserva->fresh()->est)->toBe(Reserva::EST_COMPLETADA)
        ->and(MarkovClienteEstado::where('usr_id', $turista->id)->latest('id')->first()->estado->cod)->toBe('completado');
});

test('no admite dos intentos activos ni efectivo mientras exista uno pendiente', function () {
    $reserva = reservaConfirmada($this);
    $servicio = app(PagoService::class);
    $servicio->iniciar($reserva, 'qr', (float) $reserva->pre_tot);
    expect(fn () => $servicio->iniciar($reserva, 'qr', (float) $reserva->pre_tot))
        ->toThrow(ValidationException::class);
    expect(fn () => $servicio->registrarEfectivo($reserva, 10))
        ->toThrow(ValidationException::class);
});

test('un webhook parcial repetido no vuelve a notificar ni sumar el pago', function () {
    $reserva = reservaConfirmada($this);
    $servicio = app(PagoService::class);
    $pago = $servicio->iniciar($reserva, 'qr', round($reserva->pre_tot * .5, 2));
    $evento = ['ref_trx' => $pago->ref_trx, 'est' => Pago::EST_COMPLETADO];
    $servicio->procesarNotificacion($evento);
    $servicio->procesarNotificacion($evento);
    expect(RegistroNotificacion::where('rsv_id', $reserva->id)->where('tip', 'pago_recibido')->count())->toBe(1);
    expect($servicio->montoPagado($reserva->fresh()))->toBe((float) $pago->mto);
});

test('el callback de un intento heredado no permite sobrepago ni pago tras cancelar', function () {
    $reserva = reservaConfirmada($this);
    $servicio = app(PagoService::class);
    $pago = $servicio->iniciar($reserva, 'qr', (float) $reserva->pre_tot);
    $otro = $pago->comprobante->pagos()->create(['mtd' => 'qr', 'mto' => $reserva->pre_tot, 'ref_trx' => 'SIM-LEGACY', 'est' => Pago::EST_PENDIENTE]);
    $servicio->procesarNotificacion(['ref_trx' => $pago->ref_trx, 'est' => Pago::EST_COMPLETADO]);
    $servicio->procesarNotificacion(['ref_trx' => $otro->ref_trx, 'est' => Pago::EST_COMPLETADO]);
    expect($otro->fresh()->est)->toBe(Pago::EST_FALLIDO);
    expect($servicio->montoPagado($reserva->fresh()))->toBe((float) $reserva->pre_tot);
    Queue::assertPushed(EnviarBoletosReserva::class, 1);
});

test('mis reservas solo lista las propias y rechaza acceso directo de otro titular', function () {
    $a = User::factory()->rol(User::ROL_TURISTA)->create();
    $b = User::factory()->rol(User::ROL_TURISTA)->create();
    $reserva = reservaConfirmada($this);
    $reserva->update(['usr_id' => $a->id]);
    $this->flushSession();
    $this->actingAs($a)->get(route('reservas.historial'))->assertOk()->assertSee($reserva->cod);
    $this->actingAs($b)->get(route('reservas.historial'))->assertOk()->assertDontSee($reserva->cod);
    $this->get(route('reservas.estado', $reserva))->assertForbidden();
});

test('cancelar audita cada boleto incluso cuando se modifica masivamente', function () {
    $reserva = reservaConfirmada($this);
    app(PagoService::class)->registrarEfectivo($reserva, (float) $reserva->pre_tot);
    $ids = $reserva->boletos()->pluck('boletos.id');
    app(ReservaService::class)->cancelar($reserva);
    foreach ($ids as $id) {
        expect(BitacoraAuditoria::where('nom_tbl', 'boletos')->where('id_reg', $id)
            ->where('acc', 'UPDATE')->where('val_nvo->est', 'cancelado')->count())->toBe(1);
    }
});

test('Markov ignora cancelaciones y reservas impagas aunque la salida haya terminado', function () {
    $turista = User::factory()->rol(User::ROL_TURISTA)->create();
    $reserva = reservaConfirmada($this);
    $reserva->update(['usr_id' => $turista->id, 'est' => Reserva::EST_CANCELADA]);
    $reserva->salida->update(['est' => SalidaTour::EST_COMPLETADA]);
    $servicio = app(MarkovService::class);
    $servicio->sincronizarDesdeReservas();
    expect($servicio->estadosAl(now())[$turista->id])->toBe('prospecto');
    $reserva->update(['est' => Reserva::EST_PENDIENTE]);
    $reserva->salida->update(['est' => SalidaTour::EST_PROGRAMADA, 'fec_sal' => today()->addDay()]);
    $servicio->sincronizarDesdeReservas();
    expect($servicio->estadosAl(now())[$turista->id])->toBe('prospecto');
});

test('el wizard conserva inglés entre pasos', function () {
    $this->get(route('reservas.salida', tourBajoRiesgo()).'?lang=en')->assertOk()->assertSee('When would you like to go?');
    hastaParticipantes($this, tourBajoRiesgo());
    $this->get(route('reservas.participantes'))->assertOk()->assertSee('Who is travelling?')->assertSee('Contact details');
    $this->get(route('reservas.equipo'))->assertOk()->assertSee('Do you need equipment?');
});
