<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConversacionChatbot;
use App\Models\Pago;
use App\Models\PropuestaAsignacion;
use App\Models\RegistroNotificacion;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\User;
use App\Services\Io\EoqService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Dashboard general y analítica (M09, objetivo 8, RF-33 a RF-35): todos los
 * gráficos se alimentan de las vistas SQL del esquema v5.
 */
class DashboardController extends Controller
{
    public function dashboard(Request $request, EoqService $eoq): View
    {
        $financiero = $request->user()->tieneRol(User::ROL_ADMIN);
        $hoy = today();
        $mes = $hoy->copy()->startOfMonth();

        $salidasHoy = SalidaTour::with('tour', 'guia.usuario')
            ->withSum(['reservas as pax' => fn ($q) => $q->whereIn('est', ['confirmada', 'completada'])], 'cnt_ptc')
            ->whereDate('fec_sal', $hoy)->orderBy('hra_sal')->get();

        $proximas = SalidaTour::whereBetween('fec_sal', [$hoy->toDateString(), $hoy->copy()->addDays(7)->toDateString()])
            ->where('est', 'programada')
            ->withSum(['reservas as ocupados' => fn ($q) => $q->where('est', '<>', 'cancelada')], 'cnt_ptc')
            ->get();

        $alertas = [];
        if ($n = $proximas->whereNull('prf_gui_id')->count()) {
            $alertas[] = ['icono' => 'fa-user-slash', 'texto' => "{$n} salida(s) de los próximos 7 días sin guía asignado.", 'url' => route('admin.io.propuestas')];
        }
        if ($n = PropuestaAsignacion::where('est', 'pendiente')->count()) {
            $alertas[] = ['icono' => 'fa-diagram-project', 'texto' => "{$n} propuesta(s) de asignación esperan revisión.", 'url' => route('admin.io.propuestas')];
        }
        $reponer = collect($eoq->analizar())->where('reponer', true)->pluck('tipo.nom');
        if ($reponer->isNotEmpty()) {
            $alertas[] = ['icono' => 'fa-boxes-stacked', 'texto' => 'Reponer equipo: '.$reponer->implode(', ').'.', 'url' => route('admin.io.eoq')];
        }
        if ($n = ConversacionChatbot::where('der_hum', true)->count()) {
            $alertas[] = ['icono' => 'fa-headset', 'texto' => "{$n} conversación(es) del chatbot esperan a un asesor.", 'url' => route('admin.chatbot')];
        }
        if ($n = RegistroNotificacion::where('est', 'fallido')->count()) {
            $alertas[] = ['icono' => 'fa-envelope-circle-check', 'texto' => "{$n} notificación(es) fallidas.", 'url' => route('admin.auditoria.notificaciones', ['est' => 'fallido'])];
        }

        $cupos = (int) $proximas->sum('cap_max');

        return view('admin.dashboard', [
            'ingresosHoy' => ! $financiero ? null : (float) Pago::where('est', 'completado')->whereDate('pag_en', $hoy)->sum('mto'),
            'ingresosMes' => ! $financiero ? null : (float) Pago::where('est', 'completado')->where('pag_en', '>=', $mes)->sum('mto'),
            'reservasHoy' => Reserva::whereDate('cre_en', $hoy)->count(),
            'pendientesPago' => Reserva::where('est', 'pendiente')->count(),
            'ocupacion' => $cupos ? round($proximas->sum('ocupados') * 100 / $cupos, 1) : 0,
            'salidasHoy' => $salidasHoy,
            'alertas' => $alertas,
            'ventas30' => $financiero ? $this->ventasDiarias($hoy->copy()->subDays(29), $hoy) : [],
        ]);
    }

    public function analitica(Request $request): View
    {
        $f = $request->validate(['tour' => ['nullable', 'integer', 'exists:tours,id'], 'desde' => ['nullable', 'date'], 'hasta' => ['nullable', 'date', 'after_or_equal:desde']]);
        $tourId = $f['tour'] ?? null;
        $desde = Carbon::parse($f['desde'] ?? today()->subMonths(11)->startOfMonth())->startOfMonth();
        $hasta = Carbon::parse($f['hasta'] ?? today()->addMonths(3)->endOfMonth())->endOfMonth();

        // RF-33: demanda por tour y mes (vista_demanda_por_tour_mes) con filtro de fechas.
        $demanda = DB::table('vista_demanda_por_tour_mes')->when($tourId, fn ($q) => $q->where('tur_id', $tourId))
            ->whereRaw('make_date(anio, mes, 1) BETWEEN ? AND ?', [$desde->toDateString(), $hasta->toDateString()])
            ->orderBy('anio')->orderBy('mes')->get();
        $meses = [];
        for ($m = $desde->copy(); $m->lte($hasta); $m->addMonth()) {
            $meses[] = $m->format('Y-m');
        }
        $porTour = $demanda->groupBy('tour')->map(fn ($filas) => array_map(
            fn ($mes) => (int) ($filas->first(fn ($x) => sprintf('%04d-%02d', $x->anio, $x->mes) === $mes)->reservas ?? 0), $meses
        ));

        // RF-35: proyección de demanda (proyecciones_demanda) frente a lo real.
        $proyecciones = DB::table('proyecciones_demanda as p')->join('tours as t', 't.id', '=', 'p.tur_id')->when($tourId, fn ($q) => $q->where('p.tur_id', $tourId))
            ->whereRaw('make_date(p.ani_per, p.mes_per, 1) BETWEEN ? AND ?', [$desde->toDateString(), $hasta->toDateString()])
            ->select('p.ani_per', 'p.mes_per', DB::raw('SUM(p.rsv_pry) AS reservas'), DB::raw('MAX(p.ver_mdl) AS modelo'), DB::raw('MAX(p.gen_en) AS generado'))
            ->groupBy('p.ani_per', 'p.mes_per')->get();
        $proyectado = array_map(fn ($mes) => ($p = $proyecciones->first(fn ($x) => sprintf('%04d-%02d', $x->ani_per, $x->mes_per) === $mes)) ? (int) $p->reservas : null, $meses);
        $real = array_map(fn ($i) => $porTour->sum(fn ($serie) => $serie[$i]), array_keys($meses));

        return view('admin.panels.analitica', [
            'toursFiltro' => Tour::orderBy('nom')->get(['id', 'nom']),
            'filtros' => ['tour' => $tourId, 'desde' => $desde->toDateString(), 'hasta' => $hasta->toDateString()],
            'meses' => $meses,
            'porTour' => $porTour,
            'real' => $real,
            'proyectado' => $proyectado,
            'proyeccionesPorTour' => DB::table('proyecciones_demanda as p')->join('tours as t', 't.id', '=', 'p.tur_id')->when($tourId, fn ($q) => $q->where('p.tur_id', $tourId))
                ->whereRaw('make_date(p.ani_per, p.mes_per, 1) BETWEEN ? AND ?', [$desde->toDateString(), $hasta->toDateString()])
                ->orderBy('t.nom')->orderBy('p.ani_per')->orderBy('p.mes_per')
                ->get(['t.nom', 'p.ani_per', 'p.mes_per', 'p.rsv_pry', 'p.ver_mdl', 'p.gen_en']),
            'modeloProyeccion' => $proyecciones->pluck('modelo')->filter()->unique()->implode(', ') ?: null,
            'proyeccionGenerada' => $proyecciones->max('generado'),
            // RF-34: nacionalidad (la vista ya excluye reservas canceladas).
            'nacionalidades' => DB::table('vista_turistas_por_nacionalidad')->orderByDesc('turistas')->limit(10)->get(),
            'calificaciones' => DB::table('vista_calificacion_promedio_tour')->orderByDesc('calificacion_promedio')->get(),
            'ocupacion' => DB::table('vista_ocupacion_transporte')->whereBetween('fec_sal', [$desde->toDateString(), $hasta->toDateString()])
                ->selectRaw('pla, ROUND(AVG(porcentaje_ocupacion), 1) AS ocupacion, COUNT(*) AS salidas, SUM(cos_cmb) AS combustible')
                ->groupBy('pla')->orderBy('pla')->get(),
            'cargaGuias' => DB::table('vista_carga_horaria_guia')->where('semana', today()->startOfWeek()->toDateString())->orderBy('guia')->get(),
            'llegadas' => DB::table('vista_llegadas_diarias_tour')->where('fec', '>=', today()->subDays(29)->toDateString())
                ->selectRaw('fec, SUM(admitidas) AS admitidas, SUM(rechazadas) AS rechazadas')->groupBy('fec')->orderBy('fec')->get(),
        ]);
    }

    /** Ingresos y reservas por día. */
    private function ventasDiarias(Carbon $desde, Carbon $hasta): array
    {
        $pagos = Pago::where('est', 'completado')->whereBetween('pag_en', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->selectRaw('pag_en::date AS dia, SUM(mto) AS total')->groupBy('dia')->pluck('total', 'dia');
        $reservas = Reserva::whereBetween('cre_en', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->selectRaw('cre_en::date AS dia, COUNT(*) AS n')->groupBy('dia')->pluck('n', 'dia');

        $serie = [];
        for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
            $k = $d->toDateString();
            $serie[] = ['dia' => $d->format('d/m'), 'ingresos' => (float) ($pagos[$k] ?? 0), 'reservas' => (int) ($reservas[$k] ?? 0)];
        }

        return $serie;
    }
}
