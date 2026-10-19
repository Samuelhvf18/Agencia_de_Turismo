<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ColaMetricaDiaria;
use App\Models\ColaParametroTour;
use App\Models\Tour;
use App\Services\Io\ColasService;
use App\Services\Io\IoDemoData;
use App\Services\Io\PanelColasBuilder;
use Illuminate\Http\Request;

/**
 * Panel «Análisis de colas de reservas» (Módulo 07 — Teoría de colas M/M/c/K).
 */
class AnalisisColasController extends Controller
{
    public function __construct(private ColasService $colas) {}

    public function index(Request $request)
    {
        $datos = $request->validate([
            'tour' => ['nullable', 'integer'],
            'c' => ['nullable', 'integer', 'min:1', 'max:60'],
            'k' => ['nullable', 'integer', 'min:1', 'max:60'],
            'lambda' => ['nullable', 'numeric', 'min:0', 'max:500'],
        ]);

        $parametros = ColaParametroTour::with('tour')->where('act', true)->get()
            ->filter(fn ($p) => $p->tour !== null)
            ->sortBy(fn ($p) => $p->tour->nom)->values();

        if ($parametros->isEmpty()) {
            // Sin parámetros cargados todavía: se muestra la maqueta para no dejar el panel vacío.
            return view('admin.io.colas', array_merge(app(IoDemoData::class)->colas($datos['tour'] ?? null, $datos['c'] ?? null, $datos['k'] ?? null, $datos['lambda'] ?? null),
                ['formAction' => route('admin.io.colas'),
                   'avisoDemo' => 'Todavía no hay parámetros de colas registrados. Ejecute php artisan db:seed --class=IoColasMarkovSeeder para cargarlos por tour.']));
        }

        $hoy = now();
        $tours = $parametros->map(function (ColaParametroTour $p) use ($hoy) {
            $lambda = $this->colas->lambdaObservado($p->tur_id, $hoy->copy()->subDays(29), $hoy);
            $fila = $this->fila($p, $lambda);
            $m = $this->colas->metricas($fila['lambda'], $fila['mu'], $fila['c'], $fila['k']);

            return $fila + ['rho' => $m['rho'], 'lq' => $m['lq'], 'wq_hrs' => $m['wq_hrs'], 'p_rec' => $m['p_rec'], 'estado' => $this->colas->estado($m)];
        })->all();

        $actual = $parametros->firstWhere('tur_id', $datos['tour'] ?? null) ?? $parametros->first();
        $sel = collect($tours)->firstWhere('id', $actual->tur_id);
        $sel['cos_esp'] = (float) ($actual->cos_esp ?? 15);
        $sel['cos_srv'] = (float) ($actual->cos_srv ?? 25);
        if (isset($datos['lambda'])) {
            $sel['lambda'] = (float) $datos['lambda'];
            $sel['origen_lambda'] = 'valor simulado';
        }

        $historial = ColaMetricaDiaria::where('tur_id', $actual->tur_id)
            ->where('fec', '>=', $hoy->copy()->subDays(29)->toDateString())
            ->orderBy('fec')->get()
            ->map(fn ($r) => ['fec' => $r->fec->format('d/m'), 'lmb_obs' => $r->lmb_obs, 'rho' => $r->rho, 'wq_hrs' => $r->wq_hrs, 'p_rec' => $r->p_rec])
            ->all();

        return view('admin.io.colas', PanelColasBuilder::armar($this->colas, $tours, $sel, $datos['c'] ?? null, $datos['k'] ?? null, $historial)
            + ['demo' => false, 'formAction' => route('admin.io.colas')]);
    }

    /** Parámetros de un tour; si hay llegadas registradas en los últimos 30 días se usa λ observado. */
    private function fila(ColaParametroTour $p, ?float $lambdaObs): array
    {
        return [
            'id' => $p->tur_id,
            'nom' => $p->tour->nom,
            'dur_hrs' => (float) $p->tour->dur_hrs,
            'lambda' => $lambdaObs ?? (float) $p->lmb,
            'origen_lambda' => $lambdaObs !== null ? 'observado, últimos 30 días' : 'parámetro configurado',
            'mu' => (float) $p->mu,
            'c' => (int) $p->c_srv,
            'k' => (int) $p->k_cap,
        ];
    }
}
