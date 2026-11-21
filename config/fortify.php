<?php

use Laravel\Fortify\Features;

return [

    /* Guardia de Fortify. */

    'guard' => 'web',

    /* Recuperación de contraseñas en Fortify. */

    'passwords' => 'users',

    /* Campos de usuario y correo. */

    // Busca el correo registrado.
    'username' => 'login',
    'email' => 'cor',

    /* Usuario en minúsculas. */

    'lowercase_usernames' => true,

    /* Ruta de inicio. */

    'home' => '/mi-panel',

    'paths' => [
        'login' => '/iniciar-sesion',
        'register' => '/crear-cuenta',
        'logout' => '/cerrar-sesion',
    ],

    /* Prefijo y subdominio de Fortify. */

    'prefix' => '',

    'domain' => null,

    /* Middleware de rutas Fortify. */

    'middleware' => ['web'],

    /* Límites de peticiones. */

    'limiters' => [
        'login' => 'login',
        'passkeys' => 'passkeys',
        'two-factor' => 'two-factor',
    ],

    /* Rutas de vistas. */

    'views' => true,

    /* Llaves de acceso. */

    'passkeys' => [
        'relying_party_id' => parse_url(config('app.url'), PHP_URL_HOST),
        'allowed_origins' => [config('app.url')],
        'timeout' => 60000,
    ],

    /* Funciones disponibles. */

    'features' => [
        Features::registration(),
        Features::passkeys(),
        Features::resetPasswords(),
        Features::emailVerification(),
        Features::updateProfileInformation(),
        Features::updatePasswords(),
        Features::twoFactorAuthentication(['confirm' => true, 'confirmPassword' => true]),
    ],

];
