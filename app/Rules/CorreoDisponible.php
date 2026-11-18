<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CorreoDisponible implements ValidationRule
{
    public function __construct(private ?int $ignorarId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && User::whereRaw('LOWER(cor) = ?', [mb_strtolower(trim($value))])
            ->when($this->ignorarId, fn ($q, $id) => $q->whereKeyNot($id))->exists()) {
            $fail('Este correo electrónico ya está registrado.');
        }
    }
}
