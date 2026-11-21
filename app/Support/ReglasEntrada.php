<?php

namespace App\Support;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Reglas de entrada compartidas. */
class ReglasEntrada
{
    public const NOMBRE = 'regex:/^[\p{L}\p{M}]+(?: +[\p{L}\p{M}]+)*$/u';

    public static function nombre(int $max = 150): array
    {
        return ['required', 'string', 'max:'.$max, self::NOMBRE];
    }

    public static function correo(): array
    {
        return ['required', 'string', 'email:rfc', 'max:150'];
    }

    public static function clave(bool $confirmada = true): array
    {
        return array_filter(['required', 'string', 'max:128', Password::min(8)->letters()->numbers(), $confirmada ? 'confirmed' : null]);
    }

    public static function telefono(string $campo = 'tel', bool $obligatorio = false): array
    {
        return [
            $campo => [$obligatorio ? 'required' : 'nullable', 'string', 'regex:/^\+?[1-9][0-9]{6,14}$/'],
            $campo.'_prefijo' => ['sometimes', 'required', 'string', Rule::in(array_keys(config('turismo.prefijos')))],
            $campo.'_numero' => [$obligatorio ? 'required_with:'.$campo.'_prefijo' : 'nullable', 'string', 'regex:/^[0-9]{4,14}$/'],
        ];
    }

    public static function componerTelefono(array $datos, string $campo = 'tel'): array
    {
        if (array_key_exists($campo.'_prefijo', $datos)) {
            $numero = $datos[$campo.'_numero'] ?? '';
            $datos[$campo] = $numero === '' || $numero === null ? null
                : (is_string($numero) && is_string($datos[$campo.'_prefijo']) ? $datos[$campo.'_prefijo'].$numero : $numero);
        }

        return $datos;
    }

    public static function imagen(int $maxKb = 4096): array
    {
        return ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp',
            'max:'.$maxKb, 'dimensions:min_width=100,min_height=100,max_width=6000,max_height=6000'];
    }
}
