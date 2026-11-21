<?php

use App\Jobs\NotificacionJob;
use App\Models\Boleto;
use App\Models\DepartamentoBolivia;
use App\Models\MarkovEstadoCliente;
use App\Models\MarkovTransicion;
use App\Models\Pais;
use App\Models\Reserva;
use App\Models\Tour;
use App\Models\User;
use App\Services\Io\MarkovService;
use App\Services\Notificaciones\NotificacionService;
use App\Services\Reservas\BoletoService;
use App\Services\Reservas\ReservaService;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\IoColasMarkovSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class,
        CatalogoLandingSeeder::class, OperacionDemoSeeder::class, IoColasMarkovSeeder::class]);
    Queue::fake();
    Mail::fake();
});

function reservaObservaciones(?User $usuario = null): Reserva
{
    $salida = Tour::where('rta_amg', 'valle-de-la-luna-y-telefericos')->firstOrFail()->salidas()->orderBy('fec_sal')->firstOrFail();

    return app(ReservaService::class)->confirmar(['sal_tur_id' => $salida->id,
        'contacto' => ['nom_ctc' => 'Ana Quispe', 'cor_ctc' => 'ana@example.test', 'tel_ctc' => '+59170000000'],
        'participantes' => [['nom_cpl' => 'Ana Quispe', 'tip_doc' => 'CI', 'num_doc' => '12345678', 'ci_cmp' => '',
            'ci_dep_id' => DepartamentoBolivia::where('cod', 'LP')->value('id'),
            'fecha_nacimiento' => today()->subYears(30)->toDateString(), 'pai_id' => Pais::where('cod_iso', 'BO')->value('id'), 'es_ctc_pri' => 1]],
    ], $usuario);
}

test('login vuelve al tour con sus filtros y sin navegación previa usa la landing', function () {
    $user = User::factory()->create();
    $url = route('tours.show', Tour::first()).'?lang=es';
    $this->get($url)->assertOk();
    $this->get('/login')->assertOk();
    $this->post('/login', ['login' => $user->cor, 'password' => 'password'])->assertRedirect($url);
});

test('registro conserva el destino durante la verificación de correo', function () {
    $url = route('tours.show', Tour::first());
    $this->get($url)->assertOk();
    $this->get('/register')->assertOk();
    $this->post('/register', ['nom' => 'Ana', 'pat' => 'Quispe', 'cor' => 'ana-nueva@example.test',
        'password' => 'Clave2026', 'password_confirmation' => 'Clave2026'])->assertRedirect(route('verification.notice'));
    expect(session('url.intended'))->toBe(parse_url($url, PHP_URL_PATH));
    auth()->user()->markEmailAsVerified();
    $this->post(route('codigo.verificar'), ['codigo' => '123456'])->assertRedirect($url);
});

test('el destino externo del cliente no se usa para redirigir', function () {
    $user = User::factory()->create();
    $this->get('/login?redirect=https://externo.test')->assertOk();
    $this->post('/login', ['login' => $user->cor, 'password' => 'password'])->assertRedirect(route('home'));
});

test('la ficha rechaza nombre con cifras y teléfono con signos o prefijo desconocido', function () {
    $r = reservaObservaciones();
    $this->withSession(['reserva.borrador' => ['sal_tur_id' => $r->sal_tur_id, 'contacto' => ['nom_ctc' => 'Ana Quispe'], 'participantes' => [['nom_cpl' => 'Ana Quispe']]]]);
    $this->post(route('reservas.ficha.store'), ['fichas' => [0 => ['nom_ctc_eme' => 'Ana 123!', 'prefijo_eme' => '+999', 'telefono_eme' => '7abc-00']]])
        ->assertSessionHasErrors(['fichas.0.nom_ctc_eme', 'fichas.0.prefijo_eme', 'fichas.0.telefono_eme']);
});

test('una ficha opcional sin completar sigue siendo opcional al elegir un país', function () {
    $r = reservaObservaciones();
    $this->withSession(['reserva.borrador' => ['sal_tur_id' => $r->sal_tur_id, 'contacto' => ['nom_ctc' => 'Ana Quispe'], 'participantes' => [['nom_cpl' => 'Ana Quispe']]]]);
    $this->post(route('reservas.ficha.store'), ['fichas' => [0 => ['prefijo_eme' => '+591', 'telefono_eme' => '']]])
        ->assertSessionHasNoErrors()->assertRedirect(route('reservas.resumen'));
});

test('ticket individual y conjunto descargan PDF y otro usuario no puede descargarlos', function () {
    $u = User::factory()->create();
    $r = reservaObservaciones($u);
    $r->update(['est' => Reserva::EST_CONFIRMADA, 'est_pag' => Reserva::PAG_PAGADO]);
    $boleto = app(BoletoService::class)->emitir($r)->first();
    $this->actingAs($u)->get(route('boletos.pdf', $boleto))->assertOk()->assertHeader('content-type', 'application/pdf');
    $this->get(route('reservas.boletos', $r))->assertOk()->assertHeader('content-type', 'application/pdf');
    $this->actingAs(User::factory()->create())->get(route('boletos.pdf', $boleto))->assertForbidden();
    $boleto->update(['est' => Boleto::EST_CANCELADO]);
    $this->actingAs($u)->get(route('boletos.pdf', $boleto))->assertNotFound();
});

test('crear reserva genera aviso con código y al pagar Spring recibe el PDF', function () {
    config(['services.notificaciones.url' => 'http://correo.test', 'services.notificaciones.token' => str_repeat('t', 32)]);
    Http::fake(['correo.test/*' => Http::response(null, 204)]);
    $r = reservaObservaciones();
    $aviso = $r->notificaciones()->where('tip', 'reserva_creada')->firstOrFail();
    (new NotificacionJob($aviso->id))->handle();
    Http::assertSent(fn ($req) => str_contains($req['texto'], $r->cod) && $req['adjuntos'] === []);
    $r->update(['est' => Reserva::EST_CONFIRMADA, 'est_pag' => Reserva::PAG_PAGADO]);
    app(BoletoService::class)->emitir($r);
    $pago = app(NotificacionService::class)->confirmacionReserva($r);
    (new NotificacionJob($pago->id))->handle();
    Http::assertSent(fn ($req) => count($req['adjuntos']) === 1 && str_starts_with(base64_decode($req['adjuntos'][0]['contenido']), '%PDF-')
        && $req->hasHeader('Authorization', 'Bearer '.str_repeat('t', 32)));
    expect($pago->fresh()->est)->toBe('enviado');
    // Reenvía el PDF original.
    $primero = Http::recorded()->last()[0]->data();
    $pago->refresh()->update(['est' => 'fallido']);
    $r->update(['nom_ctc' => 'Nombre Cambiado']);
    (new NotificacionJob($pago->id))->handle();
    expect(Http::recorded()->last()[0]->data())->toBe($primero);
});

test('el turista ve sus datos y solo actualiza su propio teléfono', function () {
    $u = User::factory()->create(['tel' => '+59170000000']);
    $otro = User::factory()->create(['tel' => '+59171111111']);
    $this->actingAs($u)->get(route('turista.cuenta'))->assertOk()->assertSee($u->cor)->assertDontSee($otro->cor);
    $this->put(route('turista.cuenta.update'), ['prefijo' => '+51', 'telefono' => '987654321', 'usr_id' => $otro->id, 'rol_id' => 1, 'cor' => 'otro@example.test'])->assertSessionHasNoErrors();
    expect($u->fresh()->tel)->toBe('+51987654321')->and($u->fresh()->cor)->toBe($u->cor)->and($otro->fresh()->tel)->toBe('+59171111111');
    $this->put(route('turista.cuenta.update'), ['prefijo' => '+591', 'telefono' => '7abc!'])->assertSessionHasErrors('telefono');
});

test('recomendaciones usan visitas y reservas y excluyen salidas ya reservadas', function () {
    $u = User::factory()->create();
    $r = reservaObservaciones($u);
    $visitado = Tour::where('cat_id', '<>', $r->salida->tour->cat_id)->firstOrFail();
    $this->actingAs($u)->get(route('tours.show', $visitado))->assertOk();
    $this->get('/')->assertOk()->assertViewHas('recomendaciones', function ($recs) use ($r) {
        return $recs['tours']->isNotEmpty() && ! $recs['tours']->contains('id', $r->salida->tur_id);
    })->assertSee('Para tu interés');
    app(MarkovService::class)->registrarCambio($u->id, 'perdido');
    $this->get('/')->assertViewHas('recomendaciones', fn ($recs) => $recs['mensaje'] === 'Retoma tus planes con estas experiencias.');
    Tour::query()->update(['act' => false]);
    $this->get('/')->assertOk()->assertViewHas('recomendaciones', fn ($r) => $r['tours']->isEmpty());
});

test('recuperación muestra primero correo y después código sin repetir formularios', function () {
    $this->get(route('codigo.recuperar'))->assertOk()->assertSee('Enviar código de recuperación')->assertDontSee('id="codigo"', false);
    $this->post(route('codigo.solicitar'), ['cor' => 'desconocida@example.test'])->assertRedirect(route('codigo.recuperar'));
    $this->get(route('codigo.recuperar'))->assertOk()->assertSee('id="codigo"', false)->assertDontSee('id="solicitar-correo"', false);
});

test('la matriz vigente cambia la estrategia sin publicar probabilidades individuales', function () {
    $u = User::factory()->create();
    app(MarkovService::class)->registrarCambio($u->id, 'prospecto');
    $origen = MarkovEstadoCliente::where('cod', 'prospecto')->value('id');
    $perdido = MarkovEstadoCliente::where('cod', 'perdido')->value('id');
    MarkovTransicion::where('est_ori_id', $origen)->where('act', true)->update(['prb' => 0]);
    MarkovTransicion::where('est_ori_id', $origen)->where('est_des_id', $origen)->where('act', true)->update(['prb' => 0.2]);
    MarkovTransicion::where('est_ori_id', $origen)->where('est_des_id', $perdido)->where('act', true)->update(['prb' => 0.8]);
    $this->actingAs($u)->get('/')->assertOk()->assertViewHas('recomendaciones', fn ($r) => $r['mensaje'] === 'Retoma tus planes con estas experiencias.');
});
