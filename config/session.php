<?php

use Illuminate\Support\Str;

return [

    /* Controlador de sesión predeterminado. */

    'driver' => env('SESSION_DRIVER', 'database'),

    /* Duración de la sesión. */

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /* Cifrado de sesiones. */

    'encrypt' => env('SESSION_ENCRYPT', false),

    /* Ubicación de archivos de sesión. */

    'files' => storage_path('framework/sessions'),

    /* Conexión para almacenar sesiones. */

    'connection' => env('SESSION_CONNECTION'),

    /* Tabla de sesiones. */

    'table' => env('SESSION_TABLE', 'sessions'),

    /* Caché de sesiones. */

    'store' => env('SESSION_STORE'),

    /* Probabilidad de depurar sesiones. */

    'lottery' => [2, 100],

    /* Nombre de la cookie. */

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),

    /* Ruta de la cookie. */

    'path' => env('SESSION_PATH', '/'),

    /* Dominio de la cookie. */

    'domain' => env('SESSION_DOMAIN'),

    /* Cookies solo por HTTPS. */

    'secure' => env('SESSION_SECURE_COOKIE'),

    /* Cookies inaccesibles desde JavaScript. */

    'http_only' => env('SESSION_HTTP_ONLY', true),

    /* Política SameSite de cookies. */

    'same_site' => env('SESSION_SAME_SITE', 'lax'),

    /* Cookies particionadas. */

    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
