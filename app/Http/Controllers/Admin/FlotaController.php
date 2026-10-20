<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VehiculoRequest;
use App\Models\SalidaTour;
use App\Models\Vehiculo;
use Illuminate\Http\RedirectResponse;
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

    public function store(VehiculoRequest $request): RedirectResponse
    {
        Vehiculo::create($request->safe()->except('est') + ['est' => Vehiculo::EST_ACTIVO]);

        return back()->with('estado', 'Vehículo registrado.');
    }

    public function update(VehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        $datos = $request->validated();

        if ($datos['est'] !== Vehiculo::EST_ACTIVO) {
            $futuras = $vehiculo->salidas()->where('est', SalidaTour::EST_PROGRAMADA)->whereDate('fec_sal', '>=', today())->count();
            if ($futuras > 0) {
                return back()->withErrors(['flota' => "El vehículo {$vehiculo->pla} tiene {$futuras} salida(s) futura(s). Reasígnalas antes de pasarlo a {$datos['est']}."]);
            }
        }

        $vehiculo->update($datos);

        return back()->with('estado', "Vehículo {$vehiculo->pla} actualizado.");
    }
}
