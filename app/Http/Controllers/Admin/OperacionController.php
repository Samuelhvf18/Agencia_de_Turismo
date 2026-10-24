<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalidaTour;
use App\Models\Tour;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OperacionController extends Controller
{
    public function index()
    {
        $salidas = SalidaTour::with(['tour', 'vehiculo'])->orderBy('id', 'desc')->get();
        $vehiculos = Vehiculo::where('est', '!=', 'inact')->get();

        return view('admin.panels.operaciones', compact('salidas', 'vehiculos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_tour' => 'required|string|max:100',
            'veh_id'      => 'required|exists:vehiculos,id',
            'fec_sal'     => 'required|date',
            'est'         => 'required|string',
        ]);

        $nuevaFecha = Carbon::parse($request->fec_sal);

        $conflicto = SalidaTour::where('veh_id', $request->veh_id)
            ->whereIn('est', ['programada', 'en_ruta'])
            ->get()
            ->first(function ($salida) use ($nuevaFecha) {
                $fechaExistente = Carbon::parse($salida->fec_sal);
                return $fechaExistente->diffInDays($nuevaFecha) < 3;
            });

        if ($conflicto) {
            $fechaConflicto = Carbon::parse($conflicto->fec_sal)->format('d/m/Y');
            return redirect()->back()->withErrors([
                'veh_id' => "El vehículo seleccionado ya tiene una salida asignada el {$fechaConflicto}. Debe existir un margen de al menos 3 días entre salidas."
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
            'tur_id'  => $tour->id,
            'veh_id'  => $request->veh_id,
            'fec_sal' => $request->fec_sal,
            'hra_sal' => '08:00:00',
            'cap_max' => $capacidad,
            'est'     => $request->est,
        ]);

        return redirect()->back()->with('success', 'Salida programada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'veh_id'  => 'required|exists:vehiculos,id',
            'fec_sal' => 'required|date',
            'est'     => 'required|string',
        ]);

        $nuevaFecha = Carbon::parse($request->fec_sal);

        $conflicto = SalidaTour::where('veh_id', $request->veh_id)
            ->where('id', '!=', $id)
            ->whereIn('est', ['programada', 'en_ruta'])
            ->get()
            ->first(function ($salida) use ($nuevaFecha) {
                $fechaExistente = Carbon::parse($salida->fec_sal);
                return $fechaExistente->diffInDays($nuevaFecha) < 3;
            });

        if ($conflicto) {
            $fechaConflicto = Carbon::parse($conflicto->fec_sal)->format('d/m/Y');
            return redirect()->back()->withErrors([
                'veh_id' => "El vehículo seleccionado ya tiene una salida asignada el {$fechaConflicto}. Debe haber un intervalo de al menos 3 días."
            ])->withInput();
        }

        $salida = SalidaTour::findOrFail($id);
        $salida->update([
            'veh_id'  => $request->veh_id,
            'fec_sal' => $request->fec_sal,
            'est'     => $request->est,
        ]);

        return redirect()->back()->with('success', 'Salida actualizada correctamente.');
    }

}