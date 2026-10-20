<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComprobantePago;
use App\Models\Pago;
use App\Models\Reserva;
use App\Services\Pagos\ComprobanteService;
use App\Services\Pagos\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/** Finanzas: cobros por método, saldos, comprobantes, anulación y pagos en efectivo. */
class FinanzasController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'est' => ['nullable', 'in:pendiente,pagado,anulado'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);
        $desde = $filtros['desde'] ?? today()->startOfMonth()->toDateString();
        $hasta = $filtros['hasta'] ?? today()->toDateString();

        $cobros = Pago::where('est', Pago::EST_COMPLETADO)
            ->whereDate('pag_en', '>=', $desde)->whereDate('pag_en', '<=', $hasta)
            ->selectRaw('mtd, COUNT(*) AS n, SUM(mto) AS total')
            ->groupBy('mtd')->get()->keyBy('mtd');

        $porCobrar = (float) ComprobantePago::where('est', ComprobantePago::EST_PENDIENTE)->sum('mto_tot')
            - (float) Pago::where('est', Pago::EST_COMPLETADO)
                ->whereIn('cmp_pag_id', ComprobantePago::where('est', ComprobantePago::EST_PENDIENTE)->select('id'))
                ->sum('mto');

        $comprobantes = ComprobantePago::with(['reserva.salida.tour', 'pagos'])
            ->when($filtros['est'] ?? null, fn ($q, $est) => $q->where('est', $est))
            ->whereDate('fec_emi', '>=', $desde)->whereDate('fec_emi', '<=', $hasta)
            ->orderByDesc('id')
            ->paginate(20)->withQueryString();

        return view('admin.panels.finanzas', [
            'cobros' => $cobros,
            'porCobrar' => max(0, $porCobrar),
            'comprobantes' => $comprobantes,
            'metodos' => PagoService::METODOS + ['efectivo' => 'Efectivo'],
            'filtros' => $filtros + ['desde' => $desde, 'hasta' => $hasta],
            'pagosRecientes' => Pago::with('comprobante.reserva')->orderByDesc('pag_en')->limit(10)->get(),
        ]);
    }

    public function anular(Request $request, ComprobantePago $comprobante, PagoService $pagos): RedirectResponse
    {
        $datos = $request->validate(['motivo' => ['required', 'string', 'max:200']]);
        $pagos->anularComprobante($comprobante, $datos['motivo']);

        return back()->with('estado', "Comprobante {$comprobante->cod} anulado y reserva cancelada.");
    }

    /** Cobro en efectivo en oficina: se registra como pago completado. */
    public function efectivo(Request $request, Reserva $reserva, PagoService $pagos): RedirectResponse
    {
        $datos = $request->validate(['mto' => ['required', 'numeric', 'min:0.01', 'regex:/^\d+(\.\d{1,2})?$/']]);
        $pago = $pagos->registrarEfectivo($reserva, (float) $datos['mto']);

        return back()->with('estado', "Pago en efectivo de Bs. {$pago->mto} registrado.");
    }

    public function comprobantePdf(ComprobantePago $comprobante, ComprobanteService $servicio): Response
    {
        return $servicio->pdf($comprobante)->download("comprobante-{$comprobante->cod}.pdf");
    }
}
