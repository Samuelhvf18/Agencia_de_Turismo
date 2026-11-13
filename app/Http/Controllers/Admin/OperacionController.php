<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\Vehiculo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OperacionController extends Controller
{
public function index()
{
    $salidas = SalidaTour::with(['tour', 'vehiculo', 'guia.user'])->orderBy('id', 'desc')->get();
    $vehiculos = Vehiculo::where('est', '!=', 'inact')->get();
    
    $guias = \App\Models\PerfilGuia::with('user')->get(); 

    return view('admin.panels.operaciones', compact('salidas', 'vehiculos', 'guias'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nombre_tour' => 'required|string|max:100',
            'veh_id'      => 'required|exists:vehiculos,id',
            'prf_gui_id'  => 'required|exists:users,id',
            'fec_sal'     => 'required|date',
            'est'         => 'required|string',
        ]);

        $nuevaFecha = Carbon::parse($request->fec_sal);

        $conflictoVehiculo = SalidaTour::where('veh_id', $request->veh_id)
            ->whereIn('est', ['programada', 'en_ruta'])
            ->get()
            ->first(function ($salida) use ($nuevaFecha) {
                $fechaExistente = Carbon::parse($salida->fec_sal);
                return $fechaExistente->diffInDays($nuevaFecha) < 3;
            });

        if ($conflictoVehiculo) {
            $fechaConflicto = Carbon::parse($conflictoVehiculo->fec_sal)->format('d/m/Y');
            return redirect()->back()->withErrors([
                'veh_id' => "El vehículo ya tiene una salida el {$fechaConflicto}. Debe existir un margen de al menos 3 días."
            ])->withInput();
        }

        $conflictoGuia = SalidaTour::where('prf_gui_id', $request->prf_gui_id)
            ->whereIn('est', ['programada', 'en_ruta'])
            ->get()
            ->first(function ($salida) use ($nuevaFecha) {
                $fechaExistente = Carbon::parse($salida->fec_sal);
                return $fechaExistente->diffInDays($nuevaFecha) < 3;
            });

        if ($conflictoGuia) {
            $fechaConflicto = Carbon::parse($conflictoGuia->fec_sal)->format('d/m/Y');
            return redirect()->back()->withErrors([
                'prf_gui_id' => "El guía ya tiene una salida asignada el {$fechaConflicto}. Necesita al menos 2 días de descanso."
            ])->withInput();
        }

        $nombreTour = trim($request->nombre_tour);
        $defaultCatId = DB::table('categorias_tour')->value('id');

        $tour = Tour::firstOrCreate(
            ['nom' => $nombreTour],
            [
                'cat_id'   => $defaultCatId,
                'cod'      => 'TOUR-' . strtoupper(Str::random(5)),
                'des'      => 'Ruta registrada desde operaciones',
                'dur_hrs'  => 8.0,
                'niv_dif'  => 'moderado',
                'pre_bas'  => 250.00,
                'cap_max'  => 15,
                'act'      => true,
            ]
        );

        $vehiculo = Vehiculo::find($request->veh_id);
        $capacidad = $vehiculo ? $vehiculo->cap_asi : 15;

        SalidaTour::create([
            'tur_id'     => $tour->id,
            'veh_id'     => $request->veh_id,
            'prf_gui_id' => $request->prf_gui_id,
            'fec_sal'    => $request->fec_sal,
            'hra_sal'    => '08:00:00',
            'cap_max'    => $capacidad,
            'est'        => $request->est,
        ]);

        return redirect()->back()->with('success', 'Salida programada y guía asignado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'veh_id'     => 'required|exists:vehiculos,id',
            'prf_gui_id' => 'required|exists:users,id',
            'fec_sal'    => 'required|date',
            'est'        => 'required|string',
        ]);

        $nuevaFecha = Carbon::parse($request->fec_sal);

        $conflictoVehiculo = SalidaTour::where('veh_id', $request->veh_id)
            ->where('id', '!=', $id)
            ->whereIn('est', ['programada', 'en_ruta'])
            ->get()
            ->first(function ($salida) use ($nuevaFecha) {
                $fechaExistente = Carbon::parse($salida->fec_sal);
                return $fechaExistente->diffInDays($nuevaFecha) < 3;
            });

        if ($conflictoVehiculo) {
            $fechaConflicto = Carbon::parse($conflictoVehiculo->fec_sal)->format('d/m/Y');
            return redirect()->back()->withErrors([
                'veh_id' => "El vehículo ya tiene una salida el {$fechaConflicto}. Respete el intervalo de 3 días."
            ])->withInput();
        }

        $conflictoGuia = SalidaTour::where('prf_gui_id', $request->prf_gui_id)
            ->where('id', '!=', $id)
            ->whereIn('est', ['programada', 'en_ruta'])
            ->get()
            ->first(function ($salida) use ($nuevaFecha) {
                $fechaExistente = Carbon::parse($salida->fec_sal);
                return $fechaExistente->diffInDays($nuevaFecha) < 3;
            });

        if ($conflictoGuia) {
            $fechaConflicto = Carbon::parse($conflictoGuia->fec_sal)->format('d/m/Y');
            return redirect()->back()->withErrors([
                'prf_gui_id' => "El guía ya tiene un tour asignado el {$fechaConflicto}. Necesita al menos 2 días de descanso."
            ])->withInput();
        }

        $salida = SalidaTour::findOrFail($id);
        $salida->update([
            'veh_id'     => $request->veh_id,
            'prf_gui_id' => $request->prf_gui_id,
            'fec_sal'    => $request->fec_sal,
            'est'        => $request->est,
        ]);

        return redirect()->back()->with('success', 'Salida actualizada correctamente.');
    }
}