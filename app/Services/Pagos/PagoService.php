<?php

namespace App\Services\Pagos;

use App\Jobs\EnviarBoletosReserva;
use App\Models\ComprobantePago;
use App\Models\Pago;
use App\Models\Reserva;
use App\Services\CodeGeneratorService;
use App\Services\Notificaciones\NotificacionService;
use App\Services\Reservas\BoletoService;
use App\Services\Reservas\ReservaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Facturación y pagos (Módulo 06, RF-13, RF-14, RNF-19).
 *
 * - Cada intento de pago queda en `pagos` (pendiente → completado/fallido).
 * - Se admiten pagos parciales: la reserva pasa a est_pag = parcial hasta
 *   cubrir el total.
 * - El comprobante (comprobantes_pago) se crea con el primer intento y se
 *   marca pagado al cubrir el total; nunca se borra, solo se anula.
 */
class PagoService
{
    public const METODOS = ['tarjeta' => 'Tarjeta', 'qr' => 'QR bancario', 'transferencia' => 'Transferencia'];

    /** IVA incluido en el precio (Bolivia: 13 %). */
    public const TASA_IVA = 0.13;

    /** Primer pago mínimo: 30 % del total (o el saldo, si es menor). */
    public const PORCENTAJE_MINIMO = 0.30;

    public function __construct(
        private ReservaService $reservas,
        private BoletoService $boletos,
    ) {}

    public function montoPagado(Reserva $reserva): float
    {
        return $reserva->comprobante ? $reserva->comprobante->montoPagado() : 0.0;
    }

    public function saldo(Reserva $reserva): float
    {
        return round((float) $reserva->pre_tot - $this->montoPagado($reserva), 2);
    }

    public function montoMinimo(Reserva $reserva): float
    {
        return round(min($this->saldo($reserva), (float) $reserva->pre_tot * self::PORCENTAJE_MINIMO), 2);
    }

    public function admitePago(Reserva $reserva): bool
    {
        return $reserva->est === Reserva::EST_PENDIENTE
            || ($reserva->est === Reserva::EST_CONFIRMADA && $this->saldo($reserva) > 0);
    }

    /** Registra un intento de pago pendiente y devuelve el pago creado. */
    public function iniciar(Reserva $reserva, string $metodo, float $monto): Pago
    {
        return DB::transaction(function () use ($reserva, $metodo, $monto) {
            $reserva = Reserva::lockForUpdate()->findOrFail($reserva->id);

            if (! $this->admitePago($reserva)) {
                throw ValidationException::withMessages(['mto' => 'Esta reserva ya no admite pagos.']);
            }
            if (! array_key_exists($metodo, self::METODOS)) {
                throw ValidationException::withMessages(['mtd' => 'Elige un método de pago válido.']);
            }

            $saldo = $this->saldo($reserva);
            $minimo = $this->montoMinimo($reserva);
            $monto = round($monto, 2);
            if ($monto < $minimo || $monto > $saldo) {
                throw ValidationException::withMessages([
                    'mto' => sprintf('El monto debe estar entre Bs. %s y Bs. %s.', number_format($minimo, 2, ',', '.'), number_format($saldo, 2, ',', '.')),
                ]);
            }

            $comprobante = $reserva->comprobante ?? $this->crearComprobante($reserva);
            if ($comprobante->pagos()->where('est', Pago::EST_PENDIENTE)->exists()) {
                throw ValidationException::withMessages(['mto' => 'Existe un intento de pago pendiente. Resuélvelo antes de iniciar otro.']);
            }

            return $comprobante->pagos()->create([
                'mtd' => $metodo,
                'mto' => $monto,
                'ref_trx' => 'SIM-'.strtoupper(Str::random(12)),
                'est' => Pago::EST_PENDIENTE,
            ]);
        });
    }

    /**
     * Procesa la notificación (webhook) de la pasarela. Es idempotente: un
     * pago que ya no está pendiente no se vuelve a procesar.
     *
     * @param  array{ref_trx: string, est: string, mto?: float}  $evento
     */
    public function procesarNotificacion(array $evento): ?Pago
    {
        $confirmada = false;
        $procesado = false;

        $pago = DB::transaction(function () use ($evento, &$confirmada, &$procesado) {
            $referencia = Pago::where('ref_trx', $evento['ref_trx'] ?? '')->first();
            if (! $referencia) {
                return null;
            }
            // Orden global de bloqueos: reserva → comprobante → pago.
            $cmp = ComprobantePago::findOrFail($referencia->cmp_pag_id);
            $reserva = Reserva::lockForUpdate()->findOrFail($cmp->rsv_id);
            $comprobante = ComprobantePago::lockForUpdate()->findOrFail($cmp->id);
            $pago = Pago::lockForUpdate()->findOrFail($referencia->id);
            if (! $pago || $pago->est !== Pago::EST_PENDIENTE) {
                return $pago;
            }

            $aprobado = ($evento['est'] ?? null) === Pago::EST_COMPLETADO
                && (! isset($evento['mto']) || abs((float) $evento['mto'] - (float) $pago->mto) < 0.01);

            // No contabilizar cobros tardíos ni excedentes como dinero aprobado.
            // La pasarela simulada los rechaza; un proveedor real exige conciliación/reembolso.
            $aprobado = $aprobado && $this->admitePago($reserva)
                && $comprobante->est !== ComprobantePago::EST_ANULADO
                && (float) $pago->mto <= round((float) $reserva->pre_tot - $comprobante->montoPagado(), 2);
            $procesado = true;
            $pago->update(['est' => $aprobado ? Pago::EST_COMPLETADO : Pago::EST_FALLIDO, 'pag_en' => now()]);
            if (! $aprobado) {
                return $pago;
            }

            $pagado = $comprobante->montoPagado();

            if ($pagado + 0.009 >= (float) $comprobante->mto_tot) {
                $comprobante->update(['est' => ComprobantePago::EST_PAGADO]);
                $reserva->update(['est_pag' => Reserva::PAG_PAGADO]);
                $this->reservas->marcarConfirmada($reserva);
                $this->boletos->emitir($reserva);
                $confirmada = true;
            } else {
                $reserva->update(['est_pag' => Reserva::PAG_PARCIAL]);
            }

            return $pago;
        });

        if ($confirmada) {
            // Boletos (PDF con QR) y comprobante por correo; queda en registros_notificacion.
            EnviarBoletosReserva::dispatch($pago->comprobante->rsv_id)->afterCommit();
        } elseif ($procesado && $pago?->est === Pago::EST_COMPLETADO) {
            $reserva = $pago->comprobante->reserva;
            app(NotificacionService::class)->aReserva($reserva, 'pago_recibido', [
                'mensaje' => sprintf('Recibimos tu pago de Bs. %s. Saldo pendiente: Bs. %s. Los tickets se emiten al completar el pago.',
                    number_format((float) $pago->mto, 2, ',', '.'), number_format($this->saldo($reserva), 2, ',', '.')),
                'monto' => (float) $pago->mto,
                'saldo' => $this->saldo($reserva),
            ]);
        }

        return $pago;
    }

    /** Cobro en efectivo registrado por el personal: cualquier monto hasta el saldo. */
    public function registrarEfectivo(Reserva $reserva, float $monto): Pago
    {
        $pago = DB::transaction(function () use ($reserva, $monto) {
            $reserva = Reserva::lockForUpdate()->findOrFail($reserva->id);
            $monto = round($monto, 2);
            if (! $this->admitePago($reserva) || $monto <= 0 || $monto > $this->saldo($reserva)) {
                throw ValidationException::withMessages(['mto' => 'El monto supera el saldo o la reserva no admite pagos.']);
            }
            $comprobante = $reserva->comprobante ?? $this->crearComprobante($reserva);
            if ($comprobante->pagos()->where('est', Pago::EST_PENDIENTE)->exists()) {
                throw ValidationException::withMessages(['mto' => 'Existe un intento de pago pendiente. Resuélvelo antes de iniciar otro.']);
            }

            return $comprobante->pagos()->create([
                'mtd' => 'efectivo',
                'mto' => $monto,
                'ref_trx' => 'EFE-'.strtoupper(Str::random(12)),
                'est' => Pago::EST_PENDIENTE,
            ]);
        });

        return $this->procesarNotificacion(['ref_trx' => $pago->ref_trx, 'est' => Pago::EST_COMPLETADO, 'mto' => (float) $pago->mto]);
    }

    /**
     * Anula el comprobante (sin borrado físico) y cancela la reserva; lo ya
     * cobrado queda como reembolsado.
     */
    public function anularComprobante(ComprobantePago $comprobante, string $motivo): void
    {
        DB::transaction(function () use ($comprobante, $motivo) {
            $reserva = Reserva::lockForUpdate()->findOrFail($comprobante->rsv_id);
            $comprobante = ComprobantePago::lockForUpdate()->findOrFail($comprobante->id);
            if ($comprobante->est === ComprobantePago::EST_ANULADO) {
                return;
            }

            $comprobante->pagos()->where('est', Pago::EST_PENDIENTE)->update(['est' => Pago::EST_FALLIDO]);
            $comprobante->update(['est' => ComprobantePago::EST_ANULADO]);
            $this->reservas->cancelar($comprobante->reserva, "Comprobante anulado: {$motivo}");
        });
    }

    private function crearComprobante(Reserva $reserva): ComprobantePago
    {
        $total = round((float) $reserva->pre_tot, 2);
        $subtotal = round($total / (1 + self::TASA_IVA), 2);

        return ComprobantePago::create([
            'cod' => CodeGeneratorService::generate('CMP'),
            'rsv_id' => $reserva->id,
            'fec_emi' => today(),
            'sub' => $subtotal,
            'imp' => round($total - $subtotal, 2),
            'mto_tot' => $total,
            'est' => ComprobantePago::EST_PENDIENTE,
        ]);
    }
}
