<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Support\PresentadorTour;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    // Landing: solo se publican tours activos que tengan al menos una foto disponible
    public function index()
    {
        $tours = Tour::with(['categoria', 'fotos' => fn ($q) => $q->orderBy('ord_vis')])
            ->where('act', true)
            ->whereNull('eli_en')
            ->orderBy('cod')
            ->get()
            ->map(fn (Tour $tour) => PresentadorTour::presentar($tour))
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
        PresentadorTour::presentar($tour);

        $puntos = DB::table('puntos_ruta')
            ->where('tur_id', $tour->id)
            ->orderBy('ord_sec')
            ->get(['nom', 'lat', 'lon', 'alt_m', 'tip_pun']);

        return view('tours.show', compact('tour', 'puntos'));
    }
}
