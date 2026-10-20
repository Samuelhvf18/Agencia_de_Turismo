<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Controllers\Controller;
use App\Models\Boleto;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Observers\AuditObserver;
use App\Services\Pagos\ComprobanteService;
use App\Services\Pagos\PagoService;
use App\Services\Reservas\BoletoService;
use App\Services\Reservas\ReservaService;
use App\Support\PresentadorTour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Consulta pública de reservas por código + correo (RF-12), estado con
 * boletos QR, descarga de boletos y comprobante en PDF y cancelación.
 */
class ConsultaReservaController extends Controller
{
    public function __construct(
        private BoletoService $boletos,
        private PagoService $pagos,
    ) {}

    public function consultar(): View
    {
        return view('reservas.consultar', ['accionForm' => route('reservas.consultar.buscar')]);
    }

    public function buscar(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cod' => ['required', 'string', 'max:20'],
            'cor_ctc' => ['required', 'email', 'max:150'],
        ]);

        $reserva = Reserva::where('cod', strtoupper(trim($datos['cod'])))
            ->where('cor_ctc', strtolower(trim($datos['cor_ctc'])))
            ->first();

        if (! $reserva) {
            return back()->withErrors(['cod' => 'No encontramos una reserva con esos datos.'])->withInput();
        }

        AccesoReserva::conceder($request, $reserva);

        return redirect()->route('reservas.estado', $reserva);
    }

    public function estado(Request $request, Reserva $reserva): View
    {
        AccesoReserva::exigir($request, $reserva);
        $reserva->load('salida.tour.categoria', 'salida.tour.fotos', 'salida.guia.usuario', 'participantes.boleto', 'participantes.fichaMedica', 'comprobante.pagos');
        $tour = PresentadorTour::presentar($reserva->salida->tour);

        // RNF-05: la consulta de datos personales de participantes queda en la bitácora.
        foreach ($reserva->participantes as $p) {
            AuditObserver::lectura($p);
        }

        return view('reservas.estado', [
            'reserva' => $reserva->only(['cod', 'nom_ctc', 'cor_ctc', 'tel_ctc', 'cnt_ptc', 'est', 'pre_tot', 'est_pag', 'cre_en']),
            'tour' => $tour,
            'salida' => [
                'fec_sal' => $reserva->salida->fec_sal->toDateString(),
                'hra_sal' => $reserva->salida->hra_sal,
                'est' => $reserva->salida->est,
                'gui_nom' => $reserva->salida->guia?->usuario?->nom,
            ],
            'participantes' => $reserva->participantes->map(fn ($p) => [
                'nom_cpl' => $p->nom_cpl,
                'tip_doc' => $p->tip_doc,
                'num_doc' => $p->num_doc,
                'es_ctc_pri' => $p->es_ctc_pri,
                'ficha_ok' => (bool) $p->fichaMedica,
                'boleto' => $p->boleto && $p->boleto->est !== Boleto::EST_CANCELADO ? [
                    'cod' => $p->boleto->cod,
                    'est' => $p->boleto->est,
                    'qr' => $this->boletos->qrDataUri($p->boleto->cod),
                    'url' => route('boletos.pdf', $p->boleto),
                ] : null,
            ])->all(),
            'historialPagos' => $reserva->comprobante?->pagos->sortByDesc('pag_en')->values() ?? collect(),
            'metodos' => PagoService::METODOS,
            'saldo' => $this->pagos->saldo($reserva),
            'urlPago' => $this->pagos->admitePago($reserva) ? route('pagos.create', $reserva) : null,
            'urlComprobante' => $reserva->comprobante ? route('reservas.comprobante', $reserva) : null,
            'urlBoletos' => $reserva->boletos()->where('boletos.est', '<>', Boleto::EST_CANCELADO)->exists() ? route('reservas.boletos', $reserva) : null,
            'urlCancelar' => $this->cancelable($reserva) ? route('reservas.cancelar', $reserva) : null,
            'resena' => $reserva->resena,
            'urlResena' => $reserva->est === Reserva::EST_COMPLETADA && ! $reserva->resena ? route('reservas.resena', $reserva) : null,
            'urlConsultar' => route('reservas.consultar'),
        ]);
    }

    public function cancelar(Request $request, Reserva $reserva, ReservaService $servicio): RedirectResponse
    {
        AccesoReserva::exigir($request, $reserva);
        abort_unless($this->cancelable($reserva), 422, 'Esta reserva ya no se puede cancelar.');

        $servicio->cancelar($reserva, 'Cancelada por el cliente');

        return redirect()->route('reservas.estado', $reserva)->with('estado', 'Tu reserva fue cancelada y los cupos se liberaron.');
    }

    public function boletosPdf(Request $request, Reserva $reserva): Response
    {
        AccesoReserva::exigir($request, $reserva);
        $boletos = $reserva->boletos()->where('boletos.est', '<>', Boleto::EST_CANCELADO)->get();
        abort_if($boletos->isEmpty(), 404, 'Los boletos se emiten cuando el pago está completo.');

        return $this->boletos->pdf($reserva, $boletos)->download("boletos-{$reserva->cod}.pdf");
    }

    public function boletoPdf(Request $request, Boleto $boleto): Response
    {
        $reserva = $boleto->participante->reserva;
        AccesoReserva::exigir($request, $reserva);
        abort_if($boleto->est === Boleto::EST_CANCELADO, 404);

        return $this->boletos->pdf($reserva, collect([$boleto]))->download("boleto-{$boleto->cod}.pdf");
    }

    public function comprobantePdf(Request $request, Reserva $reserva, ComprobanteService $comprobantes): Response
    {
        AccesoReserva::exigir($request, $reserva);
        abort_unless($reserva->comprobante, 404);

        return $comprobantes->pdf($reserva->comprobante)->download("comprobante-{$reserva->comprobante->cod}.pdf");
    }

    /** Se puede cancelar mientras no esté cancelada/completada y la salida sea a partir de mañana. */
    private function cancelable(Reserva $reserva): bool
    {
        return in_array($reserva->est, [Reserva::EST_PENDIENTE, Reserva::EST_CONFIRMADA], true)
            && $reserva->salida->est === SalidaTour::EST_PROGRAMADA
            && $reserva->salida->fec_sal->gt(today());
    }
}
