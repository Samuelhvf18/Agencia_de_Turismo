<?php

use App\Actions\Fortify\UpdateUserPassword;
use App\Jobs\NotificacionJob;
use App\Mail\AvisoMail;
use App\Models\BitacoraAuditoria;
use App\Models\FichaMedica;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\Role;
use App\Models\SalidaTour;
use App\Models\User;
use App\Services\CodeGeneratorService;
use App\Services\Notificaciones\NotificacionService;
use App\Services\Pagos\PagoService;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class]);
    $this->admin = User::factory()->rol(User::ROL_ADMIN)->create(['cor' => 'jefe@pipocas.test']);
});

function reservaSimple(?SalidaTour $salida = null, string $est = Reserva::EST_PENDIENTE): Reserva
{
    $salida ??= SalidaTour::orderBy('fec_sal')->first();
    $r = Reserva::create(['cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id, 'nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'ana@example.com',
        'tel_ctc' => '+591 70000000', 'cnt_ptc' => 1, 'pre_tot' => 200, 'est' => $est]);
    $r->participantes()->create(['nom_cpl' => 'Ana Quispe', 'tip_doc' => 'PASAPORTE', 'num_doc' => 'P'.$r->id, 'eda' => 30]);

    return $r;
}

test('NotificacionJob envía el evento al webhook de n8n con el token', function () {
    config(['services.n8n.webhook_notificaciones' => 'https://n8n.test/webhook/pipocas-notificaciones', 'services.n8n.token' => 'tok']);
    Http::fake(['n8n.test/*' => Http::response(['ok' => true])]);
    Queue::fake();

    $registro = app(NotificacionService::class)->aReserva(reservaSimple(), 'recordatorio_salida', ['mensaje' => 'Mañana sales']);
    (new NotificacionJob($registro->id))->handle();

    Http::assertSent(fn ($req) => $req->hasHeader('X-Pipocas-Token', 'tok')
        && $req['tipo'] === 'recordatorio_salida'
        && $req['reserva']['correo'] === 'ana@example.com');
    expect($registro->fresh()->est)->toBe('enviado')->and($registro->fresh()->env_en)->not->toBeNull();
});

test('sin n8n la notificación sale por correo; si falla queda como fallida', function () {
    Mail::fake();
    Queue::fake();
    $registro = app(NotificacionService::class)->aReserva(reservaSimple(), 'salida_reprogramada', ['mensaje' => 'Cambió la hora']);

    (new NotificacionJob($registro->id))->handle();
    Mail::assertSent(AvisoMail::class, fn ($m) => $m->hasTo('ana@example.com'));
    expect($registro->fresh()->est)->toBe('enviado');

    config(['services.n8n.webhook_notificaciones' => 'https://n8n.test/webhook/x']);
    Http::fake(['n8n.test/*' => Http::response('caído', 500)]);
    $otro = app(NotificacionService::class)->aReserva(reservaSimple(), 'recordatorio_salida');
    expect(fn () => (new NotificacionJob($otro->id))->handle())->toThrow(Exception::class);
    expect($otro->fresh()->est)->toBe('fallido')->and($otro->fresh()->cnd)->toHaveKey('error');
});

test('un pago parcial avisa al cliente con el saldo', function () {
    Queue::fake();
    $reserva = reservaSimple();
    app(PagoService::class)->registrarEfectivo($reserva, 100);

    $aviso = RegistroNotificacion::where('rsv_id', $reserva->id)->where('tip', 'pago_recibido')->first();
    expect($aviso)->not->toBeNull()->and((float) $aviso->cnd['saldo'])->toBe(100.0);
});

test('el recordatorio de salida se programa una sola vez', function () {
    Queue::fake();
    $salida = SalidaTour::orderBy('fec_sal')->first();
    $salida->update(['fec_sal' => today()->addDay()]);
    $reserva = reservaSimple($salida, Reserva::EST_CONFIRMADA);

    $this->artisan('reservas:recordatorios')->assertSuccessful();
    $this->artisan('reservas:recordatorios')->assertSuccessful();

    expect(RegistroNotificacion::where('rsv_id', $reserva->id)->where('tip', 'recordatorio_salida')->count())->toBe(1);
});

test('RNF-04: tres intentos fallidos, cuenta nueva y cambio de contraseña alertan al administrador', function () {
    Queue::fake();
    $turista = User::where('cor', 'turista@pipocas.test')->first();

    foreach (range(1, 3) as $i) {
        $this->post('/login', ['login' => $turista->cor, 'password' => 'mala-'.$i]);
    }
    $this->post('/register', ['nom' => 'Lucía', 'pat' => 'Mamani', 'cor' => 'lucia@example.com', 'password' => 'Clave2026', 'password_confirmation' => 'Clave2026']);

    $alertas = RegistroNotificacion::where('tip', 'alerta_seguridad')->where('usr_id', $this->admin->id)->get();
    expect($alertas->pluck('cnd.evento')->all())->toContain('Intentos fallidos de inicio de sesión', 'Cuenta nueva')
        // Las alertas son solo para administradores.
        ->and(RegistroNotificacion::where('tip', 'alerta_seguridad')->where('usr_id', User::where('cor', 'operaciones@pipocas.test')->value('id'))->exists())->toBeFalse();
});

test('RNF-04: el cambio de contraseña desde el perfil alerta al administrador', function () {
    Queue::fake();
    $turista = User::where('cor', 'turista@pipocas.test')->first();
    $this->actingAs($turista);

    app(UpdateUserPassword::class)->update($turista, [
        'current_password' => 'Pipocas2026', 'password' => 'NuevaClave2026', 'password_confirmation' => 'NuevaClave2026',
    ]);

    expect(RegistroNotificacion::where('tip', 'alerta_seguridad')->where('cnd->evento', 'Contraseña cambiada')->exists())->toBeTrue();
});

test('el panel de auditoría filtra, muestra el antes/después y es solo para el administrador', function () {
    Queue::fake();
    $reserva = reservaSimple();
    $reserva->update(['tel_ctc' => '71111111']);
    $registro = BitacoraAuditoria::where('nom_tbl', 'reservas')->where('id_reg', $reserva->id)->where('acc', 'UPDATE')->first();

    $this->actingAs(User::where('cor', 'operaciones@pipocas.test')->first())->get(route('admin.auditoria'))->assertForbidden();

    $this->flushSession();
    $this->actingAs($this->admin);
    $this->get(route('admin.auditoria', ['tabla' => 'reservas', 'acc' => 'UPDATE']))->assertOk()->assertSee('reservas #'.$reserva->id);
    $this->get(route('admin.auditoria', ['tabla' => 'users', 'acc' => 'UPDATE', 'id_reg' => 999999]))->assertOk()->assertSee('Sin registros');
    $this->get(route('admin.auditoria.show', $registro))->assertOk()->assertSee('+591 70000000')->assertSee('71111111');
    $this->get(route('admin.auditoria.notificaciones'))->assertOk();
});

test('el administrador gestiona roles pero no puede desactivarse a sí mismo', function () {
    $this->actingAs($this->admin);
    $turista = User::where('cor', 'turista@pipocas.test')->first();
    $rolOperaciones = Role::where('nom', 'operaciones')->value('id');

    $this->get(route('admin.auditoria.usuarios', ['q' => 'turista']))->assertOk()->assertSee('turista@pipocas.test');
    $this->put(route('admin.auditoria.usuarios.update', $turista), ['rol_id' => $rolOperaciones, 'act' => 1])->assertSessionHasNoErrors();
    expect($turista->fresh()->nombreRol())->toBe('operaciones');

    $this->put(route('admin.auditoria.usuarios.update', $this->admin), ['rol_id' => $this->admin->rol_id, 'act' => 0])->assertSessionHasErrors('usuario');
    expect($this->admin->fresh()->act)->toBeTrue();
});

test('reintentar una notificación fallida la vuelve a encolar', function () {
    Queue::fake();
    $this->actingAs($this->admin);
    $registro = RegistroNotificacion::create(['rsv_id' => reservaSimple()->id, 'can' => 'email', 'tip' => 'recordatorio_salida', 'est' => 'fallido', 'cnd' => ['destino' => 'ana@example.com']]);

    $this->post(route('admin.auditoria.reintentar', $registro))->assertSessionHasNoErrors();

    expect($registro->fresh()->est)->toBe('pendiente');
    Queue::assertPushed(NotificacionJob::class, fn ($j) => $j->registroId === $registro->id);
});

test('ver el manifiesto del check-in audita la lectura de fichas médicas (RNF-05)', function () {
    Queue::fake();
    $salida = SalidaTour::orderBy('fec_sal')->first();
    $reserva = reservaSimple($salida, Reserva::EST_CONFIRMADA);
    FichaMedica::create(['ptc_rsv_id' => $reserva->participantes()->first()->id, 'con_med' => 'Asma', 'rta_fir' => 'firmas/x.png', 'fir_en' => now()]);

    $this->actingAs(User::where('cor', 'operaciones@pipocas.test')->first())->get(route('guias.salida', $salida))->assertOk()->assertSee('Asma');

    expect(BitacoraAuditoria::where('acc', 'READ')->where('nom_tbl', 'fichas_medicas')->exists())->toBeTrue();
});

test('chofer asignado no recibe documentos ni condiciones médicas', function () {
    Queue::fake();
    $salida = SalidaTour::whereNotNull('prf_cho_id')->firstOrFail();
    $reserva = reservaSimple($salida, Reserva::EST_CONFIRMADA);
    FichaMedica::create(['ptc_rsv_id' => $reserva->participantes()->first()->id, 'con_med' => 'Asma privada', 'rta_fir' => 'firmas/x.png', 'fir_en' => now()]);
    $chofer = $salida->chofer->usuario;
    $this->actingAs($chofer)->getJson(route('guias.manifiesto', $salida))->assertOk()
        ->assertJsonPath('pasajeros.0.documento', null)->assertJsonPath('pasajeros.0.alerta_medica', null)
        ->assertDontSee('Asma privada');
});

test('si falla la bitácora se revierte una escritura masiva', function () {
    $reserva = reservaSimple();
    DB::statement("ALTER TABLE bitacora_auditoria ADD CONSTRAINT prueba_fallo CHECK (acc <> 'UPDATE') NOT VALID");
    expect(fn () => DB::transaction(fn () => Reserva::whereKey($reserva->id)->update(['tel_ctc' => '77777777'])))
        ->toThrow(QueryException::class);
    expect($reserva->fresh()->tel_ctc)->toBe('+591 70000000');
});
