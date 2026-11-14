<?php

namespace App\Http\Controllers;

use App\Models\CategoriaTour;
use App\Models\PerfilGuia;
use App\Models\Tour;
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
        $tours = Tour::with(['categoria', 'fotos' => fn ($q) => $q->orderBy('ord_vis')])
            ->where('act', true)
            ->whereNull('eli_en')
            ->when($filtros['q'] ?? null, fn ($q, $texto) => $q->where(function ($q) use ($texto) {
                $q->where('nom', 'ILIKE', '%'.$texto.'%')->orWhere('cod', 'ILIKE', '%'.$texto.'%');
            }))
            ->when(($filtros['categoria'] ?? 'todos') !== 'todos', fn ($q) => $q->whereHas('categoria', fn ($c) => $c->where('rta_amg', $filtros['categoria'])))
            ->when($filtros['fecha'] ?? null, fn ($q, $fecha) => $q->whereHas('salidas', fn ($s) => $s
                ->whereDate('fec_sal', $fecha)->whereDate('fec_sal', '>=', today())->where('est', 'programada')
                ->whereRaw("cap_max > (SELECT COALESCE(SUM(cnt_ptc), 0) FROM reservas WHERE sal_tur_id = salidas_tour.id AND est <> 'cancelada')")))
            ->orderBy('cod')
            ->get()
            ->map(fn (Tour $tour) => PresentadorTour::presentar($tour))
            ->filter(fn (Tour $tour) => $tour->img)
            ->values();

        $categorias = CategoriaTour::whereHas('tours', fn ($q) => $q->where('act', true)->whereNull('eli_en'))->orderBy('nom')->get();

        return view('welcome', compact('tours', 'categorias'));
    }

    public function categoria(CategoriaTour $categoria)
    {
        $tours = $categoria->tours()->where('act', true)->whereNull('eli_en')->get();

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
