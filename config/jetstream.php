<?php

use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;

return [

    /* Pila de Jetstream. */

    'stack' => 'livewire',

    /* Middleware de rutas Jetstream. */

    'middleware' => ['web'],

    'auth_session' => AuthenticateSession::class,

    /* Guardia de Jetstream. */

    'guard' => 'sanctum',

    /* Funciones disponibles. */

    'features' => [
        // Features::termsAndPrivacyPolicy(),
        Features::profilePhotos(),
        Features::api(),
        // Features::teams(['invitations' => true]),
        Features::accountDeletion(),
    ],

    /* Almacenamiento de fotos de perfil. */

    'profile_photo_disk' => 'public',

];
