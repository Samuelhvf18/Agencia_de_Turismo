<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Reserva;
use App\Models\ParticipanteReserva;
use App\Models\SalidaTour;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnaliticaController extends Controller
{
    public function index()
    {
        $totalReservas = Reserva::count();
        $reservasConfirmadas = Reserva::where('est', 'confirmada')->count();
        $totalPasajeros = ParticipanteReserva::count();
        $ingresosTotales = Pago::where('est', 'completado')->sum('mto');

        $toursPopulares = Tour::select('tours.id', 'tours.nom')
            ->leftJoin('salidas_tour', 'tours.id', '=', 'salidas_tour.tur_id')
            ->leftJoin('reservas', 'salidas_tour.id', '=', 'reservas.sal_tur_id')
            ->selectRaw('COUNT(reservas.id) as total_reservas')
            ->selectRaw('COALESCE(SUM(reservas.cnt_ptc), 0) as total_turistas')
            ->groupBy('tours.id', 'tours.nom')
            ->orderByDesc('total_turistas')
            ->take(5)
            ->get();

        $turistasPorDepto = DB::table('departamentos_bolivia')
            ->leftJoin('participantes_reserva', 'departamentos_bolivia.id', '=', 'participantes_reserva.ci_dep_id')
            ->select('departamentos_bolivia.nom', 'departamentos_bolivia.cod', DB::raw('COUNT(participantes_reserva.id) as cantidad'))
            ->groupBy('departamentos_bolivia.id', 'departamentos_bolivia.nom', 'departamentos_bolivia.cod')
            ->orderByDesc('cantidad')
            ->get();

        $salidasRecientes = SalidaTour::with('tour')
            ->withCount(['reservas as turistas_inscritos' => function ($query) {
                $query->select(DB::raw("coalesce(sum(cnt_ptc),0)"));
            }])
            ->orderBy('fec_sal', 'desc')
            ->take(5)
            ->get();

        return view('admin.panels.analitica', compact(
            'totalReservas',
            'reservasConfirmadas',
            'totalPasajeros',
            'ingresosTotales',
            'toursPopulares',
            'turistasPorDepto',
            'salidasRecientes'
        ));
    }

    public function exportarExcel()
    {
        $reservas = Reserva::with([
            'salidaTour.tour',
            'participantes.departamento',
            'comprobantePago.pagos'
        ])->orderBy('id', 'desc')->get();

        $filename = "reporte_analitica_pasajeros_" . date('Y-m-d_His') . ".csv";

        $headers = [
            "Content-Type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($reservas) {
            $file = fopen('php://output', 'w');
            
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Codigo Reserva',
                'Fecha Registro',
                'Tour',
                'Fecha Salida',
                'Contacto',
                'Telefono',
                'Pasajero Nombre Completo',
                'Documento',
                'Extension Depto',
                'Edad',
                'Precio Total Reserva (Bs)',
                'Estado Reserva',
                'Estado Pago'
            ], ';');

            foreach ($reservas as $rsv) {
                $tourNom = $rsv->salidaTour->tour->nom ?? 'N/A';
                $fecSal = $rsv->salidaTour ? \Carbon\Carbon::parse($rsv->salidaTour->fec_sal)->format('d/m/Y') : 'N/A';
                $fecReg = \Carbon\Carbon::parse($rsv->cre_en)->format('d/m/Y H:i');

                if ($rsv->participantes->count() > 0) {
                    foreach ($rsv->participantes as $p) {
                        fputcsv($file, [
                            $rsv->cod,
                            $fecReg,
                            $tourNom,
                            $fecSal,
                            $rsv->nom_ctc,
                            $rsv->tel_ctc,
                            $p->nom_cpl,
                            ($p->tip_doc ?? 'CI') . ': ' . ($p->num_doc ?? 'S/N') . ($p->ci_cmp ? '-' . $p->ci_cmp : ''),
                            $p->departamento->nom ?? 'N/A',
                            $p->eda ?? 'S/D',
                            number_format($rsv->pre_tot, 2, '.', ''),
                            ucfirst($rsv->est),
                            ucfirst($rsv->est_pag)
                        ], ';');
                    }
                } else {
                    fputcsv($file, [
                        $rsv->cod,
                        $fecReg,
                        $tourNom,
                        $fecSal,
                        $rsv->nom_ctc,
                        $rsv->tel_ctc,
                        'Sin pasajeros registrados',
                        'N/A',
                        'N/A',
                        'N/A',
                        number_format($rsv->pre_tot, 2, '.', ''),
                        ucfirst($rsv->est),
                        ucfirst($rsv->est_pag)
                    ], ';');
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
