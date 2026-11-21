<?php

return [

    /* Nombre de la aplicación. */

    'name' => env('APP_NAME') === 'Pipocas' ? 'Andinist Bolivia' : env('APP_NAME', 'Andinist Bolivia'),

    /* Entorno de la aplicación. */

    'env' => env('APP_ENV', 'production'),

    /* Modo de depuración. */

    'debug' => (bool) env('APP_DEBUG', false),

    /* URL de la aplicación. */

    'url' => env('APP_URL', 'http://localhost'),

    // HTTPS por defecto en producción.
    'force_https' => (bool) env('FORCE_HTTPS', env('APP_ENV') === 'production'),

    /* Zona horaria. */

    'timezone' => 'UTC',

    /* Idioma de la aplicación. */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /* Clave de cifrado. */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /* Modo de mantenimiento. */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
