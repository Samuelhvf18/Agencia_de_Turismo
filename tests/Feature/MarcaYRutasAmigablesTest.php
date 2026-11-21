<?php

use App\Models\PerfilGuia;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\User;
use App\Models\Vehiculo;
use Database\Seeders\CatalogoLandingSeeder;
use Database\Seeders\CatalogosBaseSeeder;
use Database\Seeders\OperacionDemoSeeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\UsuariosDemoSeeder;

beforeEach(function () {
    $this->seed([RolesSeeder::class, CatalogosBaseSeeder::class, UsuariosDemoSeeder::class, CatalogoLandingSeeder::class, OperacionDemoSeeder::class]);
});

it('presenta la marca y separa las palabras del título en ambos idiomas', function () {
    $this->get(route('home'))->assertOk()
        ->assertSee('Andinist Bolivia')
        ->assertSee('con la aventura <em>en el corazón de Bolivia</em>', false)
        ->assertDontSee('Demostración: pagos simulados')
        ->assertDontSee('Pipocas');

    $this->get(route('home', ['lang' => 'en']))->assertOk()
        ->assertSee('Travel Agency')
        ->assertSee('with adventure <em>in the heart of Bolivia</em>', false);
});

it('publica URLs en español con acceso compatible y protegido', function () {
    expect(route('login', absolute: false))->toBe('/iniciar-sesion')
        ->and(route('register', absolute: false))->toBe('/crear-cuenta')
        ->and(route('logout', absolute: false))->toBe('/cerrar-sesion');

    foreach (['/iniciar-sesion', '/login', '/crear-cuenta', '/register', '/asistente-turistico', '/chatbot'] as $ruta) {
        $this->get($ruta)->assertOk();
    }

    foreach (['/admin/panel', '/admin/dashboard', '/guias/mis-salidas', '/guias/checkin'] as $ruta) {
        $this->get($ruta)->assertRedirect(route('login', absolute: false));
    }
});

it('genera enlaces para el tour, el vehículo y la salida a partir de sus datos', function () {
    $tour = Tour::firstOrFail();
    $vehiculo = Vehiculo::firstOrFail();
    $salida = SalidaTour::firstOrFail();

    expect(route('admin.catalogo.edit', $tour, false))->toBe("/admin/catalogo/tours/{$tour->rta_amg}/editar")
        ->and(route('admin.transporte.update', $vehiculo, false))->toBe("/admin/transporte/{$vehiculo->pla}")
        ->and(route('admin.salidas.edit', $salida, false))->toBe("/admin/salidas/{$salida->fec_sal->toDateString()}-{$salida->id}/editar");

    $this->actingAs(User::factory()->rol(User::ROL_ADMIN)->create());
    foreach ([route('admin.catalogo.edit', $tour), "/admin/catalogo/tours/{$tour->id}", route('admin.salidas.edit', $salida), "/admin/salidas/{$salida->id}"] as $ruta) {
        $this->get($ruta)->assertOk();
    }
    $this->get('/admin/salidas/fecha-invalida/editar')->assertNotFound();

    $guia = PerfilGuia::firstOrFail();
    $salida->update(['prf_gui_id' => $guia->id]);
    $this->flushSession();
    $this->actingAs($guia->usuario);
    $this->get(route('guias.salida', $salida))->assertOk();
    $this->get("/guias/salidas/{$salida->id}")->assertOk();
});
