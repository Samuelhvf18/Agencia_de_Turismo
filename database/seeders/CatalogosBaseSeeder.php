<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Catálogos generales: departamentos de Bolivia (complemento del CI),
 * países (nacionalidad de los turistas) e idiomas (guías y chatbot).
 * Es idempotente.
 */
class CatalogosBaseSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            'LP' => 'La Paz', 'CB' => 'Cochabamba', 'SC' => 'Santa Cruz', 'OR' => 'Oruro', 'PT' => 'Potosí',
            'CH' => 'Chuquisaca', 'TJ' => 'Tarija', 'BE' => 'Beni', 'PD' => 'Pando',
        ];
        foreach ($departamentos as $cod => $nom) {
            DB::table('departamentos_bolivia')->updateOrInsert(['cod' => $cod], ['nom' => $nom]);
        }

        $paises = config('catalogos.paises');
        foreach ($paises as $iso => $nom) {
            DB::table('paises')->updateOrInsert(['cod_iso' => $iso], ['nom' => $nom]);
        }

        $idiomas = config('catalogos.idiomas');
        foreach ($idiomas as $iso => $nom) {
            DB::table('idiomas')->updateOrInsert(['cod_iso' => $iso], ['nom' => $nom]);
        }
    }
}
