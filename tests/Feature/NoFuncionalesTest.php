<?php

use App\Models\Tour;
use App\Models\User;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\RolesSeeder;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, CatalogoLandingSeeder::class]);
});

test('RNF-14: la interfaz pública está en español y cambia a inglés con ?lang=en', function () {
    $tour = Tour::first();

    $this->get('/')->assertOk()->assertSee('<html lang="es">', false)->assertSee('Catálogo oficial');
    $this->get(route('tours.show', $tour).'?lang=en')->assertOk()->assertSee('<html lang="en">', false)->assertSee('Book this tour');

    // La elección queda en la sesión.
    $this->get('/')->assertSee('Official catalogue');
    $this->get('/?lang=es')->assertSee('Catálogo oficial');
});

test('RNF-14: el idioma del navegador se respeta si no hay preferencia guardada', function () {
    $this->withHeader('Accept-Language', 'en-GB,en;q=0.9')->get('/')->assertSee('Official catalogue');
});

test('RNF-14: los mensajes de validación salen en español', function () {
    $this->post('/register', ['nom' => '', 'cor' => 'no-es-correo'])
        ->assertSessionHasErrors(['cor' => 'Ingresa un correo electrónico válido.', 'nom' => 'El nombre es obligatorio.']);
    $this->post('/login', ['login' => 'x@y.com', 'password' => 'mala'])->assertSessionHasErrors(['login' => 'El correo o la contraseña no son correctos.']);
});

test('RNF-01: con FORCE_HTTPS las peticiones HTTP se redirigen a HTTPS y llevan HSTS', function () {
    config(['app.force_https' => true]);

    $this->get('http://localhost/tours')->assertRedirect('https://localhost/tours')->assertStatus(301);
    $this->get('https://localhost/')->assertOk()->assertHeader('Strict-Transport-Security');
});

test('RNF-01: sin FORCE_HTTPS (desarrollo) no se redirige y siempre hay cabeceras de seguridad', function () {
    $this->get('/')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeaderMissing('Strict-Transport-Security');
});

test('RNF-12: el comando de respaldo informa el error si pg_dump no está disponible', function () {
    $this->artisan('respaldo:bd', ['--pg-dump' => 'pg_dump_inexistente_xyz'])->assertFailed();
});

test('RNF-02: un turista no accede a ninguna pantalla administrativa', function (string $ruta) {
    $this->actingAs(User::factory()->rol(User::ROL_TURISTA)->create())->get(route($ruta))->assertForbidden();
})->with(['admin.dashboard', 'admin.reservas', 'admin.finanzas', 'admin.analitica', 'admin.io.propuestas', 'admin.io.eoq', 'admin.chatbot', 'admin.tours']);

test('RF-39: los formularios validan formato de nombres, montos, placas y fechas', function () {
    $this->actingAs(User::factory()->rol(User::ROL_OPERACIONES)->create());

    $this->post(route('admin.catalogo.store'), [
        'cat_id' => \App\Models\CategoriaTour::first()->id, 'nom' => '<script>x</script>', 'niv_dif' => 'facil',
        'dur_hrs' => 4, 'pre_bas' => '10.999', 'cap_max' => 10,
    ])->assertSessionHasErrors(['nom', 'pre_bas']);

    $this->post(route('admin.transporte.store'), ['pla' => 'ABC-1234', 'tip' => 'van', 'cap_asi' => 12])->assertSessionHasErrors('pla');
    $this->post(route('admin.transporte.store'), ['pla' => '1234abc', 'tip' => 'van', 'cap_asi' => 12, 'tip_cmb' => 'diesel'])->assertSessionHasNoErrors();
    expect(\App\Models\Vehiculo::where('pla', '1234ABC')->value('est'))->toBe('activo');

    $this->post(route('admin.salidas.store'), ['tur_id' => Tour::first()->id, 'fec_sal' => '2020-01-01', 'hra_sal' => '25:00', 'cap_max' => 10])
        ->assertSessionHasErrors(['fec_sal', 'hra_sal']);
});
