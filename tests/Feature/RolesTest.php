<?php

use App\Models\BitacoraAuditoria;
use App\Models\User;

test('cada rol es redirigido a su inicio tras iniciar sesión', function (string $rol, string $ruta) {
    $user = User::factory()->rol($rol)->create();

    $this->post('/login', ['login' => $user->cor, 'password' => 'password'])
        ->assertRedirect(route($ruta, absolute: false));
})->with([
    'admin' => [User::ROL_ADMIN, 'admin.dashboard'],
    'operaciones' => [User::ROL_OPERACIONES, 'admin.dashboard'],
    'guia' => [User::ROL_GUIA, 'guias.checkin'],
    'chofer' => [User::ROL_CHOFER, 'guias.checkin'],
    'turista' => [User::ROL_TURISTA, 'home'],
]);

test('un invitado no puede entrar al panel administrativo', function () {
    $this->get('/admin/dashboard')->assertRedirect(route('login', absolute: false));
    $this->get('/guias/checkin')->assertRedirect(route('login', absolute: false));
});

test('el panel administrativo es solo para admin y operaciones', function (string $rol, int $estado) {
    $this->actingAs(User::factory()->rol($rol)->create())
        ->get('/admin/dashboard')
        ->assertStatus($estado);
})->with([
    'admin' => [User::ROL_ADMIN, 200],
    'operaciones' => [User::ROL_OPERACIONES, 200],
    'guia' => [User::ROL_GUIA, 403],
    'chofer' => [User::ROL_CHOFER, 403],
    'turista' => [User::ROL_TURISTA, 403],
]);

test('la auditoría es solo para el administrador', function () {
    $this->actingAs(User::factory()->rol(User::ROL_OPERACIONES)->create())
        ->get('/admin/auditoria')
        ->assertForbidden();

    $this->actingAs(User::factory()->rol(User::ROL_ADMIN)->create())
        ->get('/admin/auditoria')
        ->assertOk();
});

test('el check-in es para guías y choferes, no para turistas', function () {
    $this->actingAs(User::factory()->rol(User::ROL_GUIA)->create())
        ->get('/guias/checkin')
        ->assertOk();

    $this->actingAs(User::factory()->rol(User::ROL_TURISTA)->create())
        ->get('/guias/checkin')
        ->assertForbidden();
});

test('una cuenta desactivada no puede iniciar sesión', function () {
    $user = User::factory()->create(['act' => false]);

    $this->post('/login', ['login' => $user->cor, 'password' => 'password']);

    $this->assertGuest();
});

test('la bitácora registra los cambios sin copiar la contraseña', function () {
    $user = User::factory()->create();
    $user->update(['tel' => '70000000']);

    $creacion = BitacoraAuditoria::where('nom_tbl', 'users')->where('id_reg', $user->id)->where('acc', 'CREATE')->first();
    $cambio = BitacoraAuditoria::where('nom_tbl', 'users')->where('id_reg', $user->id)->where('acc', 'UPDATE')->first();

    expect($creacion->val_nvo)->not->toHaveKey('clv')
        ->and($cambio->val_ant)->toHaveKey('tel')
        ->and($cambio->val_nvo['tel'])->toBe('70000000');
});

test('todas las pantallas del panel cargan para el administrador', function (string $ruta) {
    $this->actingAs(User::factory()->rol(User::ROL_ADMIN)->create())
        ->get(route($ruta))
        ->assertOk();
})->with([
    'admin.dashboard', 'admin.operaciones', 'admin.inventario', 'admin.transporte', 'admin.personal',
    'admin.tours', 'admin.reservas', 'admin.finanzas', 'admin.analitica', 'admin.auditoria',
    'admin.io.colas', 'admin.io.ciclo-vida',
]);
