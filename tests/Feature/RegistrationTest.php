<?php

use App\Models\User;
use Database\Seeders\RolesSeeder;
use Laravel\Fortify\Features;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

test('new users can register as turista', function () {
    $this->seed(RolesSeeder::class);

    $response = $this->post('/register', [
        'nom' => 'Ana',
        'pat' => 'Quispe',
        'mat' => 'Mamani',
        'cor' => 'ana@example.com',
        'password' => 'Clave2026',
        'password_confirmation' => 'Clave2026',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));

    $user = User::where('cor', 'ana@example.com')->first();
    expect($user->nom)->toBe('Ana Quispe Mamani')
        ->and($user->nombreRol())->toBe(User::ROL_TURISTA);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration rejects weak passwords', function () {
    $this->seed(RolesSeeder::class);

    $this->post('/register', [
        'nom' => 'Ana',
        'pat' => 'Quispe',
        'cor' => 'ana@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('password');

    $this->assertGuest();
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');
