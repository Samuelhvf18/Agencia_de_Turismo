<?php

use App\Models\ConversacionChatbot;
use App\Models\MensajeChatbot;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\User;
use App\Services\CodeGeneratorService;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class]);
    Queue::fake();
    config(['services.n8n.webhook_chatbot' => null]);
});

function preguntar($test, string $texto)
{
    return $test->postJson(route('chatbot.enviar'), ['mensaje' => $texto])->assertOk();
}

test('la página del chatbot carga', function () {
    $this->get(route('chatbot'))->assertOk()->assertSee('Asistente turístico');
});

test('sin n8n recomienda tours activos según tiempo, dificultad y presupuesto (respaldo local)', function () {
    $r = preguntar($this, 'Recomiéndame un tour fácil de 6 horas por menos de 200 bs');

    expect($r->json('modo'))->toBe('local')
        ->and($r->json('intencion'))->toBe('recomendacion')
        ->and($r->json('respuesta'))->toContain('Valle de la Luna')
        ->and($r->json('respuesta'))->not->toContain('Huayna');

    // Se guardan el mensaje del usuario y la respuesta.
    expect(MensajeChatbot::where('rem', 'usuario')->count())->toBe(1)
        ->and(MensajeChatbot::where('rem', 'bot')->value('int_det'))->toBe('recomendacion');
});

test('responde en el idioma del usuario', function () {
    $r = preguntar($this, 'What should I bring for the tour?');

    expect($r->json('idioma'))->toBe('en')
        ->and($r->json('respuesta'))->toContain('waterproof jacket');
});

test('consulta una reserva en solo lectura con código y correo', function () {
    $salida = SalidaTour::first();
    $reserva = Reserva::create(['cod' => CodeGeneratorService::generate('RES'), 'sal_tur_id' => $salida->id, 'nom_ctc' => 'Ana', 'cor_ctc' => 'ana@example.com', 'tel_ctc' => '70000000', 'cnt_ptc' => 2, 'pre_tot' => 300]);

    expect(preguntar($this, "Quiero ver mi reserva {$reserva->cod}")->json('respuesta'))->toContain('correo');
    expect(preguntar($this, "Mi reserva es {$reserva->cod} y mi correo ana@example.com")->json('respuesta'))
        ->toContain($salida->tour->nom)->toContain('pendiente');
    expect(preguntar($this, "Reserva {$reserva->cod} correo otro@example.com")->json('respuesta'))->toContain('No encontré');
});

test('derivar a una persona marca la conversación, avisa a operaciones y el asesor responde', function () {
    $r = preguntar($this, 'Quiero hablar con una persona por favor');
    expect($r->json('derivado'))->toBeTrue();

    $conversacion = ConversacionChatbot::first();
    expect($conversacion->der_hum)->toBeTrue()
        ->and(RegistroNotificacion::where('tip', 'derivacion_chatbot')->exists())->toBeTrue();

    // Mientras está derivada, el bot no contesta.
    $antes = MensajeChatbot::where('rem', 'bot')->count();
    preguntar($this, '¿Siguen ahí?');
    expect(MensajeChatbot::where('rem', 'bot')->count())->toBe($antes);

    // El asesor responde desde el panel y el cliente lo recibe por sondeo.
    $ultimo = MensajeChatbot::max('id');
    $sesion = session('chatbot.sesion');
    $this->actingAs(User::where('cor', 'operaciones@pipocas.test')->first())
        ->post(route('admin.chatbot.responder', $conversacion), ['mensaje' => 'Hola, soy Carla de Pipocas.'])->assertSessionHasNoErrors();
    $this->get(route('admin.chatbot'))->assertOk();

    $this->withSession(['chatbot.sesion' => $sesion])->getJson(route('chatbot.mensajes', ['desde' => $ultimo]))
        ->assertOk()->assertJsonPath('mensajes.0.humano', true)->assertJsonPath('mensajes.0.txt', 'Hola, soy Carla de Pipocas.');
});

test('con n8n usa la respuesta de la IA y respeta la derivación que pida el agente', function () {
    config(['services.n8n.webhook_chatbot' => 'https://n8n.test/webhook/pipocas-chatbot', 'services.n8n.token' => 'tok']);
    Http::fakeSequence('n8n.test/*')
        ->push(['respuesta' => 'Te recomiendo el Valle de la Luna.', 'intencion' => 'recomendacion', 'derivar' => false])
        ->push(['respuesta' => 'Te paso con un asesor.', 'intencion' => 'derivacion', 'derivar' => true]);

    $r = preguntar($this, 'Hola, ¿qué tour me recomiendas?');
    expect($r->json('modo'))->toBe('ia')->and($r->json('respuesta'))->toBe('Te recomiendo el Valle de la Luna.');
    Http::assertSent(fn ($req) => $req->hasHeader('X-Pipocas-Token', 'tok') && $req['idioma'] === 'es' && isset($req['conversacion_id']));

    expect(preguntar($this, 'No entiendes nada')->json('derivado'))->toBeTrue();
});

test('si la IA no responde, el chatbot sigue funcionando con el respaldo local (RNF-20)', function () {
    config(['services.n8n.webhook_chatbot' => 'https://n8n.test/webhook/pipocas-chatbot']);
    Http::fake(['n8n.test/*' => Http::response('error', 503)]);

    $r = preguntar($this, '¿Cómo funcionan los pagos?');
    expect($r->json('modo'))->toBe('local')->and($r->json('respuesta'))->toContain('anticipo');
});

test('las herramientas del agente exigen el token de n8n', function () {
    config(['services.n8n.token' => 'tok']);

    $this->getJson('/api/n8n/chatbot/tours')->assertStatus(401);
    $this->getJson('/api/n8n/chatbot/tours?dificultad=facil&presupuesto=250', ['X-Pipocas-Token' => 'tok'])
        ->assertOk()->assertJsonPath('tours.0.dificultad', 'facil');
    $this->getJson('/api/n8n/chatbot/faq?tema=altura&idioma=en', ['X-Pipocas-Token' => 'tok'])->assertOk()->assertSee('3,650');
    $this->postJson('/api/n8n/chatbot/reserva', ['codigo' => 'RES-XXXXXX', 'correo' => 'a@b.com'], ['X-Pipocas-Token' => 'tok'])->assertOk()->assertJson(['encontrada' => false]);
});
