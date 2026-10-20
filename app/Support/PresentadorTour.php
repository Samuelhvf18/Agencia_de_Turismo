<?php

namespace App\Support;

use App\Models\Tour;
use Illuminate\Support\Facades\Storage;

/**
 * Prepara un Tour para las vistas públicas (catálogo y motor de reservas):
 * galería, imagen principal, etiqueta de color y textos de duración y dificultad.
 */
class PresentadorTour
{
    // Clase de color de la etiqueta por categoría (slug)
    private const TAGS = [
        'biking'   => 't-bike',
        'climbing' => 't-climb',
        'city'     => 't-city',
        'trekking' => 't-trek',
        'hiking'   => 't-hike',
    ];

    // Texto y número de barras (de 5) por nivel de dificultad
    private const DIFICULTAD = [
        'facil'    => ['Fácil', 1],    'baja'  => ['Fácil', 1],
        'moderado' => ['Moderado', 3], 'media' => ['Moderado', 3],
        'dificil'  => ['Difícil', 4],  'alta'  => ['Difícil', 4],
        'experto'  => ['Experto', 5],
    ];

    // Agrega al tour los datos ya listos para la vista
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

    // Acepta URL absolutas, archivos en public/ o en el disco "public" de storage
    public static function urlFoto(?string $ruta): ?string
    {
        if (!$ruta) {
            return null;
        }
        if (preg_match('#^https?://#', $ruta)) {
            return $ruta;
        }
        if (file_exists(public_path($ruta))) {
            return asset($ruta);
        }
        if (Storage::disk('public')->exists($ruta)) {
            return asset('storage/' . $ruta);
        }
        return null;
    }
}
