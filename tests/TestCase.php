<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Omite assets compilados en pruebas.
        $this->withoutVite();

        // Usa el idioma predeterminado.
        $this->withHeader('Accept-Language', 'es-BO,es;q=0.9');
    }
}
