<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Livewire\AsistenteTuristico;
use App\Livewire\CatalogoTours;
use App\Livewire\FlotaListado;
use App\Livewire\GaleriaTour;
use App\Livewire\PersonalListado;
use App\Models\FotoTour;
use App\Models\Idioma;
use App\Models\MensajeChatbot;
use App\Models\Pais;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\Chatbot\ChatbotService;
use App\Support\PresentadorTour;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\IoColasMarkovSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class, IoColasMarkovSeeder::class]);
    Queue::fake();
    Mail::fake();
    $this->ops = User::where('cor', 'operaciones@pipocas.test')->firstOrFail();
    $this->admin = User::factory()->rol(User::ROL_ADMIN)->create(['nom' => 'Ana Quispe']);
});

function imagenMejoras(string $nombre = 'foto.png'): UploadedFile
{
    // PNG real de 100 × 100, sin dependencia de GD en las pruebas.
    $raw = str_repeat("\0".str_repeat("\x1f\x5f\x8b", 100), 100);
    $chunk = fn ($tipo, $datos) => pack('N', strlen($datos)).$tipo.$datos.pack('N', crc32($tipo.$datos));
    $png = "\x89PNG\r\n\x1a\n".$chunk('IHDR', pack('NNCCCCC', 100, 100, 8, 2, 0, 0, 0)).$chunk('IDAT', gzcompress($raw)).$chunk('IEND', '');

    return UploadedFile::fake()->createWithContent($nombre, $png);
}

function guiaMejoras(array $extra = []): array
{
    return $extra + ['nom' => 'María José Quispe', 'cor' => 'nueva@example.test', 'password' => 'Clave2026',
        'tel_prefijo' => '+591', 'tel_numero' => '70012345', 'ani_exp' => 3, 'lim_hrs_sem' => 40,
        'idiomas' => [Idioma::where('cod_iso', 'es')->value('id')]];
}

test('operaciones consulta personal pero no puede crear guías ni choferes', function () {
    $this->actingAs($this->ops)->get(route('admin.personal'))->assertOk()->assertDontSee('Nuevo guía')->assertDontSee('Nuevo chofer');
    $antes = User::count();
    foreach (['guia', 'chofer'] as $tipo) {
        $this->post(route('admin.personal.store', $tipo), guiaMejoras())->assertForbidden();
    }
    expect(User::count())->toBe($antes);
});

test('operaciones solo cambia asientos y no puede registrar vehículos ni falsificar su identidad', function () {
    $v = Vehiculo::create(['pla' => '9977-ABC', 'mar' => 'Toyota', 'mdl' => 'Hiace', 'tip' => 'van', 'cap_asi' => 14, 'tip_cmb' => 'diesel', 'est' => 'activo']);
    $this->actingAs($this->ops)->post(route('admin.transporte.store'), $v->toArray())->assertForbidden();
    $this->put(route('admin.transporte.update', $v), ['cap_asi' => 12])->assertSessionHasNoErrors();
    expect($v->fresh()->cap_asi)->toBe(12);
    $this->put(route('admin.transporte.update', $v), ['cap_asi' => 10, 'pla' => '9999-ABC', 'mar' => 'Otra', 'mdl' => 'Otro', 'tip' => 'otro', 'tip_cmb' => 'gnv', 'est' => 'inactivo'])
        ->assertSessionHasErrors(['pla', 'mar', 'mdl', 'tip', 'tip_cmb', 'est']);
    expect($v->fresh()->cap_asi)->toBe(12)->and($v->fresh()->pla)->toBe('9977-ABC')->and($v->fresh()->est)->toBe('activo');
    $this->get(route('admin.transporte'))->assertOk()->assertDontSee('Registrar vehículo');
});

test('reducir asientos no deja salidas futuras con cupos incompatibles', function () {
    $v = SalidaTour::whereNotNull('veh_id')->firstOrFail()->vehiculo;
    $this->actingAs($this->ops)->put(route('admin.transporte.update', $v), ['cap_asi' => 1])->assertSessionHasErrors('cap_asi');
});

test('nombres teléfonos correos y contraseñas se validan aunque se omita Javascript', function () {
    $this->actingAs($this->admin)->post(route('admin.personal.store', 'guia'), guiaMejoras(['nom' => 'Ana 123!', 'tel_numero' => '7abc!', 'cor' => 'correo@@test', 'password' => 'corta']))
        ->assertSessionHasErrors(['nom', 'tel_numero', 'cor', 'password']);
    $this->post(route('admin.personal.store', 'guia'), guiaMejoras(['cor' => ' TURISTA@PIPOCAS.TEST ']))->assertSessionHasErrors('cor');
    $this->post(route('admin.personal.store', 'guia'), guiaMejoras(['cor' => ' NUEVA@EXAMPLE.TEST ']))->assertSessionHasNoErrors();
    $nuevo = User::where('cor', 'nueva@example.test')->firstOrFail();
    expect($nuevo->nom)->toBe('María José Quispe')->and($nuevo->tel)->toBe('+59170012345');
});

test('los nombres del catálogo y del inventario tampoco aceptan números', function () {
    $this->actingAs($this->admin)->post(route('admin.catalogo.categoria'), ['nom' => 'Aventura 123'])->assertSessionHasErrors('nom');
    $this->post(route('admin.inventario.tipo'), ['nom' => 'Casco 123', 'pre_alq_bas' => 20])->assertSessionHasErrors('nom');
});

test('catálogos amplían países e idiomas sin duplicar códigos', function () {
    expect(Pais::count())->toBe(249)->and(Idioma::count())->toBe(26);
    $this->seed(CatalogosBaseSeeder::class);
    expect(Pais::count())->toBe(249)->and(Pais::where('cod_iso', 'ZA')->exists())->toBeTrue()->and(Idioma::where('cod_iso', 'ja')->exists())->toBeTrue();
});

test('el catálogo Livewire filtra sin devolver tours que no coinciden', function () {
    Livewire::test(CatalogoTours::class)->set('q', 'Huayna')->assertSee('Huayna Potosí')->assertDontSee('Tiwanaku y Puma Punku')
        ->call('limpiar')->set('categoria', 'city')->assertSee('Tiwanaku')->assertDontSee('Descenso Camino de la Muerte');
});

test('personal y flota usan filtros reactivos con autorización en cada petición', function () {
    $this->actingAs($this->ops);
    Livewire::test(PersonalListado::class)->assertSee('Guía Demo')->set('buscar', 'SinCoincidencias')->assertDontSee('Guía Demo');
    Livewire::test(FlotaListado::class)->set('buscar', '2345')->assertSee('2345-LPZ');
    $this->actingAs(User::factory()->create());
    Livewire::test(PersonalListado::class)->assertForbidden();
    Livewire::test(FlotaListado::class)->assertForbidden();
});

test('galería Livewire sube una imagen válida y actualiza su texto y la portada', function () {
    Storage::fake('public');
    $tour = Tour::firstOrFail();
    $this->actingAs($this->admin);
    $componente = Livewire::test(GaleriaTour::class, ['tour' => $tour])->set('fotos', [imagenMejoras()])->call('subir')->assertHasNoErrors();
    $foto = $tour->fotos()->orderByDesc('id')->first();
    Storage::disk('public')->assertExists($foto->rta_arc);
    $componente->set('textos.'.$foto->id, 'Vista real del destino')->call('guardarTextos')->assertHasNoErrors()->call('mover', $foto->id, -1);
    expect($tour->fotos()->orderBy('ord_vis')->first()->id)->toBe($foto->id)->and($foto->fresh()->txt_atv)->toBe('Vista real del destino');
});

test('galería rechaza archivos disfrazados y fotos demasiado pequeñas sin guardar archivos', function () {
    Storage::fake('public');
    $tour = Tour::firstOrFail();
    $this->actingAs($this->admin)->post(route('admin.catalogo.fotos', $tour), ['fotos' => [UploadedFile::fake()->createWithContent('falso.jpg', '<?php echo 1;')]])->assertSessionHasErrors('fotos.0');
    $pequena = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
    $this->post(route('admin.catalogo.fotos', $tour), ['fotos' => [UploadedFile::fake()->createWithContent('minima.png', $pequena)]])->assertSessionHasErrors('fotos.0');
    expect(Storage::disk('public')->allFiles())->toBe([]);
});

test('galería no permite alterar fotos de otro tour', function () {
    $this->actingAs($this->admin);
    $tour = Tour::firstOrFail();
    $otra = FotoTour::where('tur_id', '<>', $tour->id)->firstOrFail();
    Livewire::test(GaleriaTour::class, ['tour' => $tour])->call('eliminar', $otra->id)->assertNotFound();
});

test('no se puede quitar la única portada de un tour publicado', function () {
    $this->actingAs($this->admin);
    $tour = Tour::firstOrFail();
    $foto = $tour->fotos()->first();
    $this->delete(route('admin.catalogo.fotos.eliminar', [$tour, $foto]))->assertSessionHasErrors('fotos');
    expect($foto->fresh())->not->toBeNull();
});

test('foto de perfil comprueba el contenido real y conserva la anterior si falla', function () {
    Storage::fake('public');
    $u = User::factory()->create(['nom' => 'Ana Quispe', 'fot_prf' => 'profile-photos/anterior.png']);
    Storage::disk('public')->put($u->fot_prf, 'anterior');
    $datos = ['name' => $u->nom, 'email' => $u->cor];
    expect(fn () => app(UpdateUserProfileInformation::class)->update($u, $datos + ['photo' => UploadedFile::fake()->createWithContent('foto.png', 'texto')]))->toThrow(ValidationException::class);
    Storage::disk('public')->assertExists('profile-photos/anterior.png');
    app(UpdateUserProfileInformation::class)->update($u, $datos + ['photo' => imagenMejoras()]);
    Storage::disk('public')->assertExists($u->fresh()->fot_prf);
    Storage::disk('public')->assertMissing('profile-photos/anterior.png');
});

test('chatbot reconoce destinos y conserva tarjetas reales con imagen y botón al recargar', function () {
    Livewire::test(AsistenteTuristico::class)->set('mensaje', 'Muéstrame el Huayna Potosí')->call('enviar')->assertHasNoErrors()
        ->assertSee('Huayna Potosí')->assertSee('huayna-potosi.jpg', false)->assertSee('Ver tour');
    $m = MensajeChatbot::where('rem', 'bot')->firstOrFail();
    $tour = Tour::where('rta_amg', 'glaciar-huayna-potosi')->firstOrFail();
    expect($m->tours_ids)->toBe([$tour->id]);
    $c = $m->conversacion;
    expect(app(ChatbotService::class)->mensajesDesde($c, 0)[1]['tours'][0]['url'])->toBe(route('tours.show', $tour));
    $tour->update(['act' => false]);
    expect(app(ChatbotService::class)->mensajesDesde($c, 0)[1]['tours'])->toBe([]);
});

test('n8n no puede inyectar imágenes enlaces o tours inexistentes en las tarjetas', function () {
    config(['services.n8n.webhook_chatbot' => 'https://n8n.test/chat']);
    Http::fake(['n8n.test/*' => Http::response(['respuesta' => '<script>alert(1)</script>', 'tours_ids' => [999999], 'imagen' => 'javascript:alert(1)', 'url' => 'https://malicioso.test'])]);
    Livewire::test(AsistenteTuristico::class)->set('mensaje', 'Hola')->call('enviar')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false)->assertDontSee('https://malicioso.test');
    expect(MensajeChatbot::where('rem', 'bot')->first()->tours_ids)->toBe([]);
});

test('las rutas de imágenes rechazan recorridos fuera de los directorios públicos', function () {
    expect(PresentadorTour::urlFoto('../.env'))->toBeNull()->and(PresentadorTour::urlFoto('javascript:alert(1)'))->toBeNull();
    $this->get(route('imagenes.creditos'))->assertOk()->assertSee('Wikimedia Commons')->assertSee('Carlos García Salazar');
});
