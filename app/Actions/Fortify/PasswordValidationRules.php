<?php

namespace App\Actions\Fortify;

use App\Support\ReglasEntrada;
use Illuminate\Contracts\Validation\Rule;

trait PasswordValidationRules
{
    /**
     * Reglas de contraseñas.
     *
     * @return array<int, Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return ReglasEntrada::clave();
    }
}
