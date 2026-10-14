<?php

namespace App\Services;

class CodeGeneratorService
{
    /**
     * Genera un código alfanumérico único para tours, reservas, facturas o tickets.
     * Ejemplo: TUR-2026-LPZ01, RES-88A9F2, FAC-99B2C1
     */
    public static function generate(string $prefix): string
    {
        $randomHash = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        $year = date('Y');

        if ($prefix === 'TUR') {
            return "TUR-{$year}-LPZ" . mt_rand(10, 99);
        }

        return "{$prefix}-{$randomHash}";
    }
}
