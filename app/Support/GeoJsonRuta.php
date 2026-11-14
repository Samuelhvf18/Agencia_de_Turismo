<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

class GeoJsonRuta
{
    public static function validar(array $geo): array
    {
        $puntos = 0;
        $fallar = fn () => throw ValidationException::withMessages(['trazado' => __('El trazado debe contener líneas GeoJSON válidas con coordenadas geográficas.')]);
        $linea = function ($coords) use (&$puntos, $fallar) {
            if (! is_array($coords) || ! array_is_list($coords) || count($coords) < 2) {
                $fallar();
            }
            foreach ($coords as $c) {
                if (! is_array($c) || ! array_is_list($c) || count($c) < 2 || count($c) > 3) {
                    $fallar();
                }
                foreach ($c as $numero) {
                    if (! is_numeric($numero) || ! is_finite((float) $numero)) {
                        $fallar();
                    }
                }
                if (abs((float) $c[0]) > 180 || abs((float) $c[1]) > 90 || (isset($c[2]) && ((float) $c[2] < -500 || (float) $c[2] > 9000))) {
                    $fallar();
                }
                if (++$puntos > 5000) {
                    $fallar();
                }
            }
        };
        $visitar = function ($g, $profundidad = 0) use (&$visitar, $linea, $fallar) {
            if (! is_array($g) || $profundidad > 3) {
                $fallar();
            }
            switch ($g['type'] ?? '') {
                case 'FeatureCollection':
                    if (empty($g['features']) || ! is_array($g['features']) || count($g['features']) > 100) {
                        $fallar();
                    }
                    foreach ($g['features'] as $f) {
                        if (($f['type'] ?? '') !== 'Feature') {
                            $fallar();
                        } $visitar($f, $profundidad + 1);
                    }
                    break;
                case 'Feature': $visitar($g['geometry'] ?? null, $profundidad + 1);
                    break;
                case 'LineString': $linea($g['coordinates'] ?? null);
                    break;
                case 'MultiLineString':
                    if (empty($g['coordinates']) || ! is_array($g['coordinates'])) {
                        $fallar();
                    }
                    foreach ($g['coordinates'] as $l) {
                        $linea($l);
                    } break;
                default: $fallar();
            }
        };
        $visitar($geo);

        return $geo;
    }
}
