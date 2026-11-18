<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTour;
use App\Models\PerfilGuia;
use App\Models\Tour;
use App\Services\Catalogo\ConsultaCatalogo;
use App\Services\Catalogo\RecomendadorTours;
use App\Support\PresentadorTour;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    // Landing: solo se publican tours activos que tengan al menos una foto disponible
    public function index()
    {
        $filtros = request()->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'fecha' => ['nullable', 'date_format:Y-m-d'],
            'categoria' => ['nullable', 'string', 'max:100'],
        ]);
        $tours = app(ConsultaCatalogo::class)->buscar($filtros);

        $categorias = CategoriaTour::whereHas('tours', fn ($q) => $q->where('act', true)->whereNull('eli_en'))->orderBy('nom')->get();

        $recomendaciones = app(RecomendadorTours::class)->para(request());

        return view('welcome', compact('tours', 'categorias', 'recomendaciones'));
    }

    public function categoria(CategoriaTour $categoria)
    {
        $tours = $categoria->tours()->with('categoria', 'fotos')->where('act', true)->whereNull('eli_en')->get()->map(fn (Tour $t) => PresentadorTour::presentar($t));

        return view('tours.categoria', compact('categoria', 'tours'));
    }

    public function guia(PerfilGuia $guia)
    {
        abort_unless($guia->act && $guia->usuario?->act, 404);

        return view('tours.guia', ['nombre' => $guia->usuario->nom,
            'bio' => $guia->bio, 'experiencia' => $guia->ani_exp,
            'idiomas' => $guia->idiomas()->pluck('nom')]);
    }

    // Detalle del tour con ruta amigable: /tours/{rta_amg}
    public function show(Tour $tour)
    {
        abort_unless($tour->act && is_null($tour->eli_en), 404);

        $vistos = array_values(array_diff((array) session('catalogo.vistos', []), [$tour->id]));
        $vistos[] = $tour->id;
        session(['catalogo.vistos' => array_slice($vistos, -20)]);

        $tour->load(['categoria', 'fotos' => fn ($q) => $q->orderBy('ord_vis')]);
        PresentadorTour::presentar($tour);

        $puntos = DB::table('puntos_ruta')
            ->where('tur_id', $tour->id)
            ->orderBy('ord_sec')
            ->get(['nom', 'lat', 'lon', 'alt_m', 'tip_pun']);

        // Reseñas verificadas y promedio desde vista_calificacion_promedio_tour (RF-32).
        $calificacion = DB::table('vista_calificacion_promedio_tour')->where('tur_id', $tour->id)->first();
        $resenas = $tour->resenas()->whereNotNull('rsv_id')->orderByDesc('cre_en')->limit(6)->get();

        return view('tours.show', compact('tour', 'puntos', 'calificacion', 'resenas'));
    }
}
