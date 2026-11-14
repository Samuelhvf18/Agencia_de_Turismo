<?php
namespace App\Http\Controllers\Turista;
use App\Http\Controllers\Controller;
use App\Models\CategoriaTour;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TuristaController extends Controller
{
    public function dashboard()
    {
        return redirect()->route('turista.tours.index');
    }
    public function tours(Request $request)
    {
        $consulta = Tour::where('act', true)
            ->with(['categoria', 'fotos'])
            ->with(['salidas' => function ($query) {
                $query->where('est', 'programada')
                    ->whereDate('fec_sal', '>=', today())
                    ->orderBy('fec_sal')
                    ->withSum([
                        'reservas as participantes_reservados' => function ($reservas) {
                            $reservas->where('est', '<>', 'cancelada');
                        }
                    ], 'cnt_ptc');
            }]);
        if ($request->filled('buscar')) {
            $buscar = trim($request->buscar);
            $consulta->where(function ($query) use ($buscar) {
                $query->where('nom', 'ILIKE', "%{$buscar}%")
                    ->orWhere('des', 'ILIKE', "%{$buscar}%");
            });
        }
        if ($request->filled('categoria')) {
            $consulta->where('cat_id', $request->categoria);
        }
        $tours = $consulta
            ->latest('id')
            ->paginate(9)
            ->withQueryString();
        $categorias = CategoriaTour::orderBy('nom')->get();
        return view('turista.tours.index', compact(
            'tours',
            'categorias'
        ));
    }
    public function showTour($tour)
    {
        $tour = Tour::where('act', true)
            ->with(['categoria', 'fotos'])
            ->with(['salidas' => function ($query) {
                $query->where('est', 'programada')
                    ->whereDate('fec_sal', '>=', today())
                    ->orderBy('fec_sal')
                    ->withSum([
                        'reservas as participantes_reservados' => function ($reservas) {
                            $reservas->where('est', '<>', 'cancelada');
                        }
                    ], 'cnt_ptc');
            }])
            ->findOrFail($tour);
        return view('turista.tours.show', compact('tour'));
    }
    public function reservas()
    {
        $reservas = Reserva::with([
                'salidaTour.tour',
                'participantes'
            ])
            ->where('usr_id', Auth::id())
            ->latest('cre_en')
            ->paginate(10);
        return view('turista.reservas.index', compact('reservas'));
    }
    public function perfil()
    {
        $usuario = Auth::user();
        return view('turista.perfil', compact('usuario'));
    }
    public function actualizarPerfil(Request $request)
    {
        $datos = $request->validate([
            'nom' => ['required', 'string', 'max:150'],
            'tel' => ['nullable', 'string', 'max:20'],
            'tip_doc' => ['nullable', 'string', 'max:30'],
            'num_doc' => ['nullable', 'string', 'max:30'],
        ]);

        $usuario = Auth::user();
        $usuario->update($datos);

        return redirect()
            ->route('turista.perfil')
            ->with('success', 'Tus datos personales se actualizaron correctamente.');
    }
    public function solicitarReserva(Request $request)
    {
        $datos = $request->validate([
            'sal_tur_id' => [
                'required',
                'integer',
                'exists:salidas_tour,id',
            ],
            'nom_ctc' => [
                'required',
                'string',
                'max:150',
            ],
            'cor_ctc' => [
                'required',
                'email',
                'max:150',
            ],
            'tel_ctc' => [
                'required',
                'string',
                'max:20',
            ],
            'participantes' => [
                'required',
                'array',
                'min:1',
                'max:25',
            ],
            'participantes.*.nom_cpl' => [
                'required',
                'string',
                'max:150',
            ],
        ]);
        $cantidadParticipantes = count($datos['participantes']);
        DB::transaction(function () use ($datos, $cantidadParticipantes) {
            $salida = SalidaTour::whereKey($datos['sal_tur_id'])
                ->lockForUpdate()
                ->firstOrFail();
            $salida->load('tour');
            if (
                $salida->est !== 'programada' ||
                $salida->fec_sal < today()->toDateString() ||
                !$salida->tour ||
                !$salida->tour->act
            ) {
                throw ValidationException::withMessages([
                    'sal_tur_id' => 'Esta salida ya no está disponible.',
                ]);
            }
            $participantesReservados = Reserva::where(
                    'sal_tur_id',
                    $salida->id
                )
                ->where('est', '<>', 'cancelada')
                ->sum('cnt_ptc');
            $cuposDisponibles = max(
                (int) $salida->cap_max - (int) $participantesReservados,
                0
            );
            if ($cantidadParticipantes > $cuposDisponibles) {
                throw ValidationException::withMessages([
                    'participantes' => "Solo quedan {$cuposDisponibles} cupos disponibles para esta salida.",
                ]);
            }
            $precioUnitario = (float) $salida->tour->pre_bas;
            $precioTotal = round(
                $precioUnitario * $cantidadParticipantes,
                2
            );
            $reserva = Reserva::create([
                'cod' => 'RES-' . Str::upper(Str::random(6)),
                'sal_tur_id' => $salida->id,
                'usr_id' => Auth::id(),
                'nom_ctc' => $datos['nom_ctc'],
                'cor_ctc' => $datos['cor_ctc'],
                'tel_ctc' => $datos['tel_ctc'],
                'cnt_ptc' => $cantidadParticipantes,
                'est' => 'pendiente',
                'pre_tot' => $precioTotal,
                'est_pag' => 'pendiente',
            ]);
            foreach ($datos['participantes'] as $indice => $participante) {
                $reserva->participantes()->create([
                    'nom_cpl' => trim($participante['nom_cpl']),
                    'es_ctc_pri' => $indice === 0,
                ]);
            }
        });
        return redirect()
            ->route('turista.reservas.index')
            ->with(
                'success',
                'Tu solicitud de reserva fue enviada correctamente. Está pendiente de confirmación.'
            );
    }
}
