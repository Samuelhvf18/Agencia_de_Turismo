<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerfilChofer;
use App\Models\PerfilGuia;
use App\Models\Reserva;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\Vehiculo;
use App\Services\Operaciones\AsignacionService;
use App\Services\Operaciones\SalidaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Programación de salidas y asignación manual de guía, chofer y vehículo (M04). */
class SalidasController extends Controller
{
    public function __construct(private SalidaService $salidas) {}

    public function index(Request $request, AsignacionService $asignacion): View
    {
        $filtros = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
            'tur_id' => ['nullable', 'integer'],
            'est' => ['nullable', 'in:programada,en_curso,completada,cancelada'],
        ]);
        $desde = $filtros['desde'] ?? today()->toDateString();
        $hasta = $filtros['hasta'] ?? today()->addDays(14)->toDateString();

        $salidas = SalidaTour::with('tour', 'guia.usuario', 'chofer.usuario', 'vehiculo')
            ->withSum(['reservas as ocupados' => fn ($q) => $q->where('est', '<>', Reserva::EST_CANCELADA)], 'cnt_ptc')
            ->whereBetween('fec_sal', [$desde, $hasta])
            ->when($filtros['tur_id'] ?? null, fn ($q, $t) => $q->where('tur_id', $t))
            ->when($filtros['est'] ?? null, fn ($q, $e) => $q->where('est', $e))
            ->orderBy('fec_sal')->orderBy('hra_sal')
            ->get();

        return view('admin.panels.operaciones', [
            'salidas' => $salidas->groupBy(fn ($s) => $s->fec_sal->toDateString()),
            'filtros' => $filtros + ['desde' => $desde, 'hasta' => $hasta],
        ] + $this->opciones());
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request, nueva: true);
        $salida = $this->salidas->programar($datos);

        return back()->with('estado', "Salida de {$salida->tour->nom} programada para el {$salida->fec_sal->format('d/m/Y')}.");
    }

    public function edit(SalidaTour $salida): View
    {
        $salida->load('tour', 'guia.usuario', 'chofer.usuario', 'vehiculo', 'reservas.participantes.boleto', 'reservas.participantes.alquileres.item.tipo');

        return view('admin.salidas.edit', ['salida' => $salida] + $this->opciones());
    }

    public function update(Request $request, SalidaTour $salida): RedirectResponse
    {
        $this->salidas->asignar($salida, $this->validar($request, nueva: false));

        return back()->with('estado', 'Asignación actualizada.');
    }

    public function reprogramar(Request $request, SalidaTour $salida): RedirectResponse
    {
        $datos = $request->validate([
            'fec_sal' => ['required', 'date', 'after_or_equal:today'],
            'hra_sal' => ['required', 'date_format:H:i'],
            'motivo' => ['required', 'string', 'max:200'],
        ]);
        $this->salidas->reprogramar($salida, $datos['fec_sal'], $datos['hra_sal'].':00', $datos['motivo']);

        return back()->with('estado', 'Salida reprogramada; se avisó a los clientes.');
    }

    public function cancelar(Request $request, SalidaTour $salida): RedirectResponse
    {
        $datos = $request->validate(['motivo' => ['required', 'string', 'max:200']]);
        $this->salidas->cancelar($salida, $datos['motivo']);

        return redirect()->route('admin.operaciones')->with('estado', 'Salida cancelada; se cancelaron sus reservas y se avisó a los clientes.');
    }

    private function validar(Request $request, bool $nueva): array
    {
        $reglas = [
            'cap_max' => ['required', 'integer', 'min:1', 'max:60'],
            'prf_gui_id' => ['nullable', 'integer', 'exists:perfiles_guia,id'],
            'prf_cho_id' => ['nullable', 'integer', 'exists:perfiles_chofer,id'],
            'veh_id' => ['nullable', 'integer', 'exists:vehiculos,id'],
        ];
        if ($nueva) {
            $reglas += [
                'tur_id' => ['required', 'integer', 'exists:tours,id'],
                'fec_sal' => ['required', 'date', 'after_or_equal:today'],
                'hra_sal' => ['required', 'date_format:H:i'],
            ];
        }
        $datos = $request->validate($reglas);
        if ($nueva) {
            $datos['hra_sal'] .= ':00';
        }
        foreach (['prf_gui_id', 'prf_cho_id', 'veh_id'] as $campo) {
            $datos[$campo] = $datos[$campo] ?? null;
        }

        return $datos;
    }

    private function opciones(): array
    {
        return [
            'tours' => Tour::where('act', true)->whereNull('eli_en')->orderBy('nom')->get(['id', 'nom', 'cap_max', 'dur_hrs']),
            'guias' => PerfilGuia::with('usuario', 'idiomas')->where('act', true)->get()->sortBy('usuario.nom'),
            'choferes' => PerfilChofer::with('usuario')->where('act', true)->get()->sortBy('usuario.nom'),
            'vehiculos' => Vehiculo::where('est', Vehiculo::EST_ACTIVO)->orderBy('pla')->get(),
        ];
    }
}
