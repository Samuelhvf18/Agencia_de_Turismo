<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\ParticipanteReserva;
use App\Models\SalidaTour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $query = Reserva::with(['salidaTour.tour', 'participantes']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cod', 'ILIKE', "%{$search}%")
                ->orWhere('nom_ctc', 'ILIKE', "%{$search}%")
                ->orWhereHas('participantes', function($p) use ($search) {
                    $p->where('nom_cpl', 'ILIKE', "%{$search}%")
                        ->orWhere('num_doc', 'ILIKE', "%{$search}%");
                });
            });
        }

        $reservas = $query->orderBy('cre_en', 'desc')->paginate(10);

        return view('admin.panels.reservas', compact('reservas'));
    }

    public function getCreateData()
    {
        $salidas = SalidaTour::with('tour')
            ->where('est', 'programada')
            ->orderBy('fec_sal', 'asc')
            ->get();

        $departamentos = DB::table('departamentos_bolivia')->select('id', 'nom', 'cod')->get();
        $paises = DB::table('paises')->select('id', 'nom', 'cod_iso')->get();

        return response()->json([
            'salidas' => $salidas,
            'departamentos' => $departamentos,
            'paises' => $paises
        ]);
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'sal_tur_id' => 'required|exists:salidas_tour,id',
        'nom_ctc'    => 'required|string|max:150',
        'cor_ctc'    => 'required|email|max:150',
        'tel_ctc'    => 'required|string|max:20',
        'participantes' => 'required|array|min:1',
        'participantes.*.nom_cpl'   => 'required|string|max:150',
        'participantes.*.tip_doc'   => 'nullable|string|max:20',
        'participantes.*.num_doc'   => 'nullable|string|max:15',
        'participantes.*.ci_cmp'    => 'nullable|string|max:5',
        'participantes.*.ci_dep_id' => 'nullable|exists:departamentos_bolivia,id',
        'participantes.*.eda'       => 'nullable|integer|min:1|max:120',
        'participantes.*.pai_id'    => 'nullable|exists:paises,id',
    ]);

    return DB::transaction(function () use ($validated) {
        $salida = SalidaTour::with('tour')->findOrFail($validated['sal_tur_id']);
        
        // Verificación de seguridad por si la salida no tiene un tour asociado
        if (!$salida->tour) {
            throw new \Exception('La salida seleccionada no tiene un tour válido asociado.');
        }

        $cnt_ptc = count($validated['participantes']);
        $pre_bas = $salida->tour->pre_bas ?? 0;
        $pre_tot = $pre_bas * $cnt_ptc;

        $reserva = Reserva::create([
            'cod'        => 'RES-' . strtoupper(Str::random(6)),
            'sal_tur_id' => $salida->id,
            'usr_id'     => Auth::id(),
            'nom_ctc'    => $validated['nom_ctc'],
            'cor_ctc'    => $validated['cor_ctc'],
            'tel_ctc'    => $validated['tel_ctc'],
            'cnt_ptc'    => $cnt_ptc,
            'est'        => 'confirmada',
            'pre_tot'    => $pre_tot,
            'est_pag'    => 'pendiente',
        ]);

        foreach ($validated['participantes'] as $index => $ptc) {
            ParticipanteReserva::create([
                'rsv_id'     => $reserva->id,
                'nom_cpl'   => $ptc['nom_cpl'],
                'tip_doc'   => $ptc['tip_doc'] ?? 'CI',
                'num_doc'   => $ptc['num_doc'] ?? null,
                'ci_cmp'    => $ptc['ci_cmp'] ?? '',
                'ci_dep_id' => $ptc['ci_dep_id'] ?? null,
                'eda'       => $ptc['eda'] ?? null,
                'pai_id'    => $ptc['pai_id'] ?? null,
                'es_ctc_pri' => ($index === 0),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reserva y pasajeros registrados correctamente.',
            'reserva' => $reserva
        ]);
    });
}

    public function show($id)
    {
        $reserva = Reserva::with([
            'salidaTour.tour',
            'participantes.fichasMedicas'
        ])->findOrFail($id);

        return response()->json($reserva);
    }

    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['est' => 'cancelada']);

        return response()->json([
            'success' => true,
            'message' => 'La reserva ha sido cancelada correctamente.'
        ]);
    }
}