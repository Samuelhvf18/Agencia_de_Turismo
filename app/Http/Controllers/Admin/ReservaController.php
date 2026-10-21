<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\ParticipanteReserva;
use App\Models\SalidaTour;
use App\Models\Boleto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReservaController extends Controller
{

    public function index(Request $request)
    {
        $query = Reserva::with([
            'salidaTour.tour',
            'salidaTour.vehiculo',
            'participantes.departamento',
            'participantes.fichasMedicas',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cod', 'ILIKE', "%{$search}%")
                  ->orWhere('nom_ctc', 'ILIKE', "%{$search}%")
                  ->orWhere('cor_ctc', 'ILIKE', "%{$search}%")
                  ->orWhere('tel_ctc', 'ILIKE', "%{$search}%")
                  ->orWhereHas('participantes', fn($p) => $p->where('nom_cpl', 'ILIKE', "%{$search}%")->orWhere('num_doc', 'ILIKE', "%{$search}%"))
                  ->orWhereHas('salidaTour.tour', fn($t) => $t->where('nom', 'ILIKE', "%{$search}%"));
            });
        }

        $reservas = $query->latest('cre_en')->paginate(10)->withQueryString();

        return view('admin.panels.reservas', compact('reservas'));
    }

    public function getCreateData()
    {
        $salidas = SalidaTour::has('tour')
            ->with('tour')
            ->whereIn('est', ['programada', 'confirmada'])
            ->whereDate('fec_sal', '>=', now()->toDateString())
            ->orderBy('fec_sal', 'asc')
            ->get()
            ->map(function ($s) {
                $ocupados = (int) Reserva::where('sal_tur_id', $s->id)->where('est', '!=', 'cancelada')->sum('cnt_ptc');
                $capacidad = (int) ($s->cap_max ?? $s->tour->cap_max ?? 15);

                return [
                    'id' => $s->id,
                    'fec_sal' => date('d/m/Y', strtotime($s->fec_sal)),
                    'hra_sal' => substr($s->hra_sal ?? '08:00', 0, 5),
                    'cap_max' => $capacidad,
                    'cupos_disponibles' => max(0, $capacidad - $ocupados),
                    'tour' => [
                        'id' => $s->tour->id,
                        'nom' => $s->tour->nom,
                        'pre_bas' => (float) $s->tour->pre_bas,
                    ],
                ];
            });

        return response()->json([
            'success' => true,
            'salidas' => $salidas,
            'departamentos' => DB::table('departamentos_bolivia')->select('id', 'nom', 'cod')->orderBy('nom')->get(),
            'paises' => DB::table('paises')->select('id', 'nom', 'cod_iso')->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sal_tur_id'                => 'required|exists:salidas_tour,id',
            'nom_ctc'                   => 'required|string|max:150',
            'cor_ctc'                   => 'required|email|max:150',
            'tel_ctc'                   => 'required|string|max:20',
            'participantes'             => 'required|array|min:1',
            'participantes.*.nom_cpl'   => 'required|string|max:150',
            'participantes.*.tip_doc'   => 'nullable|string|max:20',
            'participantes.*.num_doc'   => 'nullable|string|max:20',
            'participantes.*.ci_cmp'    => 'nullable|string|max:5',
            'participantes.*.ci_dep_id' => 'nullable|exists:departamentos_bolivia,id',
            'participantes.*.eda'       => 'nullable|integer|min:1|max:120',
            'participantes.*.pai_id'    => 'nullable|exists:paises,id',
        ]);

        return DB::transaction(function () use ($validated) {
            $salida = SalidaTour::with('tour')->findOrFail($validated['sal_tur_id']);

            if (!$salida->tour) {
                return response()->json(['success' => false, 'message' => 'La salida no tiene un tour configurado.'], 422);
            }

            $cntPtc = count($validated['participantes']);
            $ocupados = Reserva::where('sal_tur_id', $salida->id)->where('est', '!=', 'cancelada')->sum('cnt_ptc');
            $capacidad = $salida->cap_max ?? $salida->tour->cap_max ?? 15;
            $cupos = max(0, $capacidad - $ocupados);

            if ($cntPtc > $cupos) {
                return response()->json(['success' => false, 'message' => "Capacidad insuficiente. Solo quedan {$cupos} cupos."], 422);
            }

            $preTot = ($salida->tour->pre_bas ?? 0) * $cntPtc;

            // Crear Reserva
            $reserva = Reserva::create([
                'cod'        => 'RES-' . strtoupper(Str::random(6)),
                'sal_tur_id' => $salida->id,
                'usr_id'     => Auth::id(),
                'nom_ctc'    => $validated['nom_ctc'],
                'cor_ctc'    => strtolower($validated['cor_ctc']),
                'tel_ctc'    => $validated['tel_ctc'],
                'cnt_ptc'    => $cntPtc,
                'est'        => 'confirmada',
                'pre_tot'    => $preTot,
                'est_pag'    => 'pendiente',
            ]);

            // Crear Pasajeros y Boletos
            foreach ($validated['participantes'] as $index => $ptc) {
                $participante = ParticipanteReserva::create([
                    'rsv_id'     => $reserva->id,
                    'nom_cpl'    => $ptc['nom_cpl'],
                    'tip_doc'    => $ptc['tip_doc'] ?? 'CI',
                    'num_doc'    => $ptc['num_doc'] ?? null,
                    'ci_cmp'     => $ptc['ci_cmp'] ?? '',
                    'ci_dep_id'  => $ptc['ci_dep_id'] ?? null,
                    'eda'        => $ptc['eda'] ?? null,
                    'pai_id'     => $ptc['pai_id'] ?? null,
                    'es_ctc_pri' => ($index === 0),
                ]);

                DB::table('boletos')->insert([
                    'cod'        => 'TKT-' . strtoupper(Str::random(8)),
                    'ptc_rsv_id' => $participante->id,
                    'est'        => 'emitido',
                    'emt_en'     => now(),
                ]);
            }

            DB::table('comprobantes_pago')->insert([
                'cod'     => 'CMP-' . strtoupper(Str::random(6)),
                'rsv_id'  => $reserva->id,
                'fec_emi' => now()->toDateString(),
                'mto_tot' => $preTot,
                'est'     => 'emitido',
            ]);

            return response()->json([
                'success' => true,
                'message' => "¡Reserva {$reserva->cod} registrada exitosamente!",
                'reserva' => $reserva->load(['salidaTour.tour', 'participantes']),
            ], 201);
        });
    }

 
    public function show($id)
    {
        $reserva = Reserva::with([
            'salidaTour.tour',
            'salidaTour.vehiculo',
            'participantes.departamento',
            'participantes.pais',
            'participantes.fichasMedicas',
            'participantes.boleto',
            'comprobantePago',
        ])->find($id);

        if (!$reserva) {
            return response()->json(['success' => false, 'message' => 'Reserva no encontrada.'], 404);
        }

        return response()->json($reserva);
    }


    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $validated = $request->validate([
            'nom_ctc' => 'sometimes|required|string|max:150',
            'cor_ctc' => 'sometimes|required|email|max:150',
            'tel_ctc' => 'sometimes|required|string|max:20',
            'est'     => 'sometimes|required|in:confirmada,pendiente,cancelada',
            'est_pag' => 'sometimes|required|in:pendiente,completado,parcial',
        ]);

        $reserva->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reserva actualizada correctamente.',
            'reserva' => $reserva,
        ]);
    }

    public function destroy($id)
    {
        $reserva = Reserva::with('participantes')->findOrFail($id);

        if ($reserva->est === 'cancelada') {
            return response()->json(['success' => true, 'message' => 'La reserva ya estaba cancelada.']);
        }

        DB::transaction(function () use ($reserva) {
            $reserva->update(['est' => 'cancelada']);

            $participanteIds = $reserva->participantes->pluck('id');
            DB::table('boletos')->whereIn('ptc_rsv_id', $participanteIds)->update(['est' => 'anulado']);
        });

        return response()->json([
            'success' => true,
            'message' => "La reserva #{$reserva->cod} ha sido cancelada.",
        ]);
    }
}