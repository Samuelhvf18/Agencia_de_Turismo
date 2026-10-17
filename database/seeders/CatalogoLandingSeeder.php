<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Carga las categorías, tours y fotos que se muestran en el catálogo público
 * de la landing page. Es idempotente: se puede ejecutar varias veces.
 *
 * php artisan db:seed --class=CatalogoLandingSeeder
 */
class CatalogoLandingSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'biking'   => ['nom' => 'Biking',    'ico' => 'fa-solid fa-person-biking', 'des' => 'Descensos de adrenalina en bicicleta.'],
            'hiking'   => ['nom' => 'Hiking',    'ico' => 'fa-solid fa-person-hiking', 'des' => 'Caminos prehispánicos.'],
            'trekking' => ['nom' => 'Trekking',  'ico' => 'fa-solid fa-mountain-sun',  'des' => 'Lagunas glaciares y picos nevados.'],
            'climbing' => ['nom' => 'Climbing',  'ico' => 'fa-solid fa-mountain',      'des' => 'Glaciares y cumbres de la Cordillera Real.'],
            'city'     => ['nom' => 'City Tour', 'ico' => 'fa-solid fa-landmark',      'des' => 'Cultura y patrimonio de La Paz.'],
        ];

        foreach ($categorias as $slug => $cat) {
            DB::table('categorias_tour')->updateOrInsert(['rta_amg' => $slug], $cat);
        }
        $catId = DB::table('categorias_tour')->pluck('id', 'rta_amg');

        $tours = [
            [
                'cod' => 'TUR-2026-LPZ01', 'cat' => 'biking', 'rta_amg' => 'descenso-camino-de-la-muerte',
                'nom' => 'Descenso Camino de la Muerte',
                'des' => 'De La Cumbre a Coroico en bicicleta de doble suspensión con guías expertos.',
                'niv_dif' => 'dificil', 'alt_min_m' => 1200, 'alt_max_m' => 4700, 'dur_hrs' => 7.5,
                'req_fis' => 'Experiencia básica en bicicleta y buena condición física.',
                'pre_bas' => 350, 'cap_max' => 12, 'foto' => 'camino-de-la-muerte.jpg', 'alt' => 'Camino de la Muerte',
            ],
            [
                'cod' => 'TUR-2026-LPZ02', 'cat' => 'climbing', 'rta_amg' => 'glaciar-huayna-potosi',
                'nom' => 'Glaciar Huayna Potosí',
                'des' => 'Iniciación al montañismo glaciar con crampones, piolet y guía de alta montaña.',
                'niv_dif' => 'experto', 'alt_min_m' => 4700, 'alt_max_m' => 5100, 'dur_hrs' => 8,
                'req_fis' => 'Excelente condición física y aclimatación previa a la altura.',
                'pre_bas' => 480, 'cap_max' => 6, 'foto' => 'huayna-potosi.jpg', 'alt' => 'Huayna Potosí',
            ],
            [
                'cod' => 'TUR-2026-LPZ03', 'cat' => 'city', 'rta_amg' => 'valle-de-la-luna-y-telefericos',
                'nom' => 'Valle de la Luna & Teleféricos',
                'des' => 'Formaciones lunares y vista panorámica desde el teleférico urbano más alto del mundo.',
                'niv_dif' => 'facil', 'alt_min_m' => 3300, 'alt_max_m' => 3600, 'dur_hrs' => 4,
                'req_fis' => 'Apto para todo público.',
                'pre_bas' => 180, 'cap_max' => 20, 'foto' => 'valle-de-la-luna.jpg', 'alt' => 'Valle de la Luna',
            ],
            [
                'cod' => 'TUR-2026-LPZ04', 'cat' => 'trekking', 'rta_amg' => 'laguna-chiar-khota-y-condoriri',
                'nom' => 'Laguna Chiar Khota & Condoriri',
                'des' => 'Caminata de altura entre lagunas turquesa y picos nevados de la Cordillera.',
                'niv_dif' => 'moderado', 'alt_min_m' => 4300, 'alt_max_m' => 4500, 'dur_hrs' => 6.5,
                'req_fis' => 'Buena condición física para caminar en altura.',
                'pre_bas' => 260, 'cap_max' => 10, 'foto' => 'condoriri.jpg', 'alt' => 'Condoriri',
            ],
            [
                'cod' => 'TUR-2026-LPZ05', 'cat' => 'hiking', 'rta_amg' => 'camino-prehispanico-el-choro',
                'nom' => 'Camino Prehispánico El Choro',
                'des' => 'Empedrados incas originales de Apacheta a Challapampa con vista al valle subtropical.',
                'niv_dif' => 'moderado', 'alt_min_m' => 2100, 'alt_max_m' => 4200, 'dur_hrs' => 7,
                'req_fis' => 'Buena condición física para descensos prolongados.',
                'pre_bas' => 290, 'cap_max' => 12, 'foto' => 'el-choro.jpg', 'alt' => 'El Choro',
            ],
            [
                'cod' => 'TUR-2026-LPZ06', 'cat' => 'city', 'rta_amg' => 'tiwanaku-y-puma-punku',
                'nom' => 'Tiwanaku & Puma Punku',
                'des' => 'La cuna de la civilización andina con guía arqueológico y la Puerta del Sol.',
                'niv_dif' => 'facil', 'alt_min_m' => 3800, 'alt_max_m' => 3850, 'dur_hrs' => 6,
                'req_fis' => 'Apto para todo público.',
                'pre_bas' => 220, 'cap_max' => 20, 'foto' => 'tiwanaku.jpg', 'alt' => 'Tiwanaku',
            ],
        ];

        foreach ($tours as $t) {
            DB::table('tours')->updateOrInsert(['cod' => $t['cod']], [
                'cat_id'    => $catId[$t['cat']],
                'rta_amg'   => $t['rta_amg'],
                'nom'       => $t['nom'],
                'des'       => $t['des'],
                'niv_dif'   => $t['niv_dif'],
                'alt_min_m' => $t['alt_min_m'],
                'alt_max_m' => $t['alt_max_m'],
                'dur_hrs'   => $t['dur_hrs'],
                'req_fis'   => $t['req_fis'],
                'pre_bas'   => $t['pre_bas'],
                'cap_max'   => $t['cap_max'],
                'act'       => true,
                'eli_en'    => null,
            ]);

            $turId = DB::table('tours')->where('cod', $t['cod'])->value('id');
            DB::table('fotos_tour')->updateOrInsert(
                ['tur_id' => $turId, 'ord_vis' => 1],
                ['rta_arc' => 'images/tours/' . $t['foto'], 'txt_atv' => $t['alt']]
            );
        }
    }
}
