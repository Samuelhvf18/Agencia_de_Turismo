<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/* Configuración de pruebas. */

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/* Expectativas personalizadas. */

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/* Funciones auxiliares. */

function something()
{
    // ..
}
