<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reservas\AccesoReserva;
use App\Models\Pago;
use App\Models\Reserva;
use App\Services\Pagos\PagoService;
use App\Services\Pagos\PasarelaSimulada;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Pago de una reserva con la pasarela simulada (Módulo 06):
 * 1) el cliente elige método y monto (se admite pago parcial),
 * 2) "paga" en la pantalla de la pasarela,
 * 3) la pasarela notifica al webhook firmado, que confirma o rechaza el pago.
 */
class PagoController extends Controller
{
    public function __construct(private PagoService $pagos) {}

    public function create(Request $request, Reserva $reserva): View|RedirectResponse
    {
        AccesoReserva::exigir($request, $reserva);
        if (! $this->pagos->admitePago($reserva)) {
            return redirect()->route('reservas.estado', $reserva)->with('estado', __('Esta reserva no tiene pagos pendientes.'));
        }
        $reserva->load('salida.tour', 'comprobante.pagos');

        return view('pagos.create', [
            'reserva' => $reserva,
            'tour' => $reserva->salida->tour,
            'salida' => $reserva->salida,
            'metodos' => PagoService::METODOS,
            'saldo' => $this->pagos->saldo($reserva),
            'pagado' => $this->pagos->montoPagado($reserva),
            'minimo' => $this->pagos->montoMinimo($reserva),
            'historial' => $reserva->comprobante?->pagos->sortByDesc('pag_en')->values() ?? collect(),
        ]);
    }

    public function store(Request $request, Reserva $reserva): RedirectResponse
    {
        AccesoReserva::exigir($request, $reserva);
        $datos = $request->validate([
            'mtd' => ['required', Rule::in(array_keys(PagoService::METODOS))],
            'mto' => ['required', 'numeric', 'min:0.01', 'max:99999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
        ], [
            'mtd.*' => __('Elige un método de pago.'),
            'mto.*' => __('Ingresa un monto válido (hasta 2 decimales).'),
        ]);

        $pago = $this->pagos->iniciar($reserva, $datos['mtd'], (float) $datos['mto']);

        return redirect()->route('pasarela.show', $pago->ref_trx);
    }

    // ---- Pantalla de la pasarela simulada ---------------------------------------

    public function pasarela(Request $request, string $ref): View|RedirectResponse
    {
        $pago = Pago::with('comprobante.reserva.salida.tour')->where('ref_trx', $ref)->firstOrFail();
        $reserva = $pago->comprobante->reserva;
        AccesoReserva::exigir($request, $reserva);

        if ($pago->est !== Pago::EST_PENDIENTE) {
            return redirect()->route('reservas.estado', $reserva);
        }

        return view('pagos.pasarela', ['pago' => $pago, 'reserva' => $reserva, 'metodos' => PagoService::METODOS]);
    }

    public function resolver(Request $request, string $ref, PasarelaSimulada $pasarela): RedirectResponse
    {
        $pago = Pago::with('comprobante.reserva')->where('ref_trx', $ref)->firstOrFail();
        $reserva = $pago->comprobante->reserva;
        AccesoReserva::exigir($request, $reserva);
        $aprobar = $request->validate(['resultado' => ['required', 'in:aprobar,rechazar']])['resultado'] === 'aprobar';

        // La pasarela "externa" notifica el resultado al webhook firmado.
        $pasarela->notificar([
            'ref_trx' => $pago->ref_trx,
            'est' => $aprobar ? Pago::EST_COMPLETADO : Pago::EST_FALLIDO,
            'mto' => (float) $pago->mto,
            'fec' => now()->toIso8601String(),
        ]);

        $pago->refresh();
        $mensaje = match ($pago->est) {
            Pago::EST_COMPLETADO => __('Pago aprobado. ¡Gracias!'),
            Pago::EST_FALLIDO => __('El pago fue rechazado. Puedes intentarlo de nuevo.'),
            default => __('Estamos esperando la confirmación de la pasarela.'),
        };

        return redirect()->route('reservas.estado', $reserva)->with('estado', $mensaje);
    }

    // ---- Webhook ------------------------------------------------------------------

    public function webhook(Request $request): JsonResponse
    {
        $cuerpo = $request->getContent();
        if (! PasarelaSimulada::verificar($cuerpo, $request->header(PasarelaSimulada::CABECERA_FIRMA))) {
            return response()->json(['ok' => false, 'error' => __('Firma inválida')], 401);
        }

        $evento = json_decode($cuerpo, true);
        if (! is_array($evento) || empty($evento['ref_trx']) || empty($evento['est'])) {
            return response()->json(['ok' => false, 'error' => 'Evento incompleto'], 422);
        }

        $pago = $this->pagos->procesarNotificacion($evento);

        return response()->json(['ok' => (bool) $pago, 'est' => $pago?->est], $pago ? 200 : 404);
    }
}
