<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Las vistas usan @vite; en las pruebas no se necesitan los assets compilados.
        $this->withoutVite();
    }
}
