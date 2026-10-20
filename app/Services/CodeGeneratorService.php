<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Códigos legibles y únicos (RF-10, RF-36, RNF-07).
 *
 * Cada código se verifica contra la columna UNIQUE de su tabla antes de
 * devolverlo; la restricción UNIQUE de la base es la garantía final.
 * Alfabeto sin caracteres ambiguos (0/O, 1/I/L): 31 símbolos, así
 * RES-XXXXXX admite ~887 millones de combinaciones.
 */
class CodeGeneratorService
{
    private const ALFABETO = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    /** prefijo => [tabla, columna, longitud de la parte aleatoria] */
    private const TIPOS = [
        'RES' => ['reservas', 'cod', 6],
        'BOL' => ['boletos', 'cod', 10],
        'CMP' => ['comprobantes_pago', 'cod', 8],
        'TUR' => ['tours', 'cod', 5],
    ];

    /**
     * Genera un código único para el prefijo dado: RES-7K3QXM, BOL-…, CMP-…,
     * TUR-2026-AB3CD. Para prefijos sin tabla registrada devuelve un código
     * aleatorio sin verificar.
     */
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
