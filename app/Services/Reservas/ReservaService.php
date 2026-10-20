<?php

namespace App\Services\Reservas;

use App\Models\AlquilerEquipoReserva;
use App\Models\Boleto;
use App\Models\ComprobantePago;
use App\Models\EquipoPorTour;
use App\Models\FichaMedica;
use App\Models\ItemEquipo;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\User;
use App\Services\CodeGeneratorService;
use App\Services\Io\ColasService;
use App\Services\Io\MarkovService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Ciclo de vida de una reserva (Módulo 05): confirmación con control
 * transaccional de cupo y stock, cancelación y cierre. Cada evento alimenta
 * los modelos de IO II: llegadas a la cola M/M/c/K (admitida, rechazada o
 * cancelada) y cambios de estado del cliente en la cadena de Markov.
 */
class ReservaService
{
    public function __construct(
        private ColasService $colas,
        private MarkovService $markov,
    ) {}

    /** Cupos ocupados por reservas no canceladas de una salida. */
    public function cuposOcupados(int $salTurId): int
    {
        return (int) Reserva::where('sal_tur_id', $salTurId)
            ->where('est', '<>', Reserva::EST_CANCELADA)
            ->sum('cnt_ptc');
    }

    /** Precio unitario de cada tipo de equipo habilitado en el tour: [tip_eqp_id => precio]. */
    public function preciosEquipo(int $turId): array
    {
        return EquipoPorTour::with('tipo')
            ->where('tur_id', $turId)
            ->where('act', true)
            ->get()
            ->mapWithKeys(fn (EquipoPorTour $e) => [$e->tip_eqp_id => $e->precio()])
            ->all();
    }

    /** Total del borrador: precio base × participantes + equipo alquilado. */
    public function calcularTotal(SalidaTour $salida, array $borrador): array
    {
        $precios = $this->preciosEquipo($salida->tur_id);
        $tour = (float) $salida->tour->pre_bas * count($borrador['participantes'] ?? []);
        $equipo = 0.0;
        foreach ($borrador['alquileres'] ?? [] as $porTipo) {
            foreach ($porTipo as $tipId => $itemId) {
                if ($itemId) {
                    $equipo += $precios[$tipId] ?? 0;
                }
            }
        }

        return ['tour' => round($tour, 2), 'equipo' => round($equipo, 2), 'total' => round($tour + $equipo, 2)];
    }

    /**
     * Crea la reserva (pendiente de pago) a partir del borrador del wizard.
     * Bloquea la salida y los ítems de equipo (SELECT … FOR UPDATE) para que
     * dos reservas simultáneas no sobrevendan cupos ni tallas.
     *
     * @throws CupoAgotadoException si no quedan cupos (la llegada se registra como rechazada)
     * @throws ValidationException si falta la ficha en un tour de alto riesgo o no hay stock
     */
    public function confirmar(array $borrador, ?User $usuario): Reserva
    {
        $participantes = array_values($borrador['participantes'] ?? []);
        $cantidad = count($participantes);

        try {
            $reserva = DB::transaction(function () use ($borrador, $usuario, $participantes, $cantidad) {
                $salida = SalidaTour::with('tour')->lockForUpdate()->findOrFail($borrador['sal_tur_id']);
                $this->validarSalidaAbierta($salida);

                $disponibles = $salida->cap_max - $this->cuposOcupados($salida->id);
                if ($cantidad < 1 || $cantidad > $disponibles) {
                    throw new CupoAgotadoException(max(0, $disponibles));
                }

                if ($salida->tour->esAltoRiesgo()) {
                    foreach ($participantes as $i => $p) {
                        if (! FirmaDigital::existe($borrador['fichas'][$i]['rta_fir'] ?? null)) {
                            throw ValidationException::withMessages([
                                'fichas' => "Falta la ficha médica firmada de {$p['nom_cpl']}: es obligatoria en este tour.",
                            ]);
                        }
                    }
                }

                $precios = $this->preciosEquipo($salida->tur_id);
                $items = $this->reservarEquipo($borrador['alquileres'] ?? [], $precios);
                $total = $this->calcularTotal($salida, $borrador)['total'];

                $reserva = Reserva::create([
                    'cod' => CodeGeneratorService::generate('RES'),
                    'sal_tur_id' => $salida->id,
                    'usr_id' => $usuario?->id,
                    'nom_ctc' => $borrador['contacto']['nom_ctc'],
                    'cor_ctc' => strtolower($borrador['contacto']['cor_ctc']),
                    'tel_ctc' => $borrador['contacto']['tel_ctc'],
                    'cnt_ptc' => $cantidad,
                    'est' => Reserva::EST_PENDIENTE,
                    'pre_tot' => $total,
                    'est_pag' => Reserva::PAG_PENDIENTE,
                ]);

                foreach ($participantes as $i => $p) {
                    $ptc = $reserva->participantes()->create([
                        'nom_cpl' => $p['nom_cpl'],
                        'tip_doc' => $p['tip_doc'],
                        'num_doc' => $p['num_doc'],
                        'ci_cmp' => $p['tip_doc'] === 'CI' ? ($p['ci_cmp'] ?? '') : '',
                        'ci_dep_id' => $p['tip_doc'] === 'CI' ? ($p['ci_dep_id'] ?? null) : null,
                        'eda' => $p['eda'],
                        'pai_id' => $p['pai_id'] ?: null,
                        'es_ctc_pri' => ! empty($p['es_ctc_pri']),
                    ]);

                    foreach ($borrador['alquileres'][$i] ?? [] as $tipId => $itemId) {
                        if ($itemId) {
                            AlquilerEquipoReserva::create([
                                'ptc_rsv_id' => $ptc->id,
                                'itm_eqp_id' => $items[$itemId]->id,
                                'pre' => $precios[$tipId],
                            ]);
                        }
                    }

                    $ficha = $borrador['fichas'][$i] ?? null;
                    if ($ficha && FirmaDigital::existe($ficha['rta_fir'] ?? null)) {
                        FichaMedica::create([
                            'ptc_rsv_id' => $ptc->id,
                            'con_med' => $ficha['con_med'] ?? null,
                            'nom_ctc_eme' => $ficha['nom_ctc_eme'] ?? null,
                            'tel_ctc_eme' => $ficha['tel_ctc_eme'] ?? null,
                            'rta_fir' => FirmaDigital::moverDefinitiva($ficha['rta_fir'], $reserva->cod, $ptc->id),
                            'fir_en' => $ficha['fir_en'],
                            'dir_ip' => $ficha['dir_ip'] ?? null,
                        ]);
                    }
                }

                // IO II · colas: llegada admitida al sistema M/M/c/K.
                $this->colas->registrarLlegada($salida->tur_id, $salida->id, $reserva->id, $cantidad, 'admitido');

                return $reserva;
            });
        } catch (CupoAgotadoException $e) {
            // IO II · colas: el sistema estaba lleno (K alcanzado) → llegada rechazada.
            $salida = SalidaTour::find($borrador['sal_tur_id']);
            if ($salida) {
                $this->colas->registrarLlegada($salida->tur_id, $salida->id, null, max(1, $cantidad), 'rechazado');
            }
            throw $e;
        }

        return $reserva;
    }

    /** Registra una solicitud rechazada en el primer paso (cupos insuficientes al elegir la salida). */
    public function registrarRechazo(SalidaTour $salida, int $cupos): void
    {
        $this->colas->registrarLlegada($salida->tur_id, $salida->id, null, max(1, $cupos), 'rechazado');
    }

    /**
     * Cancela la reserva: libera cupos y equipo, cancela boletos, anula el
     * comprobante pendiente y marca como reembolsado lo que ya se pagó.
     */
    public function cancelar(Reserva $reserva, string $motivo = 'Cancelada'): Reserva
    {
        DB::transaction(function () use ($reserva) {
            $reserva = Reserva::lockForUpdate()->findOrFail($reserva->id);
            if (in_array($reserva->est, [Reserva::EST_CANCELADA, Reserva::EST_COMPLETADA], true)) {
                return;
            }

            $this->devolverEquipo($reserva);

            Boleto::whereIn('ptc_rsv_id', $reserva->participantes()->pluck('id'))
                ->where('est', '<>', Boleto::EST_CANCELADO)
                ->update(['est' => Boleto::EST_CANCELADO]);

            $comprobante = $reserva->comprobante;
            if ($comprobante && $comprobante->est === ComprobantePago::EST_PENDIENTE && $comprobante->montoPagado() == 0.0) {
                $comprobante->update(['est' => ComprobantePago::EST_ANULADO]);
            }

            $reserva->update([
                'est' => Reserva::EST_CANCELADA,
                'est_pag' => in_array($reserva->est_pag, [Reserva::PAG_PAGADO, Reserva::PAG_PARCIAL], true)
                    ? Reserva::PAG_REEMBOLSADO
                    : $reserva->est_pag,
            ]);

            $salida = $reserva->salida;
            // IO II · colas: abandono del sistema.
            $this->colas->registrarLlegada($salida->tur_id, $salida->id, $reserva->id, $reserva->cnt_ptc, 'cancelado');
        });

        Log::info('Reserva cancelada', ['cod' => $reserva->cod, 'motivo' => $motivo]);

        return $reserva->refresh();
    }

    /** El pago quedó completo: confirma la reserva y actualiza el estado Markov del cliente. */
    public function marcarConfirmada(Reserva $reserva): void
    {
        if ($reserva->est === Reserva::EST_PENDIENTE) {
            $reserva->update(['est' => Reserva::EST_CONFIRMADA]);
        }

        if ($reserva->usr_id) {
            // IO II · Markov: prospecto/completado → reservado.
            $this->markov->registrarCambio($reserva->usr_id, 'reservado', "Reserva {$reserva->cod} pagada");
        }
    }

    /**
     * Cierra la reserva cuando la salida termina: devuelve el equipo al
     * inventario y pasa al cliente a completado (o fidelizado desde el 2.º tour).
     */
    public function completar(Reserva $reserva): void
    {
        DB::transaction(function () use ($reserva) {
            $reserva = Reserva::lockForUpdate()->findOrFail($reserva->id);
            if ($reserva->est !== Reserva::EST_CONFIRMADA) {
                return;
            }
            $this->devolverEquipo($reserva);
            $reserva->update(['est' => Reserva::EST_COMPLETADA]);
        });

        $reserva->refresh();
        if ($reserva->usr_id && $reserva->est === Reserva::EST_COMPLETADA) {
            $completadas = Reserva::where('usr_id', $reserva->usr_id)->where('est', Reserva::EST_COMPLETADA)->count();
            $this->markov->registrarCambio($reserva->usr_id, 'completado', "Tour de la reserva {$reserva->cod} completado");
            if ($completadas >= 2) {
                $this->markov->registrarCambio($reserva->usr_id, 'fidelizado', "{$completadas} tours completados");
            }
        }
    }

    private function validarSalidaAbierta(SalidaTour $salida): void
    {
        $abierta = $salida->est === SalidaTour::EST_PROGRAMADA
            && $salida->fec_sal->gte(today())
            && $salida->tour->act
            && is_null($salida->tour->eli_en);

        if (! $abierta) {
            throw ValidationException::withMessages(['sal_tur_id' => 'Esta salida ya no admite reservas. Elige otra.']);
        }
    }

    /**
     * Descuenta del inventario los ítems elegidos (con bloqueo de fila).
     *
     * @return array<int, ItemEquipo> ítems bloqueados indexados por id
     */
    private function reservarEquipo(array $alquileres, array $precios): array
    {
        $uso = [];
        foreach ($alquileres as $porTipo) {
            foreach ($porTipo as $tipId => $itemId) {
                if (! $itemId) {
                    continue;
                }
                if (! array_key_exists($tipId, $precios)) {
                    throw ValidationException::withMessages(['alquileres' => 'Ese equipo ya no está disponible para este tour.']);
                }
                $uso[$itemId] = ($uso[$itemId] ?? 0) + 1;
            }
        }
        if (! $uso) {
            return [];
        }

        $items = ItemEquipo::with('tipo')->whereIn('id', array_keys($uso))->lockForUpdate()->get()->keyBy('id');
        foreach ($uso as $itemId => $cantidad) {
            $item = $items[$itemId] ?? null;
            if (! $item || $item->cnt_dis < $cantidad) {
                $nombre = $item ? "{$item->tipo->nom} talla {$item->tal}" : 'el equipo elegido';
                throw ValidationException::withMessages(['alquileres' => "No queda stock suficiente de {$nombre}."]);
            }
            $item->decrement('cnt_dis', $cantidad);
        }

        return $items->all();
    }

    private function devolverEquipo(Reserva $reserva): void
    {
        $uso = AlquilerEquipoReserva::whereIn('ptc_rsv_id', $reserva->participantes()->pluck('id'))
            ->selectRaw('itm_eqp_id, COUNT(*) AS cnt')
            ->groupBy('itm_eqp_id')
            ->pluck('cnt', 'itm_eqp_id');

        foreach ($uso as $itemId => $cantidad) {
            $item = ItemEquipo::lockForUpdate()->find($itemId);
            if ($item) {
                $item->update(['cnt_dis' => min($item->cnt_tot, $item->cnt_dis + (int) $cantidad)]);
            }
        }
    }
}
