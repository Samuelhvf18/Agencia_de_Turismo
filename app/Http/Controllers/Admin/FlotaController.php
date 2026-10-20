<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalidaTour;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** Flota de transporte (M03, RF-19): vehículos con estados activo, mantenimiento e inactivo. */
class FlotaController extends Controller
{
    public function index(): View
    {
        $hoy = today()->toDateString();

        return view('admin.panels.transporte', [
            'vehiculos' => Vehiculo::withCount(['salidas as futuras' => fn ($q) => $q->where('est', SalidaTour::EST_PROGRAMADA)->where('fec_sal', '>=', $hoy)])
                ->withSum('viajes as km', 'dst_km')
                ->withSum('viajes as combustible', 'cos_cmb')
                ->orderBy('pla')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Vehiculo::create($this->validar($request) + ['est' => Vehiculo::EST_ACTIVO]);

        return back()->with('estado', 'Vehículo registrado.');
    }

    public function update(Request $request, Vehiculo $vehiculo): RedirectResponse
    {
        $datos = $this->validar($request, $vehiculo);

        if ($datos['est'] !== Vehiculo::EST_ACTIVO) {
            $futuras = $vehiculo->salidas()->where('est', SalidaTour::EST_PROGRAMADA)->whereDate('fec_sal', '>=', today())->count();
            if ($futuras > 0) {
                return back()->withErrors(['flota' => "El vehículo {$vehiculo->pla} tiene {$futuras} salida(s) futura(s). Reasígnalas antes de pasarlo a {$datos['est']}."]);
            }
        }

        $vehiculo->update($datos);

        return back()->with('estado', "Vehículo {$vehiculo->pla} actualizado.");
    }

    private function validar(Request $request, ?Vehiculo $vehiculo = null): array
    {
        $request->merge(['pla' => strtoupper(trim((string) $request->input('pla')))]);

        return $request->validate([
            'pla' => ['required', 'string', 'max:15', 'regex:/^[0-9]{3,4}-?[A-Z]{3}$/', Rule::unique('vehiculos', 'pla')->ignore($vehiculo?->id)],
            'mar' => ['nullable', 'string', 'max:50'],
            'mdl' => ['nullable', 'string', 'max:50'],
            'tip' => ['required', 'in:minibus,van,otro'],
            'cap_asi' => ['required', 'integer', 'min:1', 'max:60'],
            'tip_cmb' => ['nullable', 'in:gasolina,diesel,gnv,electrico'],
            'est' => [$vehiculo ? 'required' : 'nullable', 'in:activo,mantenimiento,inactivo'],
        ], ['pla.regex' => 'La placa debe tener el formato boliviano 1234-ABC.']);
    }
}
