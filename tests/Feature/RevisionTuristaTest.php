<?php

use App\Models\DepartamentoBolivia;
use App\Models\Pais;
use App\Models\Reserva;
use App\Models\Tour;
use App\Models\User;
use App\Services\Notificaciones\CodigoCorreo;
use App\Services\Operaciones\SalidaService;
use App\Services\Reservas\BoletoService;
use App\Services\Reservas\ReservaService;
use App\Services\Reservas\SeguridadParticipantes;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\IoColasMarkovSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class, IoColasMarkovSeeder::class]);
    Queue::fake();
    Mail::fake();
});

function salidaRevision(bool $alto = false)
{
    return Tour::where('rta_amg', $alto ? 'glaciar-huayna-potosi' : 'valle-de-la-luna-y-telefericos')->firstOrFail()->salidas()->orderBy('fec_sal')->firstOrFail();
}
function adultoRevision(array $extra = []): array
{
    return $extra + ['nom_cpl' => 'Ana Quispe', 'tip_doc' => 'CI', 'num_doc' => '12345678', 'ci_cmp' => '',
        'ci_dep_id' => DepartamentoBolivia::where('cod', 'LP')->value('id'), 'fecha_nacimiento' => today()->subYears(30)->toDateString(),
        'pai_id' => Pais::where('cod_iso', 'BO')->value('id'), 'es_ctc_pri' => 1];
}
function menorRevision(array $extra = []): array
{
    return adultoRevision($extra + ['nom_cpl' => 'Luis Quispe', 'num_doc' => '87654321', 'es_ctc_pri' => 0,
        'fecha_nacimiento' => today()->subYears(12)->toDateString(), 'acompanante_indice' => 0,
        'responsable_nombre' => 'Ana Quispe', 'responsable_documento' => '12345678', 'vinculo' => 'un_progenitor', 'consentimiento_menor' => 1]);
}
function borradorRevision($salida, array $participantes): array
{
    return ['sal_tur_id' => $salida->id, 'contacto' => ['nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'ana@example.test', 'tel_ctc' => '+59170000000'], 'participantes' => $participantes];
}
function postParticipantesRevision($test, $salida, array $participantes, array $extra = [])
{
    $test->post(route('reservas.salida.store', $salida->tour), ['sal_tur_id' => $salida->id, 'cnt_ptc' => count($participantes)])->assertRedirect();

    return $test->post(route('reservas.participantes.store'), $extra + ['nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'ana@example.test', 'prefijo_ctc' => '+591', 'telefono_nacional' => '70000000', 'participantes' => $participantes]);
}

test('alto riesgo rechaza menores en formulario y servicio aunque falsifiquen la edad', function () {
    $s = salidaRevision(true);
    postParticipantesRevision($this, $s, [adultoRevision(), menorRevision(['eda' => 40])])->assertSessionHasErrors('participantes.1.fecha_nacimiento');
    expect(fn () => app(ReservaService::class)->confirmar(borradorRevision($s, [adultoRevision(), menorRevision(['eda' => 40])]), null))->toThrow(ValidationException::class);
    expect(Reserva::count())->toBe(0);
});
test('se calcula la mayoría de edad para la fecha exacta de salida', function () {
    $s = salidaRevision(true);
    $p = adultoRevision(['fecha_nacimiento' => $s->fec_sal->copy()->subYears(18)->toDateString()]);
    $normal = app(SeguridadParticipantes::class)->validar($s, [$p]);
    expect($normal[0]['eda'])->toBe(18);
    $p['fecha_nacimiento'] = $s->fec_sal->copy()->subYears(18)->addDay()->toDateString();
    expect(fn () => app(SeguridadParticipantes::class)->validar($s, [$p]))->toThrow(ValidationException::class);
});
test('un nacimiento futuro o inválido y una edad manual sin nacimiento se rechazan', function () {
    $s = salidaRevision();
    foreach ([today()->addDay()->toDateString(), '2020-02-30', '', '1800-01-01'] as $fecha) {
        expect(fn () => app(SeguridadParticipantes::class)->validar($s, [adultoRevision(['fecha_nacimiento' => $fecha, 'eda' => 30])]))->toThrow(ValidationException::class);
    }
});
test('la edad mínima del tour y la prohibición de menores se aplican en servidor', function () {
    $s = salidaRevision();
    $s->tour->update(['edad_minima' => 16]);
    expect(fn () => app(SeguridadParticipantes::class)->validar($s, [adultoRevision(), menorRevision()]))->toThrow(ValidationException::class);
    $s->tour->update(['edad_minima' => 0, 'admite_menores' => false]);
    expect(fn () => app(SeguridadParticipantes::class)->validar($s, [adultoRevision(), menorRevision()]))->toThrow(ValidationException::class);
});
test('el menor requiere adulto distinto y consentimiento del representante', function () {
    $s = salidaRevision();
    foreach ([['acompanante_indice' => 1], ['acompanante_indice' => 9], ['consentimiento_menor' => 0], ['responsable_documento' => '???']] as $cambio) {
        expect(fn () => app(SeguridadParticipantes::class)->validar($s, [adultoRevision(), menorRevision($cambio)]))->toThrow(ValidationException::class);
    }
    expect(fn () => app(SeguridadParticipantes::class)->validar($s, [menorRevision(['acompanante_indice' => 1]), menorRevision(['acompanante_indice' => 0])]))->toThrow(ValidationException::class);
});
test('reserva familiar guarda nacimiento y autorización y retiene el boleto del menor hasta revisión', function () {
    $s = salidaRevision();
    $r = app(ReservaService::class)->confirmar(borradorRevision($s, [adultoRevision(), menorRevision()]), null);
    $r->update(['est' => Reserva::EST_CONFIRMADA, 'est_pag' => Reserva::PAG_PAGADO]);
    $boletos = app(BoletoService::class)->emitir($r);
    expect($r->idioma)->toBe('es')->and($boletos)->toHaveCount(1);
    $menor = $r->participantes()->where('nom_cpl', 'Luis Quispe')->firstOrFail();
    expect($menor->autorizacion_menor['version'])->toBe(SeguridadParticipantes::VERSION)->and($menor->acompanante->nom_cpl)->toBe('Ana Quispe');
    $operador = User::whereHas('rol', fn ($q) => $q->where('nom', 'operaciones'))->firstOrFail();
    $this->actingAs($operador)->post(route('admin.reservas.menor.verificar', [$r, $menor]), ['documentos_originales' => 1])->assertSessionHasNoErrors();
    expect($menor->fresh()->boleto)->not->toBeNull()->and(app(SeguridadParticipantes::class)->bloqueo($menor->fresh(), $s))->toBeNull();
    $s->update(['fec_sal' => $s->fec_sal->copy()->addDay()]);
    expect(app(SeguridadParticipantes::class)->bloqueo($menor->fresh(), $s))->not->toBeNull();
});
test('turista no puede aprobar documentos de menores', function () {
    $r = app(ReservaService::class)->confirmar(borradorRevision(salidaRevision(), [adultoRevision(), menorRevision()]), null);
    $menor = $r->participantes()->where('nom_cpl', 'Luis Quispe')->firstOrFail();
    $turista = User::whereHas('rol', fn ($q) => $q->where('nom', 'turista'))->firstOrFail();
    $this->actingAs($turista)->post(route('admin.reservas.menor.verificar', [$r, $menor]), ['documentos_originales' => 1])->assertForbidden();
});
test('contacto autenticado procede de la cuenta aunque alteren el formulario', function () {
    $u = User::whereHas('rol', fn ($q) => $q->where('nom', 'turista'))->firstOrFail();
    $this->actingAs($u);
    postParticipantesRevision($this, salidaRevision(), [adultoRevision()], ['nom_ctc' => 'Otro Nombre', 'cor_ctc' => 'otro@example.test'])->assertSessionHasNoErrors();
    expect(session('reserva.borrador.contacto.nom_ctc'))->toBe($u->nom)->and(session('reserva.borrador.contacto.cor_ctc'))->toBe($u->cor);
});
test('teléfono y nombre rechazan caracteres ajenos a su campo', function () {
    postParticipantesRevision($this, salidaRevision(), [adultoRevision(['nom_cpl' => 'Ana 123!'])], ['telefono_nacional' => '7abc'])->assertSessionHasErrors(['telefono_nacional', 'participantes.0.nom_cpl']);
});
test('registro normaliza correo y rechaza nombre con signos', function () {
    $this->post('/register', ['nom' => 'Ana!', 'pat' => 'Quispe', 'cor' => 'ana@example.test', 'password' => 'Clave2026', 'password_confirmation' => 'Clave2026'])->assertSessionHasErrors('nom');
    $this->post('/register', ['nom' => 'Ana', 'pat' => 'Quispe', 'cor' => ' TURISTA@PIPOCAS.TEST ', 'password' => 'Clave2026', 'password_confirmation' => 'Clave2026'])->assertSessionHasErrors('cor');
});
test('OTP verifica desde cualquier dispositivo y no puede reutilizarse', function () {
    $u = User::factory()->unverified()->create();
    DB::table('codigos_correo')->insert(['correo' => $u->cor, 'proposito' => 'verificar', 'hash' => Hash::make('123456'), 'vence_en' => now()->addMinutes(10), 'creado_en' => now()]);
    $this->actingAs($u)->post(route('codigo.verificar'), ['codigo' => '123456'])->assertSessionHasNoErrors();
    expect($u->fresh()->hasVerifiedEmail())->toBeTrue()->and(DB::table('codigos_correo')->count())->toBe(0);
    expect(app(CodigoCorreo::class)->comprobar($u->cor, 'verificar', '123456', fn () => null))->toBeFalse();
});
test('OTP bloquea tras cinco fallos y rechaza códigos vencidos', function () {
    $q = DB::table('codigos_correo');
    $q->insert(['correo' => 'ana@example.test', 'proposito' => 'recuperar', 'hash' => Hash::make('123456'), 'vence_en' => now()->addMinutes(10), 'creado_en' => now()]);
    $servicio = app(CodigoCorreo::class);
    for ($i = 0; $i < 5; $i++) {
        expect($servicio->comprobar('ana@example.test', 'recuperar', '999999', fn () => null))->toBeFalse();
    }
    expect($servicio->comprobar('ana@example.test', 'recuperar', '123456', fn () => null))->toBeFalse();
    $q->update(['intentos' => 0, 'vence_en' => now()->subSecond()]);
    expect($servicio->comprobar('ana@example.test', 'recuperar', '123456', fn () => null))->toBeFalse();
});
test('recuperación por código cambia contraseña consume código y revoca sesiones', function () {
    $u = User::factory()->create();
    DB::table('codigos_correo')->insert(['correo' => $u->cor, 'proposito' => 'recuperar', 'hash' => Hash::make('123456'), 'vence_en' => now()->addMinutes(10), 'creado_en' => now()]);
    DB::table('sessions')->insert(['id' => 'sesion-antigua', 'user_id' => $u->id, 'payload' => '', 'last_activity' => time()]);
    $this->post(route('codigo.restablecer'), ['cor' => $u->cor, 'codigo' => '123456', 'password' => 'Nueva2026', 'password_confirmation' => 'Nueva2026'])->assertRedirect(route('login'));
    expect(Hash::check('Nueva2026', $u->fresh()->clv))->toBeTrue()->and(DB::table('sessions')->where('user_id', $u->id)->exists())->toBeFalse();
    $this->post(route('codigo.restablecer'), ['cor' => $u->cor, 'codigo' => '123456', 'password' => 'Nueva2027', 'password_confirmation' => 'Nueva2027'])->assertSessionHasErrors('codigo');
});
test('novedades exige consentimiento y confirmación y baja firmadas', function () {
    config(['services.notificaciones.url' => 'http://correo.test', 'services.notificaciones.token' => str_repeat('a', 32)]);
    Http::fake(['correo.test/*' => Http::response(null, 204)]);
    $this->post(route('novedades.store'), ['correo' => 'ana@example.test'])->assertSessionHasErrors('consentimiento');
    $this->post(route('novedades.store'), ['correo' => 'ANA@example.test', 'consentimiento' => 1])->assertRedirect();
    $subs = DB::table('suscripciones_novedades')->first();
    expect($subs->confirmada_en)->toBeNull();
    $url = URL::temporarySignedRoute('novedades.confirmar', now()->addDay(), ['id' => $subs->id, 'version' => $subs->consentimiento_en]);
    $this->get($url)->assertOk();
    expect(DB::table('suscripciones_novedades')->first()->confirmada_en)->toBeNull();
    $this->post($url)->assertOk();
    expect(DB::table('suscripciones_novedades')->first()->confirmada_en)->not->toBeNull();
    $this->post('/novedades/'.$subs->id.'/baja')->assertForbidden();
    $baja = URL::signedRoute('novedades.baja', ['id' => $subs->id]);
    $this->post($baja)->assertOk();
    expect(DB::table('suscripciones_novedades')->first()->baja_en)->not->toBeNull();
    Http::assertSent(fn ($r) => $r->hasHeader('Authorization', 'Bearer '.str_repeat('a', 32)));
});
test('novedades y botones Google sin configurar no aparecen al usuario conectado', function () {
    $u = User::whereHas('rol', fn ($q) => $q->where('nom', 'turista'))->firstOrFail();
    $this->actingAs($u)->get('/')->assertOk()->assertDontSee('id="footer-novedades"', false);
    $this->post(route('novedades.store'), ['correo' => 'ana@example.test', 'consentimiento' => 1])->assertRedirect();
    expect(DB::table('suscripciones_novedades')->count())->toBe(0);
});
test('Google no vincula automáticamente una cuenta local del mismo correo', function () {
    $google = (new Laravel\Socialite\Two\User)->map(['id' => 'google-prueba', 'email' => 'turista@pipocas.test', 'name' => 'Ana Quispe']);
    $google->user = ['verified_email' => true];
    Socialite::shouldReceive('driver->user')->once()->andReturn($google);
    $this->get(route('google.callback'))->assertRedirect(route('login'))->assertSessionHasErrors('cor');
    $this->assertGuest();
    expect(User::where('cor', 'turista@pipocas.test')->first()->google_id)->toBeNull();
});
test('claves de acceso exigen autenticación y rechazan credencial falsa', function () {
    $this->get(route('passkey.registration-options'))->assertRedirect(route('login'));
    $this->getJson(route('passkey.login-options'))->assertOk()->assertJsonStructure(['options' => ['challenge']]);
    $this->postJson(route('passkey.login'), ['credential' => []])->assertUnprocessable();
    $this->assertGuest();
});

test('menor solo embarca después de su adulto y se bloquea al elevar el riesgo del tour', function () {
    $s = salidaRevision();
    $r = app(ReservaService::class)->confirmar(borradorRevision($s, [adultoRevision(), menorRevision()]), null);
    $r->update(['est' => Reserva::EST_CONFIRMADA, 'est_pag' => Reserva::PAG_PAGADO]);
    $menor = $r->participantes()->where('nom_cpl', 'Luis Quispe')->firstOrFail();
    $op = User::whereHas('rol', fn ($q) => $q->where('nom', 'operaciones'))->firstOrFail();
    $this->actingAs($op)->post(route('admin.reservas.menor.verificar', [$r, $menor]), ['documentos_originales' => 1])->assertSessionHasNoErrors();
    $menor->refresh();
    $operacion = app(SalidaService::class);
    expect($operacion->validarBoleto($s, $menor->boleto->cod, $op)['ok'])->toBeFalse();
    expect($operacion->validarBoleto($s, $menor->acompanante->boleto->cod, $op)['ok'])->toBeTrue();
    expect($operacion->validarBoleto($s, $menor->boleto->cod, $op)['ok'])->toBeTrue();
    $s->tour->update(['alto_riesgo' => true]);
    expect($operacion->validarBoleto($s, $menor->boleto->cod, $op)['ok'])->toBeFalse();
});
