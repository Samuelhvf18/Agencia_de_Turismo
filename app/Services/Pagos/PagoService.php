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

/** Facturación y pagos parciales. */
class PagoService
{
    public const METODOS = ['tarjeta' => 'Tarjeta', 'qr' => 'QR bancario', 'transferencia' => 'Transferencia'];

    /** IVA incluido del 13 %. */
    public const TASA_IVA = 0.13;

    /** Mínimo inicial sujeto al saldo. */
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

    /** Crea un pago pendiente. */
    public function iniciar(Reserva $reserva, string $metodo, float $monto): Pago
    {
        return DB::transaction(function () use ($reserva, $metodo, $monto) {
            $reserva = Reserva::lockForUpdate()->findOrFail($reserva->id);

            if (! $this->admitePago($reserva)) {
                throw ValidationException::withMessages(['mto' => __('Esta reserva ya no admite pagos.')]);
            }
            if (! array_key_exists($metodo, self::METODOS)) {
                throw ValidationException::withMessages(['mtd' => __('Elige un método de pago válido.')]);
            }

            $saldo = $this->saldo($reserva);
            $minimo = $this->montoMinimo($reserva);
            $monto = round($monto, 2);
            if ($monto < $minimo || $monto > $saldo) {
                throw ValidationException::withMessages([
                    'mto' => sprintf(__('El monto debe estar entre Bs. %s y Bs. %s.'), number_format($minimo, 2, ',', '.'), number_format($saldo, 2, ',', '.')),
                ]);
            }

            $comprobante = $reserva->comprobante ?? $this->crearComprobante($reserva);
            if ($comprobante->pagos()->where('est', Pago::EST_PENDIENTE)->exists()) {
                throw ValidationException::withMessages(['mto' => __('Existe un intento de pago pendiente. Resuélvelo antes de iniciar otro.')]);
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
     * Webhook idempotente de pasarela.
     *
     * @param  array{ref_trx: string, est: string, mto?: float}  $evento
     */
    public function procesarNotificacion(array $evento): ?Pago
    {
        $confirmada = false;

        $pago = DB::transaction(function () use ($evento, &$confirmada) {
            $referencia = Pago::where('ref_trx', $evento['ref_trx'] ?? '')->first();
            if (! $referencia) {
                return null;
            }
            // Bloqueo: reserva, comprobante, pago.
            $cmp = ComprobantePago::findOrFail($referencia->cmp_pag_id);
            $reserva = Reserva::lockForUpdate()->findOrFail($cmp->rsv_id);
            $comprobante = ComprobantePago::lockForUpdate()->findOrFail($cmp->id);
            $pago = Pago::lockForUpdate()->findOrFail($referencia->id);
            if (! $pago || $pago->est !== Pago::EST_PENDIENTE) {
                return $pago;
            }

            $aprobado = ($evento['est'] ?? null) === Pago::EST_COMPLETADO
                && (! isset($evento['mto']) || abs((float) $evento['mto'] - (float) $pago->mto) < 0.01);

            // Rechaza cobros tardíos o excedentes.
            // Proveedores reales requieren conciliación.
            $aprobado = $aprobado && $this->admitePago($reserva)
                && $comprobante->est !== ComprobantePago::EST_ANULADO
                && (float) $pago->mto <= round((float) $reserva->pre_tot - $comprobante->montoPagado(), 2);
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
                app(NotificacionService::class)->confirmacionReserva($reserva);
                $confirmada = true;
            } else {
                $reserva->update(['est_pag' => Reserva::PAG_PARCIAL]);
                app(NotificacionService::class)->aReserva($reserva, 'pago_recibido', [
                    'mensaje' => __('Recibimos tu pago de Bs. :monto. Saldo pendiente: Bs. :saldo.', ['monto' => number_format((float) $pago->mto, 2), 'saldo' => number_format($this->saldo($reserva), 2)], $reserva->idioma ?? 'es'),
                    'monto' => (float) $pago->mto, 'saldo' => $this->saldo($reserva),
                ], clave: 'pago-recibido:'.$pago->id);
            }

            return $pago;
        });

        if ($confirmada) {
            try {
                EnviarBoletosReserva::dispatch($pago->comprobante->rsv_id)->afterCommit();
            } catch (\Throwable $e) {
                report($e);
            } // durable event remains recoverable
        }

        return $pago;
    }

    /** Efectivo hasta cubrir el saldo. */
    public function registrarEfectivo(Reserva $reserva, float $monto): Pago
    {
        $pago = DB::transaction(function () use ($reserva, $monto) {
            $reserva = Reserva::lockForUpdate()->findOrFail($reserva->id);
            $monto = round($monto, 2);
            if (! $this->admitePago($reserva) || $monto <= 0 || $monto > $this->saldo($reserva)) {
                throw ValidationException::withMessages(['mto' => __('El monto supera el saldo o la reserva no admite pagos.')]);
            }
            $comprobante = $reserva->comprobante ?? $this->crearComprobante($reserva);
            if ($comprobante->pagos()->where('est', Pago::EST_PENDIENTE)->exists()) {
                throw ValidationException::withMessages(['mto' => __('Existe un intento de pago pendiente. Resuélvelo antes de iniciar otro.')]);
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

    /** Anulación lógica y reembolso. */
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
