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

        $paises = [
            'BO' => 'Bolivia', 'AR' => 'Argentina', 'BR' => 'Brasil', 'CL' => 'Chile', 'PE' => 'Perú',
            'CO' => 'Colombia', 'EC' => 'Ecuador', 'PY' => 'Paraguay', 'UY' => 'Uruguay', 'VE' => 'Venezuela',
            'MX' => 'México', 'US' => 'Estados Unidos', 'CA' => 'Canadá', 'ES' => 'España', 'FR' => 'Francia',
            'DE' => 'Alemania', 'IT' => 'Italia', 'GB' => 'Reino Unido', 'NL' => 'Países Bajos', 'CH' => 'Suiza',
            'BE' => 'Bélgica', 'SE' => 'Suecia', 'IL' => 'Israel', 'AU' => 'Australia', 'NZ' => 'Nueva Zelanda',
            'JP' => 'Japón', 'KR' => 'Corea del Sur', 'CN' => 'China', 'IN' => 'India', 'RU' => 'Rusia',
        ];
        foreach ($paises as $iso => $nom) {
            DB::table('paises')->updateOrInsert(['cod_iso' => $iso], ['nom' => $nom]);
        }

        $idiomas = [
            'es' => 'Español', 'en' => 'Inglés', 'pt' => 'Portugués', 'fr' => 'Francés',
            'de' => 'Alemán', 'it' => 'Italiano', 'qu' => 'Quechua', 'ay' => 'Aymara',
        ];
        foreach ($idiomas as $iso => $nom) {
            DB::table('idiomas')->updateOrInsert(['cod_iso' => $iso], ['nom' => $nom]);
        }
    }
}
