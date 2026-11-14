<?php

namespace App\Services\Io;

use App\Models\PerfilGuia;
use App\Models\PropuestaAsignacion;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\Operaciones\AsignacionService;
use App\Services\Operaciones\SalidaService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Optimización de recursos (Módulo 07, RF-22 a RF-24).
 *
 * Guías (RF-22): problema de asignación binaria resuelto con el método
 * húngaro, un día a la vez (un guía hace a lo sumo una salida por día). El
 * costo c_ij combina carga horaria (vista_carga_horaria_guia), experiencia
 * —con más peso en tours de alto riesgo— e idiomas que piden los pasajeros.
 * Las celdas que violan restricciones (choque de horario, límite semanal)
 * tienen costo M y nunca se proponen.
 *
 * Vehículos (RF-23): mismo esquema, minimizando el costo estimado de
 * combustible del viaje (costo por km observado en vista_ocupacion_transporte)
 * más una penalización por asientos vacíos.
 *
 * Las propuestas (RF-24) se guardan como pendientes y nunca se aplican solas.
 */
class OptimizadorService
{
    public const M = 1_000_000.0;

    /** Idioma principal por país de los pasajeros (código ISO). */
    private const IDIOMA_PAIS = [
        'BO' => 'es', 'AR' => 'es', 'CL' => 'es', 'PE' => 'es', 'CO' => 'es', 'EC' => 'es', 'PY' => 'es', 'UY' => 'es',
        'VE' => 'es', 'MX' => 'es', 'ES' => 'es', 'BR' => 'pt', 'FR' => 'fr', 'BE' => 'fr', 'DE' => 'de', 'CH' => 'de', 'IT' => 'it',
    ];

    /** Costo por km por tipo de combustible cuando no hay historial (Bs/km). */
    private const COSTO_KM_DEFECTO = ['diesel' => 0.90, 'gasolina' => 1.10, 'gnv' => 0.60, 'electrico' => 0.30];

    public function __construct(
        private MetodoHungaro $hungaro,
        private AsignacionService $asignacion,
        private SalidaService $salidas,
    ) {}

    // ---- RF-22: guías ----------------------------------------------------------------

    /** @return array{lote: string, propuestas: int, sin_solucion: int} */
    public function proponerGuias(Carbon $desde, Carbon $hasta, bool $soloSinAsignar = true): array
    {
        $lote = (string) Str::uuid();
        $guias = PerfilGuia::with('usuario', 'idiomas')->where('act', true)->whereHas('usuario', fn ($q) => $q->where('act', true))->get()->values();
        $salidasPorDia = $this->salidasAbiertas($desde, $hasta, $soloSinAsignar ? 'prf_gui_id' : null)->groupBy(fn ($s) => $s->fec_sal->toDateString());
        $extra = []; // horas propuestas en este lote: [prf_gui_id][semana] => horas
        $creadas = 0;
        $sinSolucion = 0;

        foreach ($salidasPorDia as $dia => $salidas) {
            $salidas = $salidas->values();
            $costos = [];
            $detalle = [];
            foreach ($salidas as $i => $salida) {
                foreach ($guias as $j => $guia) {
                    [$costos[$i][$j], $detalle[$i][$j]] = $this->costoGuia($salida, $guia, $extra);
                }
            }

            $asignacion = $guias->isEmpty() ? [] : $this->hungaro->resolver($costos);
            foreach ($salidas as $i => $salida) {
                $j = $asignacion[$i] ?? null;
                if ($j === null || $costos[$i][$j] >= self::M) {
                    $sinSolucion++;

                    continue;
                }
                $guia = $guias[$j];
                $semana = $salida->fec_sal->copy()->startOfWeek()->toDateString();
                $extra[$guia->id][$semana] = ($extra[$guia->id][$semana] ?? 0) + (float) $salida->tour->dur_hrs;

                if ($salida->prf_gui_id === $guia->id) {
                    continue; // el modelo confirma la asignación actual
                }
                PropuestaAsignacion::create([
                    'lote' => $lote, 'tip' => 'guia', 'sal_tur_id' => $salida->id,
                    'rec_act_id' => $salida->prf_gui_id, 'rec_pro_id' => $guia->id,
                    'cos' => round($costos[$i][$j], 2), 'det' => $detalle[$i][$j] + ['dia' => $dia, 'modelo' => 'hungaro'],
                ]);
                $creadas++;
            }
        }

        return ['lote' => $lote, 'propuestas' => $creadas, 'sin_solucion' => $sinSolucion];
    }

    /** @return array{0: float, 1: array} costo y desglose */
    public function costoGuia(SalidaTour $salida, PerfilGuia $guia, array $extra = []): array
    {
        $datos = ['prf_cho_id' => null, 'veh_id' => null, 'prf_gui_id' => $guia->id] + $salida->only(['tur_id', 'cap_max', 'hra_sal'])
            + ['fec_sal' => $salida->fec_sal->toDateString()];
        $errores = $this->asignacion->validar($datos, $salida);

        $semana = $salida->fec_sal->copy()->startOfWeek()->toDateString();
        $horasPrevias = $this->horasSemanaVista($guia->id, $semana, $salida) + ($extra[$guia->id][$semana] ?? 0);
        $horasDespues = $horasPrevias + (float) $salida->tour->dur_hrs;

        if (isset($errores['prf_gui_id']) || $horasDespues > $guia->lim_hrs_sem) {
            return [self::M, ['motivo' => $errores['prf_gui_id'] ?? 'Supera el límite semanal de horas.']];
        }

        $idiomasGuia = $guia->idiomas->pluck('cod_iso')->all();
        $faltantes = array_values(array_diff($this->idiomasRequeridos($salida), $idiomasGuia));
        $pesoExperiencia = $salida->tour->esAltoRiesgo() ? 6 : 3;

        $carga = 40 * $horasDespues / max(1, $guia->lim_hrs_sem);
        $experiencia = $pesoExperiencia * (10 - min(10, (int) $guia->ani_exp));
        $idiomas = 60 * count($faltantes);

        return [$carga + $experiencia + $idiomas, [
            'carga' => round($carga, 2), 'experiencia' => $experiencia, 'idiomas' => $idiomas,
            'horas_semana' => round($horasDespues, 2), 'limite' => $guia->lim_hrs_sem,
            'idiomas_faltantes' => $faltantes, 'ani_exp' => $guia->ani_exp,
        ]];
    }

    /** Idiomas que piden los pasajeros según su nacionalidad (siempre incluye español). */
    public function idiomasRequeridos(SalidaTour $salida): array
    {
        $paises = DB::table('participantes_reserva as p')
            ->join('reservas as r', 'r.id', '=', 'p.rsv_id')
            ->leftJoin('paises as pa', 'pa.id', '=', 'p.pai_id')
            ->where('r.sal_tur_id', $salida->id)
            ->where('r.est', '<>', Reserva::EST_CANCELADA)
            ->pluck('pa.cod_iso');

        return $paises->map(fn ($iso) => self::IDIOMA_PAIS[$iso] ?? 'en')->push('es')->unique()->values()->all();
    }

    /** Horas de la semana según vista_carga_horaria_guia, sin contar la propia salida. */
    private function horasSemanaVista(int $prfGuiId, string $semana, SalidaTour $salida): float
    {
        $horas = (float) DB::table('vista_carga_horaria_guia')->where('prf_gui_id', $prfGuiId)->where('semana', $semana)->value('horas_asignadas');
        if ($salida->prf_gui_id === $prfGuiId && $salida->est !== SalidaTour::EST_CANCELADA) {
            $horas -= (float) $salida->tour->dur_hrs;
        }

        return max(0, $horas);
    }

    // ---- RF-23: vehículos --------------------------------------------------------------

    /** @return array{lote: string, propuestas: int, sin_solucion: int} */
    public function proponerVehiculos(Carbon $desde, Carbon $hasta, bool $soloSinAsignar = true): array
    {
        $lote = (string) Str::uuid();
        $vehiculos = Vehiculo::where('est', Vehiculo::EST_ACTIVO)->orderBy('id')->get()->values();
        $costoKm = $this->costoKmPorVehiculo();
        $creadas = 0;
        $sinSolucion = 0;

        $salidasPorDia = $this->salidasAbiertas($desde, $hasta, $soloSinAsignar ? 'veh_id' : null)->groupBy(fn ($s) => $s->fec_sal->toDateString());
        foreach ($salidasPorDia as $dia => $salidas) {
            $salidas = $salidas->values();
            $costos = [];
            $detalle = [];
            foreach ($salidas as $i => $salida) {
                foreach ($vehiculos as $j => $vehiculo) {
                    [$costos[$i][$j], $detalle[$i][$j]] = $this->costoVehiculo($salida, $vehiculo, $costoKm);
                }
            }

            $asignacion = $vehiculos->isEmpty() ? [] : $this->hungaro->resolver($costos);
            foreach ($salidas as $i => $salida) {
                $j = $asignacion[$i] ?? null;
                if ($j === null || $costos[$i][$j] >= self::M) {
                    $sinSolucion++;

                    continue;
                }
                if ($salida->veh_id === $vehiculos[$j]->id) {
                    continue;
                }
                PropuestaAsignacion::create([
                    'lote' => $lote, 'tip' => 'vehiculo', 'sal_tur_id' => $salida->id,
                    'rec_act_id' => $salida->veh_id, 'rec_pro_id' => $vehiculos[$j]->id,
                    'cos' => round($costos[$i][$j], 2), 'det' => $detalle[$i][$j] + ['dia' => $dia, 'modelo' => 'hungaro_combustible'],
                ]);
                $creadas++;
            }
        }

        return ['lote' => $lote, 'propuestas' => $creadas, 'sin_solucion' => $sinSolucion];
    }

    /** @return array{0: float, 1: array} */
    public function costoVehiculo(SalidaTour $salida, Vehiculo $vehiculo, array $costoKm = []): array
    {
        $datos = ['prf_gui_id' => null, 'prf_cho_id' => null, 'veh_id' => $vehiculo->id] + $salida->only(['tur_id', 'cap_max', 'hra_sal'])
            + ['fec_sal' => $salida->fec_sal->toDateString()];
        $errores = $this->asignacion->validar($datos, $salida);
        if (isset($errores['veh_id'])) {
            return [self::M, ['motivo' => $errores['veh_id']]];
        }

        $km = $this->kmEstimados($salida);
        $porKm = $costoKm[$vehiculo->id] ?? self::COSTO_KM_DEFECTO[$vehiculo->tip_cmb] ?? 1.0;
        $factorTipo = $vehiculo->tip === 'minibus' ? 1.3 : 1.0;
        $combustible = $km * $porKm * (isset($costoKm[$vehiculo->id]) ? 1 : $factorTipo);
        $pasajeros = max(1, (int) Reserva::where('sal_tur_id', $salida->id)->where('est', '<>', Reserva::EST_CANCELADA)->sum('cnt_ptc'));
        $vacios = max(0, $vehiculo->cap_asi - max($pasajeros, (int) $salida->cap_max));
        $penalizacion = 2.0 * $vacios;

        return [$combustible + $penalizacion, [
            'km_estimados' => round($km, 1), 'costo_km' => round($porKm, 3), 'combustible' => round($combustible, 2),
            'asientos_vacios' => $vacios, 'penalizacion' => $penalizacion,
            'fuente_costo' => isset($costoKm[$vehiculo->id]) ? 'historial (vista_ocupacion_transporte)' : 'valor de referencia por combustible',
        ]];
    }

    /** Costo por km observado de cada vehículo (Bs/km) según vista_ocupacion_transporte. */
    public function costoKmPorVehiculo(): array
    {
        return DB::table('vista_ocupacion_transporte')
            ->whereNotNull('dst_km')->where('dst_km', '>', 0)->whereNotNull('cos_cmb')
            ->groupBy('veh_id')
            ->selectRaw('veh_id, SUM(cos_cmb) / SUM(dst_km) AS costo_km')
            ->pluck('costo_km', 'veh_id')
            ->map(fn ($v) => (float) $v)
            ->all();
    }

    /** Km promedio de las salidas anteriores del tour; sin historial, 25 km por hora de tour. */
    private function kmEstimados(SalidaTour $salida): float
    {
        $promedio = DB::table('bitacora_viajes_vehiculo as b')
            ->join('salidas_tour as s', 's.id', '=', 'b.sal_tur_id')
            ->where('s.tur_id', $salida->tur_id)
            ->whereNotNull('b.dst_km')
            ->avg('b.dst_km');

        return $promedio ? (float) $promedio : 25.0 * (float) $salida->tour->dur_hrs;
    }

    // ---- RF-24: revisión de propuestas ----------------------------------------------------

    /**
     * Aplica una propuesta (o un recurso ajustado a mano) pasando por las mismas
     * validaciones que la asignación manual.
     */
    public function aplicar(PropuestaAsignacion $propuesta, User $usuario, ?int $recursoAjustado = null): PropuestaAsignacion
    {
        return DB::transaction(function () use ($propuesta, $usuario, $recursoAjustado) {
            app(AsignacionService::class)->bloquearAgenda();
            $propuesta = PropuestaAsignacion::lockForUpdate()->findOrFail($propuesta->id);
            if ($propuesta->est !== PropuestaAsignacion::EST_PENDIENTE) {
                throw ValidationException::withMessages(['propuesta' => 'Esta propuesta ya fue revisada. Actualiza la página.']);
            }
            $campo = $propuesta->tip === 'guia' ? 'prf_gui_id' : 'veh_id';
            $recurso = $recursoAjustado ?? $propuesta->rec_pro_id;
            $this->salidas->asignar($propuesta->salida, [$campo => $recurso]);
            $propuesta->update([
                'est' => $recursoAjustado && $recursoAjustado !== $propuesta->rec_pro_id ? PropuestaAsignacion::EST_AJUSTADA : PropuestaAsignacion::EST_APLICADA,
                'rec_pro_id' => $recurso, 'rev_por' => $usuario->id, 'rev_en' => now(),
            ]);
            PropuestaAsignacion::where('sal_tur_id', $propuesta->sal_tur_id)->where('tip', $propuesta->tip)
                ->where('est', PropuestaAsignacion::EST_PENDIENTE)->where('id', '<>', $propuesta->id)
                ->update(['est' => PropuestaAsignacion::EST_RECHAZADA, 'rev_por' => $usuario->id, 'rev_en' => now()]);

            return $propuesta;
        }, 3);
    }

    public function rechazar(PropuestaAsignacion $propuesta, User $usuario): void
    {
        DB::transaction(function () use ($propuesta, $usuario) {
            app(AsignacionService::class)->bloquearAgenda();
            $propuesta = PropuestaAsignacion::lockForUpdate()->findOrFail($propuesta->id);
            if ($propuesta->est !== PropuestaAsignacion::EST_PENDIENTE) {
                throw ValidationException::withMessages(['propuesta' => 'Esta propuesta ya fue revisada. Actualiza la página.']);
            }
            $propuesta->update(['est' => PropuestaAsignacion::EST_RECHAZADA, 'rev_por' => $usuario->id, 'rev_en' => now()]);
        }, 3);
    }

    /** @return Collection<int, SalidaTour> */
    private function salidasAbiertas(Carbon $desde, Carbon $hasta, ?string $sinCampo): Collection
    {
        return SalidaTour::with('tour')
            ->where('est', SalidaTour::EST_PROGRAMADA)
            ->whereBetween('fec_sal', [max($desde->toDateString(), today()->toDateString()), $hasta->toDateString()])
            ->when($sinCampo, fn ($q) => $q->whereNull($sinCampo))
            ->orderBy('fec_sal')->orderBy('hra_sal')
            ->get();
    }
}
