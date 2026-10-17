<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CatalogoController extends Controller
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

    // Landing: solo se publican tours activos que tengan al menos una foto disponible
    public function index()
    {
        $tours = Tour::with(['categoria', 'fotos' => fn ($q) => $q->orderBy('ord_vis')])
            ->where('act', true)
            ->whereNull('eli_en')
            ->orderBy('cod')
            ->get()
            ->map(fn (Tour $tour) => $this->presentar($tour))
            ->filter(fn (Tour $tour) => $tour->img)
            ->values();

        $categorias = $tours->pluck('categoria')->unique('id')->values();

        return view('welcome', compact('tours', 'categorias'));
    }

    // Detalle del tour con ruta amigable: /tours/{rta_amg}
    public function show(Tour $tour)
    {
        abort_unless($tour->act && is_null($tour->eli_en), 404);

        $tour->load(['categoria', 'fotos' => fn ($q) => $q->orderBy('ord_vis')]);
        $this->presentar($tour);

        $puntos = DB::table('puntos_ruta')
            ->where('tur_id', $tour->id)
            ->orderBy('ord_sec')
            ->get(['nom', 'lat', 'lon', 'alt_m', 'tip_pun']);

        return view('tours.show', compact('tour', 'puntos'));
    }

    // Agrega al tour los datos ya listos para la vista
    private function presentar(Tour $tour): Tour
    {
        $tour->galeria = $tour->fotos
            ->map(fn ($f) => ['url' => $this->urlFoto($f->rta_arc), 'alt' => $f->txt_atv ?: $tour->nom])
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
    private function urlFoto(?string $ruta): ?string
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
