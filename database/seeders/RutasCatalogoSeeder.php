<?php

namespace Database\Seeders;

use App\Models\PuntoRuta;
use App\Models\Tour;
use Illuminate\Database\Seeder;

/**
 * Puntos de interés y trazado (GeoJSON en tours.trz_geo) de los tours del
 * catálogo. Coordenadas aproximadas de cada recorrido. Idempotente.
 */
class RutasCatalogoSeeder extends Seeder
{
    /** slug => [[nom, lat, lon, alt_m, tip_pun], ...] en orden de recorrido */
    private const RUTAS = [
        'descenso-camino-de-la-muerte' => [
            ['La Cumbre', -16.3540, -68.0420, 4700, 'inicio'],
            ['Unduavi', -16.3050, -67.9000, 3300, 'checkpoint'],
            ['Chuspipata', -16.2930, -67.8130, 3250, 'mirador'],
            ['San Juan', -16.2700, -67.7800, 2000, 'checkpoint'],
            ['Yolosa', -16.2290, -67.7490, 1200, 'fin'],
        ],
        'glaciar-huayna-potosi' => [
            ['Paso Zongo', -16.2700, -68.1300, 4700, 'inicio'],
            ['Campo glaciar', -16.2650, -68.1400, 4850, 'checkpoint'],
            ['Mirador del glaciar', -16.2620, -68.1450, 5100, 'mirador'],
            ['Retorno a Zongo', -16.2705, -68.1305, 4700, 'fin'],
        ],
        'valle-de-la-luna-y-telefericos' => [
            ['Estación Central del teleférico', -16.4897, -68.1450, 3600, 'inicio'],
            ['Mirador Killi Killi', -16.4950, -68.1250, 3550, 'mirador'],
            ['Mallasa', -16.5600, -68.0950, 3350, 'checkpoint'],
            ['Valle de la Luna', -16.5680, -68.0940, 3300, 'fin'],
        ],
        'laguna-chiar-khota-y-condoriri' => [
            ['Represa de Tuni', -16.0300, -68.2300, 4300, 'inicio'],
            ['Pampa de Condoriri', -16.0150, -68.2350, 4400, 'checkpoint'],
            ['Laguna Chiar Khota', -16.0000, -68.2400, 4500, 'mirador'],
            ['Retorno a Tuni', -16.0305, -68.2305, 4300, 'fin'],
        ],
        'camino-prehispanico-el-choro' => [
            ['Abra de La Cumbre', -16.3540, -68.0420, 4200, 'inicio'],
            ['Apacheta Chucura', -16.3400, -68.0200, 4100, 'mirador'],
            ['Chucura', -16.3100, -67.9800, 3600, 'checkpoint'],
            ['Challapampa', -16.2400, -67.9200, 2100, 'fin'],
        ],
        'tiwanaku-y-puma-punku' => [
            ['Museo Lítico', -16.5560, -68.6720, 3845, 'inicio'],
            ['Pirámide de Akapana', -16.5555, -68.6735, 3850, 'mirador'],
            ['Puerta del Sol', -16.5545, -68.6745, 3848, 'checkpoint'],
            ['Puma Punku', -16.5617, -68.6800, 3840, 'fin'],
        ],
    ];

    public function run(): void
    {
        foreach (self::RUTAS as $slug => $puntos) {
            $tour = Tour::where('rta_amg', $slug)->first();
            if (! $tour) {
                continue;
            }

            foreach ($puntos as $i => [$nom, $lat, $lon, $alt, $tip]) {
                PuntoRuta::updateOrCreate(
                    ['tur_id' => $tour->id, 'ord_sec' => $i + 1],
                    ['nom' => $nom, 'lat' => $lat, 'lon' => $lon, 'alt_m' => $alt, 'tip_pun' => $tip]
                );
            }

            // Trazado GeoJSON con altitud ([lon, lat, alt]) interpolado entre los puntos.
            if (empty($tour->trz_geo)) {
                $tour->update(['trz_geo' => [
                    'type' => 'LineString',
                    'coordinates' => $this->interpolar($puntos, 6),
                ]]);
            }
        }
    }

    private function interpolar(array $puntos, int $pasos): array
    {
        $coords = [];
        for ($i = 0; $i < count($puntos) - 1; $i++) {
            [, $lat1, $lon1, $alt1] = $puntos[$i];
            [, $lat2, $lon2, $alt2] = $puntos[$i + 1];
            for ($k = 0; $k < $pasos; $k++) {
                $t = $k / $pasos;
                $coords[] = [round($lon1 + ($lon2 - $lon1) * $t, 6), round($lat1 + ($lat2 - $lat1) * $t, 6), (int) round($alt1 + ($alt2 - $alt1) * $t)];
            }
        }
        [, $lat, $lon, $alt] = end($puntos);
        $coords[] = [$lon, $lat, $alt];

        return $coords;
    }
}
