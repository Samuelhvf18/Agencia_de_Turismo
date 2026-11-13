<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComprobantePago;
use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanzaController extends Controller
{
    public function index(Request $request)
    {
        $query = ComprobantePago::with(['reserva.salidaTour.tour', 'pagos']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('cod', 'ILIKE', "%{$search}%")
                  ->orWhereHas('reserva', function ($r) use ($search) {
                      $r->where('cod', 'ILIKE', "%{$search}%")
                        ->orWhere('nom_ctc', 'ILIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('est', $request->estado);
        }

        $comprobantes = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $cobrosHoy = Pago::whereDate('pag_en', now()->toDateString())->where('est', 'completado')->sum('mto');
        $cobrosTotal = Pago::where('est', 'completado')->sum('mto');
        $comprobantesPendientes = ComprobantePago::where('est', 'emitido')->count();
        $montoPendiente = ComprobantePago::where('est', 'emitido')->sum('mto_tot');

        return view('admin.panels.finanzas', compact(
            'comprobantes',
            'cobrosHoy',
            'cobrosTotal',
            'comprobantesPendientes',
            'montoPendiente'
        ));
    }

    public function storePago(Request $request)
    {
        $request->validate([
            'cmp_pag_id' => 'required|exists:comprobantes_pago,id',
            'mtd'        => 'required|string|in:QR,Efectivo',
            'mto'        => 'required|numeric|min:1',
            'ref_trx'    => 'nullable|string|max:100',
        ], [
            'cmp_pag_id.required' => 'El comprobante es obligatorio.',
            'mtd.required'        => 'Seleccione un método de pago válido.',
            'mto.required'        => 'Ingrese el monto del pago.',
            'mto.min'             => 'El monto debe ser mayor a 0.',
        ]);

        $comprobante = ComprobantePago::with('reserva')->findOrFail($request->cmp_pag_id);

        if ($comprobante->est === 'anulado') {
            return redirect()->back()->with('error', 'No se puede registrar pagos a un comprobante anulado.');
        }

        DB::transaction(function () use ($comprobante, $request) {
            Pago::create([
                'cmp_pag_id' => $comprobante->id,
                'mtd'        => $request->mtd,
                'mto'        => $request->mto,
                'ref_trx'    => $request->ref_trx ? trim($request->ref_trx) : 'TRX-' . strtoupper(uniqid()),
                'est'        => 'completado',
            ]);

            $totalPagado = Pago::where('cmp_pag_id', $comprobante->id)
                ->where('est', 'completado')
                ->sum('mto');

            if ($totalPagado >= $comprobante->mto_tot) {
                $comprobante->update(['est' => 'pagado']);
                if ($comprobante->reserva) {
                    $comprobante->reserva->update(['est_pag' => 'completado']);
                }
            } else {
                if ($comprobante->reserva) {
                    $comprobante->reserva->update(['est_pag' => 'parcial']);
                }
            }
        });

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }

    public function toggleEstado($id)
    {
        $comprobante = ComprobantePago::findOrFail($id);

        if ($comprobante->est !== 'anulado') {
            $comprobante->update(['est' => 'anulado']);
            if ($comprobante->reserva) {
                $comprobante->reserva->update(['est_pag' => 'cancelado']);
            }
            $mensaje = "Comprobante #{$comprobante->cod} desactivado con éxito.";
            $nuevoEstado = 'anulado';
        } else {
            $comprobante->update(['est' => 'emitido']);
            if ($comprobante->reserva) {
                $comprobante->reserva->update(['est_pag' => 'pendiente']);
            }
            $mensaje = "Comprobante #{$comprobante->cod} reactivado correctamente.";
            $nuevoEstado = 'emitido';
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'est'     => $nuevoEstado,
            ]);
        }

        return redirect()->back()->with('success', $mensaje);
    }

    public function exportarExcel()
    {
        $comprobantes = ComprobantePago::with(['reserva.salidaTour.tour', 'pagos'])
            ->orderBy('id', 'desc')
            ->get();

        $filename = "reporte_finanzas_" . date('Y-m-d_His') . ".csv";

        $headers = [
            "Content-Type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($comprobantes) {
            $file = fopen('php://output', 'w');
            
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Codigo Comprobante',
                'Fecha Emision',
                'Codigo Reserva',
                'Contacto Principal',
                'Tour Contratado',
                'Monto Total (Bs)',
                'Monto Pagado (Bs)',
                'Saldo Restante (Bs)',
                'Ultimo Metodo Pago',
                'Nro Pagos Registrados',
                'Estado Comprobante'
            ], ';');

            foreach ($comprobantes as $comp) {
                $pagado = $comp->pagos->where('est', 'completado')->sum('mto');
                $saldo = max(0, $comp->mto_tot - $pagado);
                $ultimoPago = $comp->pagos->last();

                fputcsv($file, [
                    $comp->cod,
                    \Carbon\Carbon::parse($comp->fec_emi)->format('d/m/Y'),
                    $comp->reserva->cod ?? 'S/R',
                    $comp->reserva->nom_ctc ?? 'N/A',
                    $comp->reserva->salidaTour->tour->nom ?? 'Tour General',
                    number_format($comp->mto_tot, 2, '.', ''),
                    number_format($pagado, 2, '.', ''),
                    number_format($saldo, 2, '.', ''),
                    $ultimoPago ? $ultimoPago->mtd : 'Ninguno',
                    $comp->pagos->count(),
                    ucfirst($comp->est)
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
