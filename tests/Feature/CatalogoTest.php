<?php

use App\Models\CategoriaTour;
use App\Models\FotoTour;
use App\Models\PerfilGuia;
use App\Models\Tour;
use App\Models\User;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\RutasCatalogoSeeder;
use Database\Seeders\UsuariosDemoSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, CatalogoLandingSeeder::class, RutasCatalogoSeeder::class]);
    Storage::fake('public');
    $this->actingAs(User::factory()->rol(User::ROL_OPERACIONES)->create());
});

function datosTour(array $extra = []): array
{
    return $extra + [
        'cat_id' => CategoriaTour::first()->id, 'nom' => 'Chacaltaya y Valle de las Ánimas', 'des' => 'Nevado y formaciones rocosas.',
        'niv_dif' => 'moderado', 'alt_min_m' => 3800, 'alt_max_m' => 5300, 'dur_hrs' => 8, 'pre_bas' => 250, 'cap_max' => 12, 'act' => 1,
    ];
}

test('el catálogo público tiene filtros combinados de categoría, dificultad y altitud', function () {
    auth()->logout();
    $this->get('/')->assertOk()
        ->assertSee('id="f-dif"', false)
        ->assertSee('id="f-alt"', false)
        ->assertSee('data-dif="experto"', false)
        ->assertSee('data-alt="5100"', false);
});

test('el detalle muestra el trazado GeoJSON, el perfil de elevación y los puntos de interés', function () {
    auth()->logout();
    $tour = Tour::where('rta_amg', 'descenso-camino-de-la-muerte')->first();
    expect($tour->trz_geo['type'])->toBe('LineString');

    $this->get(route('tours.show', $tour))->assertOk()
        ->assertSee('id="perfil"', false)
        ->assertSee('Perfil de elevación')
        ->assertSee('La Cumbre')
        ->assertSee('"type":"LineString"', false);
});

test('crea tours con código y ruta amigable únicos (RF-37)', function () {
    $this->post(route('admin.catalogo.store'), datosTour())->assertSessionHasNoErrors();
    $this->post(route('admin.catalogo.store'), datosTour())->assertSessionHasNoErrors();

    $tours = Tour::where('nom', 'Chacaltaya y Valle de las Ánimas')->orderBy('id')->get();
    expect($tours->pluck('rta_amg')->all())->toBe(['chacaltaya-y-valle-de-las-animas', 'chacaltaya-y-valle-de-las-animas-2'])
        ->and($tours[0]->cod)->toMatch('/^TUR-\d{4}-[2-9A-HJKMNP-Z]{5}$/')
        ->and($tours[0]->cod)->not->toBe($tours[1]->cod);

    $this->get(route('tours.show', $tours[1]))->assertOk();
});

test('valida duración de un día y altitudes coherentes', function () {
    $this->post(route('admin.catalogo.store'), datosTour(['dur_hrs' => 20, 'alt_min_m' => 4000, 'alt_max_m' => 3000]))
        ->assertSessionHasErrors(['dur_hrs', 'alt_max_m']);
});

test('la ruta amigable se conserva al editar salvo que se pida regenerarla', function () {
    $tour = Tour::where('rta_amg', 'tiwanaku-y-puma-punku')->first();

    $this->put(route('admin.catalogo.update', $tour), datosTour(['nom' => 'Tiwanaku arqueológico', 'cat_id' => $tour->cat_id, 'alt_min_m' => 3800, 'alt_max_m' => 3850]))->assertSessionHasNoErrors();
    expect($tour->fresh()->rta_amg)->toBe('tiwanaku-y-puma-punku');

    $this->put(route('admin.catalogo.update', $tour), datosTour(['nom' => 'Tiwanaku arqueológico', 'cat_id' => $tour->cat_id, 'alt_min_m' => 3800, 'alt_max_m' => 3850, 'regenerar_ruta' => 1]));
    expect($tour->fresh()->rta_amg)->toBe('tiwanaku-arqueologico');
});

test('galería: subir, ordenar y eliminar fotos', function () {
    $tour = Tour::first();
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');

    $this->post(route('admin.catalogo.fotos', $tour), [
        'fotos' => [UploadedFile::fake()->createWithContent('a.png', $png), UploadedFile::fake()->createWithContent('b.png', $png)],
    ])->assertSessionHasNoErrors();

    $nuevas = $tour->fotos()->orderByDesc('id')->take(2)->get();
    Storage::disk('public')->assertExists($nuevas[0]->rta_arc);

    $orden = $tour->fotos()->orderBy('ord_vis')->pluck('id')->reverse()->values()->all();
    $this->post(route('admin.catalogo.fotos.orden', $tour), ['orden' => $orden, 'txt' => [$orden[0] => 'Portada nueva']])->assertSessionHasNoErrors();
    expect($tour->fotos()->orderBy('ord_vis')->first()->id)->toBe($orden[0])
        ->and(FotoTour::find($orden[0])->txt_atv)->toBe('Portada nueva');

    $this->delete(route('admin.catalogo.fotos.eliminar', [$tour, $nuevas[0]]))->assertSessionHasNoErrors();
    Storage::disk('public')->assertMissing($nuevas[0]->rta_arc);
    expect(FotoTour::find($nuevas[0]->id))->toBeNull();

    $this->post(route('admin.catalogo.fotos', $tour), ['fotos' => [UploadedFile::fake()->createWithContent('x.txt', 'hola')]])->assertSessionHasErrors('fotos.0');
});

test('editor de ruta: guarda puntos, genera el trazado y actualiza las altitudes', function () {
    $tour = Tour::first();

    $this->post(route('admin.catalogo.ruta', $tour), [
        'puntos' => [
            ['nom' => 'Salida', 'lat' => -16.5, 'lon' => -68.15, 'alt_m' => 3600, 'tip_pun' => 'inicio'],
            ['nom' => 'Cumbre', 'lat' => -16.4, 'lon' => -68.1, 'alt_m' => 4900, 'tip_pun' => 'mirador'],
            ['nom' => 'Llegada', 'lat' => -16.3, 'lon' => -68.0, 'alt_m' => 4100, 'tip_pun' => 'fin'],
        ],
        'quitar_trazado' => 1,
        'trazado_desde_puntos' => 1,
    ])->assertSessionHasNoErrors();

    $tour->refresh();
    expect($tour->puntosRuta)->toHaveCount(3)
        ->and($tour->puntosRuta->pluck('ord_sec')->all())->toBe([1, 2, 3])
        ->and($tour->trz_geo['coordinates'][1])->toBe([-68.1, -16.4, 4900])
        ->and($tour->alt_min_m)->toBe(3600)
        ->and($tour->alt_max_m)->toBe(4900);

    $this->post(route('admin.catalogo.ruta', $tour), ['puntos' => [], 'trazado' => '{"type":"Point","coordinates":[0,0]}'])
        ->assertSessionHasErrors('trazado');
});

test('la baja del tour es lógica y las categorías con tours no se eliminan', function () {
    $tour = Tour::first();
    $this->delete(route('admin.catalogo.destroy', $tour))->assertRedirect(route('admin.tours'));
    expect($tour->fresh()->eli_en)->not->toBeNull()->and($tour->fresh()->act)->toBeFalse();

    $this->delete(route('admin.catalogo.categoria.eliminar', CategoriaTour::whereHas('tours')->first()))->assertSessionHasErrors('categoria');

    $this->post(route('admin.catalogo.categoria'), ['nom' => 'Lagos', 'ico' => 'fa-solid fa-water'])->assertSessionHasNoErrors();
    expect(CategoriaTour::where('rta_amg', 'lagos')->exists())->toBeTrue();
});

test('las pantallas de administración del catálogo cargan', function () {
    $this->get(route('admin.tours'))->assertOk()->assertSee('Tours publicados');
    $this->get(route('admin.catalogo.create'))->assertOk();
    $this->get(route('admin.catalogo.edit', Tour::first()))->assertOk()->assertSee('mapa-editor', false);
});

test('categorías y guías tienen rutas públicas sin documentos ni correo privado', function () {
    $categoria = CategoriaTour::firstOrFail();
    $this->get(route('categorias.show', $categoria))->assertOk()->assertSee($categoria->nom);
    $this->seed([UsuariosDemoSeeder::class, OperacionDemoSeeder::class]);
    $guia = PerfilGuia::where('act', true)->firstOrFail();
    $this->get(route('guias.perfil', $guia))->assertOk()->assertDontSee($guia->usuario->cor);
    $guia->update(['act' => false]);
    $this->get(route('guias.perfil', $guia))->assertNotFound();
});
