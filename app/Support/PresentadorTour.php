<?php

namespace App\Support;

use App\Models\Tour;
use Illuminate\Support\Facades\Storage;

/** Prepara tours para vistas públicas. */
class PresentadorTour
{
    // Colores por categoría.
    private const TAGS = [
        'biking' => 't-bike',
        'climbing' => 't-climb',
        'city' => 't-city',
        'trekking' => 't-trek',
        'hiking' => 't-hike',
    ];

    // Barras según dificultad.
    private const DIFICULTAD = [
        'facil' => ['Fácil', 1],    'baja' => ['Fácil', 1],
        'moderado' => ['Moderado', 3], 'media' => ['Moderado', 3],
        'dificil' => ['Difícil', 4],  'alta' => ['Difícil', 4],
        'experto' => ['Experto', 5],
    ];

    // Enriquece datos para la vista.
    public static function presentar(Tour $tour): Tour
    {
        $tour->galeria = $tour->fotos
            ->map(fn ($f) => ['url' => self::urlFoto($f->rta_arc), 'alt' => $f->txt_atv ?: $tour->nom])
            ->filter(fn ($f) => $f['url'])
            ->values();
        $tour->img = $tour->galeria->first()['url'] ?? null;
        $tour->img_alt = $tour->galeria->first()['alt'] ?? $tour->nom;
        $tour->tag = self::TAGS[$tour->categoria->rta_amg] ?? 't-city';
        $tour->dur_txt = rtrim(rtrim(number_format((float) $tour->dur_hrs, 1, '.', ''), '0'), '.');
        [$tour->dif_txt, $tour->dif_niv] = self::DIFICULTAD[$tour->niv_dif] ?? [ucfirst($tour->niv_dif), 3];

        return $tour;
    }

    // Resuelve imágenes públicas o externas.
    public static function urlFoto(?string $ruta): ?string
    {
        if (! $ruta) {
            return null;
        }
        if (preg_match('#^https?://#', $ruta)) {
            return filter_var($ruta, FILTER_VALIDATE_URL) && ! parse_url($ruta, PHP_URL_USER) ? $ruta : null;
        }
        if (str_starts_with($ruta, '/') || str_contains($ruta, '..') || str_contains($ruta, '\\') || str_contains($ruta, ':')) {
            return null;
        }
        if (file_exists(public_path($ruta))) {
            return asset($ruta);
        }
        if (Storage::disk('public')->exists($ruta)) {
            return asset('storage/'.$ruta);
        }

        return null;
    }
}
