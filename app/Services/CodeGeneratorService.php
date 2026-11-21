<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Genera códigos legibles únicos. */
class CodeGeneratorService
{
    private const ALFABETO = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    /** Configuración por prefijo. */
    private const TIPOS = [
        'RES' => ['reservas', 'cod', 6],
        'BOL' => ['boletos', 'cod', 10],
        'CMP' => ['comprobantes_pago', 'cod', 8],
        'TUR' => ['tours', 'cod', 5],
    ];

    /** Verifica unicidad para prefijos registrados. */
    public static function generate(string $prefix): string
    {
        [$tabla, $columna, $largo] = self::TIPOS[$prefix] ?? [null, null, 6];

        for ($intento = 0; $intento < 20; $intento++) {
            $codigo = $prefix === 'TUR'
                ? 'TUR-'.date('Y').'-'.self::aleatorio($largo)
                : $prefix.'-'.self::aleatorio($largo);

            if (! $tabla || ! DB::table($tabla)->where($columna, $codigo)->exists()) {
                return $codigo;
            }
        }

        throw new RuntimeException("No se pudo generar un código único para {$prefix}.");
    }

    private static function aleatorio(int $largo): string
    {
        $max = strlen(self::ALFABETO) - 1;
        $codigo = '';
        for ($i = 0; $i < $largo; $i++) {
            $codigo .= self::ALFABETO[random_int(0, $max)];
        }

        return $codigo;
    }
}
