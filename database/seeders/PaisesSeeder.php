<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaisesSeeder extends Seeder
{
    public function run(): void
    {
        $paises = [
            ['nom' => 'Bolivia', 'cod_iso' => 'BO'],
            ['nom' => 'Argentina', 'cod_iso' => 'AR'],
            ['nom' => 'Brasil', 'cod_iso' => 'BR'],
            ['nom' => 'Chile', 'cod_iso' => 'CL'],
            ['nom' => 'Perú', 'cod_iso' => 'PE'],
            ['nom' => 'Colombia', 'cod_iso' => 'CO'],
            ['nom' => 'Uruguay', 'cod_iso' => 'UY'],
            ['nom' => 'Paraguay', 'cod_iso' => 'PY'],
            ['nom' => 'Estados Unidos', 'cod_iso' => 'US'],
            ['nom' => 'Canadá', 'cod_iso' => 'CA'],
            ['nom' => 'España', 'cod_iso' => 'ES'],
            ['nom' => 'Francia', 'cod_iso' => 'FR'],
            ['nom' => 'Alemania', 'cod_iso' => 'DE'],
            ['nom' => 'Reino Unido', 'cod_iso' => 'GB'],
            ['nom' => 'Italia', 'cod_iso' => 'IT'],
            ['nom' => 'Suiza', 'cod_iso' => 'CH'],
            ['nom' => 'Australia', 'cod_iso' => 'AU'],
            ['nom' => 'Japón', 'cod_iso' => 'JP'],
        ];

        foreach ($paises as $pais) {
            DB::table('paises')->updateOrInsert(
                ['cod_iso' => $pais['cod_iso']],
                ['nom' => $pais['nom']]
            );
        }
    }
}
