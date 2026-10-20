<?php

namespace Database\Seeders;

use App\Models\CategoriaTour;
use App\Models\Tour;
use App\Models\FotoTour;
use Illuminate\Database\Seeder;

class ToursSystemSeeder extends Seeder
{
    public function run(): void
    {

        $catBiking = CategoriaTour::create([
            'nom' => 'Biking',
            'rta_amg' => 'biking',
            'des' => 'Descensos de adrenalina y rutas en bicicleta.',
            'ico' => 'fa-person-biking'
        ]);

        $catClimbing = CategoriaTour::create([
            'nom' => 'Climbing',
            'rta_amg' => 'climbing',
            'des' => 'Glaciares y cumbres de la Cordillera Real.',
            'ico' => 'fa-mountain'
        ]);

        $tourMuerte = Tour::create([
            'cat_id' => $catBiking->id,
            'cod' => 'TUR-2026-LPZ01',
            'rta_amg' => 'descenso-camino-muerte',
            'nom' => 'Descenso Camino de la Muerte',
            'des' => 'De La Cumbre a Coroico en bicicleta de doble suspensión con guías expertos.',
            'niv_dif' => 'dificil',
            'alt_min_m' => 1200,
            'alt_max_m' => 4700,
            'dur_hrs' => 7.5,
            'pre_bas' => 350.00,
            'cap_max' => 12,
            'act' => true
        ]);

        $tourHuayna = Tour::create([
            'cat_id' => $catClimbing->id,
            'cod' => 'TUR-2026-LPZ02',
            'rta_amg' => 'glaciar-huayna-potosi',
            'nom' => 'Glaciar Huayna Potosí',
            'des' => 'Iniciación al montañismo glaciar con crampones, piolet y guía de alta montaña.',
            'niv_dif' => 'experto',
            'alt_min_m' => 4700,
            'alt_max_m' => 5100,
            'dur_hrs' => 8.0,
            'pre_bas' => 480.00,
            'cap_max' => 6,
            'act' => true
        ]);

        // 3. Crear Fotos (Aquí guardamos las rutas en la BD)
        FotoTour::create([
            'tur_id' => $tourMuerte->id,
            'rta_arc' => 'https://images.unsplash.com/photo-1544198365-f5d60b6d8190?q=80&w=800&auto=format&fit=crop',
            'ord_vis' => 1,
            'txt_atv' => 'Imagen principal Camino de la Muerte'
        ]);

        FotoTour::create([
            'tur_id' => $tourHuayna->id,
            'rta_arc' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=800&auto=format&fit=crop',
            'ord_vis' => 1,
            'txt_atv' => 'Imagen principal Huayna Potosí'
        ]);
    }
}