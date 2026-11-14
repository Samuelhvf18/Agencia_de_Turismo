<?php

namespace App\Services\Operaciones;

use App\Models\BitacoraViajeVehiculo;
use App\Models\Boleto;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\User;
use App\Services\Notificaciones\NotificacionService;
use App\Services\Reservas\FirmaDigital;
use App\Services\Reservas\ReservaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Operaciones y salidas (Módulo 04, RF-16 a RF-18, RF-21): programación,
 * asignación, reprogramación y cancelación con aviso a los clientes,
 * check-in por QR e inicio/fin real del recorrido.
 */
class SalidaService
{
    public function __construct(
        private AsignacionService $asignacion,
        private ReservaService $reservas,
        private NotificacionService $avisos,
    ) {}

    public function programar(array $datos): SalidaTour
    {
        return DB::transaction(function () use ($datos) {
            $this->asignacion->bloquearAgenda();
            $this->validar($datos);

            return SalidaTour::create($datos + ['est' => SalidaTour::EST_PROGRAMADA]);
        }, 3);
    }

    /** Cambia capacidad y asignaciones sin mover la fecha, validando dentro del bloqueo. */
    public function asignar(SalidaTour $salida, array $datos): SalidaTour
    {
        return DB::transaction(function () use ($salida, $datos) {
            $this->asignacion->bloquearAgenda();
            $salida = SalidaTour::lockForUpdate()->findOrFail($salida->id);
            $this->exigirEditable($salida);
            $completo = $datos + $salida->only(['tur_id', 'fec_sal', 'hra_sal', 'cap_max', 'prf_gui_id', 'prf_cho_id', 'veh_id']);
            $completo['fec_sal'] = $salida->fec_sal->toDateString();
            $this->validar($completo, $salida);
            $salida->update(array_intersect_key($datos, array_flip(['cap_max', 'prf_gui_id', 'prf_cho_id', 'veh_id'])));

            return $salida;
        }, 3);
    }

    public function reprogramar(SalidaTour $salida, string $fecha, string $hora, string $motivo): SalidaTour
    {
        return DB::transaction(function () use ($salida, $fecha, $hora, $motivo) {
            $this->asignacion->bloquearAgenda();
            $salida = SalidaTour::lockForUpdate()->findOrFail($salida->id);
            $this->exigirEditable($salida);
            $datos = ['fec_sal' => $fecha, 'hra_sal' => $hora] + $salida->only(['tur_id', 'cap_max', 'prf_gui_id', 'prf_cho_id', 'veh_id']);
            $this->validar($datos, $salida);
            $antes = $salida->fec_sal->format('d/m/Y').' '.substr($salida->hra_sal, 0, 5);
            $salida->update(['fec_sal' => $fecha, 'hra_sal' => $hora]);
            foreach ($this->reservasActivas($salida) as $reserva) {
                $this->avisos->aReserva($reserva, 'salida_reprogramada', [
                    'mensaje' => "La salida de {$salida->tour->nom} cambió de {$antes} al ".$salida->fec_sal->format('d/m/Y').' '.substr($hora, 0, 5).". Motivo: {$motivo}. Si la nueva fecha no te sirve, puedes cancelar sin costo desde tu reserva.",
                    'fecha_anterior' => $antes,
                ]);
            }

            return $salida;
        }, 3);
    }

    /** La salida y todas sus reservas se cancelan en la misma transacción. */
    public function cancelar(SalidaTour $salida, string $motivo): SalidaTour
    {
        return DB::transaction(function () use ($salida, $motivo) {
            $this->asignacion->bloquearAgenda();
            $salida = SalidaTour::lockForUpdate()->findOrFail($salida->id);
            $this->exigirEditable($salida);
            $salida->update(['est' => SalidaTour::EST_CANCELADA]);
            foreach ($this->reservasActivas($salida) as $reserva) {
                $this->reservas->cancelar($reserva, "Salida cancelada: {$motivo}");
                $this->avisos->aReserva($reserva->refresh(), 'salida_cancelada', [
                    'mensaje' => "La salida de {$salida->tour->nom} del ".$salida->fec_sal->format('d/m/Y')." fue cancelada ({$motivo}). Si hubo un pago real, contacta a la agencia para conciliar su devolución.",
                ]);
            }

            return $salida;
        }, 3);
    }

    public function iniciar(SalidaTour $salida): SalidaTour
    {
        return DB::transaction(function () use ($salida) {
            $this->asignacion->bloquearAgenda();
            $salida = SalidaTour::lockForUpdate()->findOrFail($salida->id);
            $this->exigirEditable($salida);
            $salida->update(['est' => SalidaTour::EST_EN_CURSO, 'hra_ini_rea' => now()]);

            return $salida;
        }, 3);
    }

    /** Cierre y efectos sobre reservas/equipo atómicos, incluso si falla una notificación. */
    public function finalizar(SalidaTour $salida, ?float $km = null, ?float $combustible = null): SalidaTour
    {
        return DB::transaction(function () use ($salida, $km, $combustible) {
            $this->asignacion->bloquearAgenda();
            $salida = SalidaTour::lockForUpdate()->findOrFail($salida->id);
            if ($salida->est !== SalidaTour::EST_EN_CURSO) {
                throw ValidationException::withMessages(['salida' => 'Primero registra el inicio del recorrido.']);
            }
            $salida->update(['est' => SalidaTour::EST_COMPLETADA, 'hra_fin_rea' => now()]);
            if ($salida->veh_id && ($km !== null || $combustible !== null)) {
                BitacoraViajeVehiculo::create(['veh_id' => $salida->veh_id, 'sal_tur_id' => $salida->id, 'dst_km' => $km, 'cos_cmb' => $combustible]);
            }
            foreach ($this->reservasActivas($salida) as $reserva) {
                $reserva->est === Reserva::EST_CONFIRMADA
                    ? $this->reservas->completar($reserva)
                    : $this->reservas->cancelar($reserva, 'Salida concluida sin pago');
            }

            return $salida;
        }, 3);
    }

    /**
     * Valida un boleto en el check-in (RF-21).
     *
     * @return array{ok: bool, mensaje: string, participante?: string, cod: string}
     */
    public function validarBoleto(SalidaTour $salida, string $codigo, User $usuario, ?string $momento = null): array
    {
        $codigo = strtoupper(trim($codigo));

        return DB::transaction(function () use ($salida, $codigo, $usuario, $momento) {
            $boleto = Boleto::with('participante.reserva', 'participante.fichaMedica')->where('cod', $codigo)->lockForUpdate()->first();

            if (! $boleto) {
                return ['ok' => false, 'cod' => $codigo, 'mensaje' => 'Boleto no encontrado.'];
            }
            $ptc = $boleto->participante;
            if ($ptc->reserva->sal_tur_id !== $salida->id) {
                return ['ok' => false, 'cod' => $codigo, 'mensaje' => 'El boleto es de otra salida.'];
            }
            if ($boleto->est === Boleto::EST_CANCELADO || $ptc->reserva->est !== Reserva::EST_CONFIRMADA) {
                return ['ok' => false, 'cod' => $codigo, 'participante' => $ptc->nom_cpl, 'mensaje' => 'El boleto está cancelado o la reserva no está confirmada.'];
            }
            if ($salida->tour->esAltoRiesgo() && (! $ptc->fichaMedica?->fir_en || ! FirmaDigital::existe($ptc->fichaMedica?->rta_fir))) {
                return ['ok' => false, 'cod' => $codigo, 'participante' => $ptc->nom_cpl, 'mensaje' => 'Falta la ficha médica firmada (tour de alto riesgo).'];
            }
            if ($boleto->est === Boleto::EST_VALIDADO) {
                return ['ok' => true, 'cod' => $codigo, 'participante' => $ptc->nom_cpl, 'mensaje' => 'Ya estaba validado ('.$boleto->vld_en->format('H:i').').', 'repetido' => true];
            }

            $boleto->update(['est' => Boleto::EST_VALIDADO, 'vld_en' => $momento ?: now(), 'vld_por' => $usuario->id]);

            return ['ok' => true, 'cod' => $codigo, 'participante' => $ptc->nom_cpl, 'mensaje' => 'Boleto validado.'];
        });
    }

    /** @return Collection<int, Reserva> */
    public function reservasActivas(SalidaTour $salida)
    {
        return Reserva::where('sal_tur_id', $salida->id)
            ->whereIn('est', [Reserva::EST_PENDIENTE, Reserva::EST_CONFIRMADA])
            ->get();
    }

    private function validar(array $datos, ?SalidaTour $actual = null): void
    {
        $errores = $this->asignacion->validar($datos, $actual);
        if ($errores) {
            throw ValidationException::withMessages($errores);
        }
    }

    private function exigirEditable(SalidaTour $salida): void
    {
        if ($salida->est !== SalidaTour::EST_PROGRAMADA) {
            throw ValidationException::withMessages(['salida' => 'Solo se pueden modificar salidas programadas.']);
        }
    }
}
