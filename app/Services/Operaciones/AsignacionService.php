<?php

namespace App\Services\Operaciones;

use App\Models\PerfilChofer;
use App\Models\PerfilGuia;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Reglas de asignación de recursos a una salida (RF-16, RF-17, M04):
 * - guía activo, sin choque de horario y sin superar su límite semanal de horas;
 * - chofer activo, sin choque de horario y con licencia vigente el día de la salida;
 * - vehículo en estado activo, sin choque de horario y con asientos suficientes;
 * - la capacidad no puede quedar por debajo de los cupos ya reservados.
 * La usan la programación manual y la aplicación de propuestas de IO II.
 */
class AsignacionService
{
    /** Serializa decisiones que comparten guía, chofer o vehículo (incluye propuestas IO). */
    public function bloquearAgenda(): void
    {
        DB::select('SELECT pg_advisory_xact_lock(64026, 1)');
    }

    /**
     * @param  array{tur_id:int, fec_sal:string, hra_sal:string, cap_max:int, prf_gui_id?:?int, prf_cho_id?:?int, veh_id?:?int}  $d
     * @return array<string, string> errores por campo (vacío si es válida)
     */
    public function validar(array $d, ?SalidaTour $actual = null): array
    {
        $errores = [];
        $tour = Tour::find($d['tur_id']);
        if (! $tour) {
            return ['tur_id' => 'El tour no existe.'];
        }

        [$inicio, $fin] = $this->ventana($d['fec_sal'], $d['hra_sal'], (float) $tour->dur_hrs);

        if ($actual) {
            $ocupados = (int) Reserva::where('sal_tur_id', $actual->id)->where('est', '<>', Reserva::EST_CANCELADA)->sum('cnt_ptc');
            if ((int) $d['cap_max'] < $ocupados) {
                $errores['cap_max'] = "Ya hay {$ocupados} cupos reservados; la capacidad no puede ser menor.";
            }
        }

        if (! empty($d['prf_gui_id'])) {
            $guia = PerfilGuia::with('usuario')->find($d['prf_gui_id']);
            if (! $guia || ! $guia->act || ! $guia->usuario?->act) {
                $errores['prf_gui_id'] = 'El guía no está activo.';
            } elseif ($choque = $this->choque('prf_gui_id', $guia->id, $inicio, $fin, $actual)) {
                $errores['prf_gui_id'] = "El guía ya tiene la salida de {$choque} en ese horario.";
            } else {
                $horas = $this->horasSemana($guia->id, $inicio, $actual) + (float) $tour->dur_hrs;
                if ($horas > $guia->lim_hrs_sem) {
                    $errores['prf_gui_id'] = sprintf('El guía superaría su límite semanal (%s de %d h).', rtrim(rtrim(number_format($horas, 1), '0'), '.'), $guia->lim_hrs_sem);
                }
            }
        }

        if (! empty($d['prf_cho_id'])) {
            $chofer = PerfilChofer::with('usuario')->find($d['prf_cho_id']);
            if (! $chofer || ! $chofer->act || ! $chofer->usuario?->act) {
                $errores['prf_cho_id'] = 'El chofer no está activo.';
            } elseif ($chofer->fec_ven_lic->lt($inicio->copy()->startOfDay())) {
                $errores['prf_cho_id'] = 'La licencia del chofer vence antes de la salida ('.$chofer->fec_ven_lic->format('d/m/Y').').';
            } elseif ($choque = $this->choque('prf_cho_id', $chofer->id, $inicio, $fin, $actual)) {
                $errores['prf_cho_id'] = "El chofer ya tiene la salida de {$choque} en ese horario.";
            }
        }

        if (! empty($d['veh_id'])) {
            $vehiculo = Vehiculo::find($d['veh_id']);
            if (! $vehiculo || $vehiculo->est !== Vehiculo::EST_ACTIVO) {
                $errores['veh_id'] = 'El vehículo no está activo (mantenimiento o inactivo).';
            } elseif ($vehiculo->cap_asi < (int) $d['cap_max']) {
                $errores['veh_id'] = "El vehículo tiene {$vehiculo->cap_asi} asientos y la salida admite {$d['cap_max']} personas.";
            } elseif ($choque = $this->choque('veh_id', $vehiculo->id, $inicio, $fin, $actual)) {
                $errores['veh_id'] = "El vehículo ya está asignado a la salida de {$choque} en ese horario.";
            }
        }

        return $errores;
    }

    /** @return array{0: Carbon, 1: Carbon} */
    public function ventana(string $fecha, string $hora, float $durHrs): array
    {
        $inicio = Carbon::parse(substr($fecha, 0, 10).' '.substr($hora, 0, 8));

        return [$inicio, $inicio->copy()->addMinutes((int) round($durHrs * 60))];
    }

    /** Horas ya asignadas al guía en la semana (lunes a domingo) de la fecha dada. */
    public function horasSemana(int $prfGuiId, Carbon $fecha, ?SalidaTour $excluir = null): float
    {
        return (float) SalidaTour::join('tours', 'tours.id', '=', 'salidas_tour.tur_id')
            ->where('salidas_tour.prf_gui_id', $prfGuiId)
            ->where('salidas_tour.est', '<>', SalidaTour::EST_CANCELADA)
            ->whereBetween('salidas_tour.fec_sal', [$fecha->copy()->startOfWeek()->toDateString(), $fecha->copy()->endOfWeek()->toDateString()])
            ->when($excluir, fn ($q) => $q->where('salidas_tour.id', '<>', $excluir->id))
            ->sum('tours.dur_hrs');
    }

    /** Devuelve una descripción de la salida que choca, o null. */
    private function choque(string $campo, int $id, Carbon $inicio, Carbon $fin, ?SalidaTour $excluir): ?string
    {
        $candidatas = SalidaTour::with('tour')
            ->where($campo, $id)
            ->where('est', '<>', SalidaTour::EST_CANCELADA)
            // Un tour dura como máximo 14 h: basta revisar el día anterior, el mismo y el siguiente.
            ->whereBetween('fec_sal', [$inicio->copy()->subDay()->toDateString(), $fin->copy()->addDay()->toDateString()])
            ->when($excluir, fn ($q) => $q->where('id', '<>', $excluir->id))
            ->get();

        foreach ($candidatas as $s) {
            [$i, $f] = $this->ventana($s->fec_sal->toDateString(), $s->hra_sal, (float) $s->tour->dur_hrs);
            if ($i->lt($fin) && $f->gt($inicio)) {
                return "{$s->tour->nom} ({$s->fec_sal->format('d/m')} {$i->format('H:i')}–{$f->format('H:i')})";
            }
        }

        return null;
    }
}
