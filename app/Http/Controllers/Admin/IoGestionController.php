<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ColaParametroTour;
use App\Models\PerfilGuia;
use App\Models\PropuestaAsignacion;
use App\Models\Tour;
use App\Models\Vehiculo;
use App\Services\Io\ColasService;
use App\Services\Io\EoqService;
use App\Services\Io\OptimizadorService;
use App\Services\Notificaciones\NotificacionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Pantallas de gestión de IO II: parámetros de colas (RF-40), propuestas de
 * asignación de guías y vehículos (RF-22 a RF-24) y EOQ (RF-25 a RF-27).
 */
class IoGestionController extends Controller
{
    // ---- RF-40: parámetros M/M/c/K -------------------------------------------------------

    public function parametros(ColasService $colas): View
    {
        $tours = Tour::where('act', true)->orderBy('nom')->get();
        $activos = ColaParametroTour::where('act', true)->get()->keyBy('tur_id');

        return view('admin.io.parametros', [
            'tours' => $tours->map(fn (Tour $t) => [
                'tour' => $t,
                'par' => $activos[$t->id] ?? null,
                'mu_sugerido' => $colas->muDesdeDuracion((float) $t->dur_hrs),
                'lambda_obs' => $colas->lambdaObservado($t->id, now()->subDays(29), now()),
            ]),
            'historial' => ColaParametroTour::with('tour')->orderByDesc('cre_en')->limit(15)->get(),
        ]);
    }

    public function guardarParametros(Request $request, Tour $tour): RedirectResponse
    {
        $datos = $request->validate([
            'lmb' => ['required', 'numeric', 'min:0', 'max:9999'],
            'mu' => ['required', 'numeric', 'gt:0', 'max:9999'],
            'c_srv' => ['required', 'integer', 'min:1', 'max:60'],
            'k_cap' => ['required', 'integer', 'gte:c_srv', 'max:200'],
            'cos_esp' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'cos_srv' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'obs' => ['nullable', 'string', 'max:500'],
        ], [
            'mu.gt' => 'La atención por guía debe ser mayor que cero.',
            'c_srv.min' => 'Debes configurar al menos un guía.',
            'k_cap.gte' => 'La capacidad del modelo debe ser igual o mayor que el número de guías.',
        ]);

        // Un solo juego activo por tour: el anterior queda como historial.
        DB::transaction(function () use ($tour, $datos) {
            Tour::whereKey($tour->id)->lockForUpdate()->firstOrFail();
            ColaParametroTour::where('tur_id', $tour->id)->where('act', true)->update(['act' => false]);
            ColaParametroTour::create($datos + ['tur_id' => $tour->id, 'fec_cal' => today(), 'act' => true]);
        });

        return back()->with('estado', "Configuración de {$tour->nom} guardada. El análisis usará estos valores; las asignaciones y los cupos de las salidas no cambiaron.");
    }

    // ---- RF-22 a RF-24: propuestas de asignación ----------------------------------------------

    public function propuestas(Request $request): View
    {
        $tip = in_array($request->input('tip'), ['guia', 'vehiculo'], true) ? $request->input('tip') : null;

        $pendientes = PropuestaAsignacion::with('salida.tour', 'salida.guia.usuario', 'salida.vehiculo')
            ->where('est', PropuestaAsignacion::EST_PENDIENTE)
            ->when($tip, fn ($q) => $q->where('tip', $tip))
            ->whereHas('salida', fn ($q) => $q->where('est', 'programada')->whereDate('fec_sal', '>=', today()))
            ->orderBy(DB::raw('(SELECT fec_sal FROM salidas_tour WHERE salidas_tour.id = propuestas_asignacion.sal_tur_id)'))
            ->get();

        return view('admin.io.propuestas', [
            'pendientes' => $pendientes,
            'revisadas' => PropuestaAsignacion::with('salida.tour', 'revisor')->where('est', '<>', PropuestaAsignacion::EST_PENDIENTE)->orderByDesc('rev_en')->limit(15)->get(),
            'guias' => PerfilGuia::with('usuario')->where('act', true)->get()->sortBy('usuario.nom'),
            'vehiculos' => Vehiculo::where('est', Vehiculo::EST_ACTIVO)->orderBy('pla')->get(),
            'tip' => $tip,
        ]);
    }

    public function generarPropuestas(Request $request, OptimizadorService $optimizador): RedirectResponse
    {
        $datos = $request->validate([
            'tip' => ['required', 'in:guia,vehiculo'],
            'desde' => ['required', 'date', 'after_or_equal:today'],
            'hasta' => ['required', 'date', 'after_or_equal:desde'],
            'solo_sin_asignar' => ['nullable', 'boolean'],
        ]);
        $desde = Carbon::parse($datos['desde']);
        $hasta = Carbon::parse($datos['hasta']);
        $solo = $request->boolean('solo_sin_asignar', true);

        $r = $datos['tip'] === 'guia'
            ? $optimizador->proponerGuias($desde, $hasta, $solo)
            : $optimizador->proponerVehiculos($desde, $hasta, $solo);

        return redirect()->route('admin.io.propuestas', ['tip' => $datos['tip']])
            ->with('estado', "Se generaron {$r['propuestas']} propuestas. En {$r['sin_solucion']} salidas no se encontró un recurso disponible que cumpla las condiciones. Revisa las propuestas antes de confirmarlas.");
    }

    public function aplicarPropuesta(Request $request, PropuestaAsignacion $propuesta, OptimizadorService $optimizador): RedirectResponse
    {
        abort_unless($propuesta->est === PropuestaAsignacion::EST_PENDIENTE, 422, 'La propuesta ya fue revisada.');
        $datos = $request->validate(['recurso' => ['nullable', 'integer']]);

        $optimizador->aplicar($propuesta, $request->user(), $datos['recurso'] ?? null);

        return back()->with('estado', 'Propuesta aplicada a la salida.');
    }

    public function rechazarPropuesta(Request $request, PropuestaAsignacion $propuesta, OptimizadorService $optimizador): RedirectResponse
    {
        abort_unless($propuesta->est === PropuestaAsignacion::EST_PENDIENTE, 422, 'La propuesta ya fue revisada.');
        $optimizador->rechazar($propuesta, $request->user());

        return back()->with('estado', 'Propuesta rechazada.');
    }

    // ---- RF-25 a RF-27: EOQ ----------------------------------------------------------------------

    public function eoq(EoqService $eoq): View
    {
        return view('admin.io.eoq', ['filas' => $eoq->analizar(), 'z' => EoqService::Z_SERVICIO, 'lead' => EoqService::LEAD_TIME_DIAS]);
    }

    public function alertasEoq(EoqService $eoq, NotificacionService $avisos): RedirectResponse
    {
        $n = $eoq->emitirAlertas($avisos);

        return back()->with('estado', $n ? "{$n} alerta(s) de reposición registradas." : 'Ningún equipo crítico está en el punto de reorden (o ya se avisó hoy).');
    }
}
