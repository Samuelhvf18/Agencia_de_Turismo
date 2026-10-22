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

        $tour = Tour::firstOrCreate(
            ['nom' => $nombreTour],
            [
                'cod'     => 'TOUR-' . strtoupper(Str::random(5)),
                'des'     => 'Ruta registrada desde operaciones',
                'dur_dias'=> 1,
                'pre_bas' => 0
            ]
        );

        DB::table('salidas_tour')->insert([
            'tur_id'  => $tour->id,
            'veh_id'  => $request->veh_id,
            'fec_sal' => $request->fec_sal,
            'est'     => $request->est,
        ]);

        if ($request->est === 'en_ruta') {
            Vehiculo::where('id', $request->veh_id)->update(['est' => 'en_ruta']);
        }

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

        DB::table('salidas_tour')
            ->where('id', $id)
            ->update([
                'veh_id'  => $request->veh_id,
                'fec_sal' => $request->fec_sal,
                'est'     => $request->est,
            ]);

        if ($request->est === 'en_ruta') {
            Vehiculo::where('id', $request->veh_id)->update(['est' => 'en_ruta']);
        } elseif ($request->est === 'finalizada' || $request->est === 'programada') {
            Vehiculo::where('id', $request->veh_id)->update(['est' => 'act']);
        }

        return redirect()->back()->with('success', 'Salida actualizada correctamente.');
    }
}