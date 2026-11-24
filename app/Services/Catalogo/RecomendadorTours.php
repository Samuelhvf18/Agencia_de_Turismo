<?php

namespace App\Services\Catalogo;

use App\Models\MarkovClienteEstado;
use App\Models\Reserva;
use App\Models\Tour;
use App\Models\User;
use App\Services\Io\MarkovService;
use App\Support\PresentadorTour;
use Illuminate\Http\Request;

/** Markov ajusta recomendaciones. */
class RecomendadorTours
{
    public function registrarConsulta(Request $request, Tour $tour): void
    {
        $usuario = $request->user();
        if (! $usuario?->tieneRol(User::ROL_TURISTA)) {
            return;
        }

        $clave = "catalogo.consultas.{$usuario->id}";
        $vistos = array_values(array_diff($request->session()->get($clave, []), [$tour->id]));
        $vistos[] = $tour->id;
        $request->session()->put($clave, array_slice($vistos, -20));
    }

    public function para(Request $request): array
    {
        $usuario = $request->user();
        if (! $usuario?->tieneRol(User::ROL_TURISTA)) {
            return ['tours' => collect(), 'mensaje' => ''];
        }
        $reservas = $usuario->reservas()->with('salida.tour')
            ->where('est', '<>', Reserva::EST_CANCELADA)->get();
        $vistos = Tour::whereIn('id', $request->session()->get("catalogo.consultas.{$usuario->id}", []))->get();
        $categorias = [];
        foreach ($vistos as $tour) {
            $categorias[$tour->cat_id] = ($categorias[$tour->cat_id] ?? 0) + 1;
        }
        foreach ($reservas as $reserva) {
            $cat = $reserva->salida->tour->cat_id;
            $categorias[$cat] = ($categorias[$cat] ?? 0) + ($reserva->est === Reserva::EST_COMPLETADA ? 5 : 3);
        }

        if ($categorias === []) {
            return ['tours' => collect(), 'mensaje' => ''];
        }

        $estado = MarkovClienteEstado::with('estado')->where('usr_id', $usuario->id)
            ->whereDate('fec_cmb', '<=', today())->orderByDesc('fec_cmb')->orderByDesc('id')->first()?->estado?->cod;
        $completadas = $reservas->where('est', Reserva::EST_COMPLETADA)->count();
        $estado ??= $completadas >= 2 ? 'fidelizado' : ($completadas ? 'completado' : ($reservas->isNotEmpty() ? 'reservado' : 'prospecto'));
        $markov = app(MarkovService::class);
        $matriz = $markov->matrizVigente()['P'];
        // Ignora matrices inválidas.
        $valida = $markov->esEstocastica($matriz) && collect($matriz)->flatten()->every(fn ($p) => is_finite($p) && $p >= 0 && $p <= 1);
        $riesgo = $valida ? ($matriz[$estado]['perdido'] ?? 0) : 0;
        $recuperar = $estado === 'perdido' || $riesgo >= 0.30;
        $reservados = $reservas->filter(fn ($r) => $r->salida->fec_sal->gte(today()) && $r->salida->est === 'programada')
            ->pluck('salida.tur_id')->all();
        $hechos = $reservas->where('est', Reserva::EST_COMPLETADA)->pluck('salida.tur_id')->all();

        $tours = Tour::with('categoria', 'fotos')->where('act', true)->whereNull('eli_en')
            ->whereNotIn('id', $reservados)
            ->whereHas('salidas', fn ($q) => $q->where('est', 'programada')->whereDate('fec_sal', '>=', today())
                ->whereRaw("cap_max > (SELECT COALESCE(SUM(cnt_ptc), 0) FROM reservas WHERE sal_tur_id = salidas_tour.id AND est <> 'cancelada')"))
            ->orderBy('cod')->get()->map(fn ($t) => PresentadorTour::presentar($t))->filter(fn ($t) => $t->img);
        $tours = $tours->sortByDesc(function ($t) use ($categorias, $recuperar, $hechos, $estado) {
            $afinidad = ($categorias[$t->cat_id] ?? 0) * 10;
            $nuevo = ! in_array($t->id, $hechos, true) ? ($estado === 'fidelizado' ? 25 : 8) : 0;
            $facilidad = $recuperar ? (6 - $t->dif_niv) * 8 : 0;

            return $afinidad + $nuevo + $facilidad;
        })->take(3)->values();
        $tours->each(function ($tour) use ($categorias, $recuperar) {
            $tour->motivo_recomendacion = isset($categorias[$tour->cat_id])
                ? 'Relacionado con los tours que consultaste o reservaste'
                : ($recuperar ? 'Una opción para volver a viajar a tu ritmo' : 'Una nueva experiencia para descubrir');
        });

        return ['tours' => $tours, 'mensaje' => $recuperar ? 'Retoma tus planes con estas experiencias.'
            : 'Ideas basadas en tus consultas y reservas.'];
    }
}
