<?php

namespace App\Services\Catalogo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Ruta amigable (rta_amg) única con sufijo numérico si ya existe (RF-37). */
class SlugService
{
    public static function unico(string $texto, string $tabla, ?int $ignorarId = null, int $largo = 170): string
    {
        $base = Str::limit(Str::slug($texto), $largo, '') ?: 'tour';
        $slug = $base;
        $n = 2;

        while (DB::table($tabla)->where('rta_amg', $slug)->when($ignorarId, fn ($q) => $q->where('id', '<>', $ignorarId))->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }
}
