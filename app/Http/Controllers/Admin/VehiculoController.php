<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\SalidaTour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::all();
        return view('admin.panels.transporte', compact('vehiculos'));
    }

    public function store(Request $request)
    {
        $capacidadPorTipo = [
            'Automóvil' => 3,
            'Vagoneta 4x4' => 5,
            'Minibús' => 13,
            'Bus' => 22,
        ];

        $tipo = $request->input('tip');
        $capacidad = $capacidadPorTipo[$tipo] ?? 10;

        $request->validate([
            'pla' => 'required|string|max:15|unique:vehiculos,pla',
            'mar' => 'nullable|string|max:50',
            'mdl' => 'nullable|string|max:50',
            'tip' => 'required|string|max:20',
            'tip_cmb' => 'nullable|string|max:20',
        ], [
            'pla.unique' => 'La placa ingresada ya se encuentra registrada en la flota.',
        ]);

        Vehiculo::create([
            'pla'     => strtoupper(trim($request->pla)),
            'mar'     => $request->mar,
            'mdl'     => $request->mdl,
            'tip'     => $tipo,
            'cap_asi' => $capacidad,
            'tip_cmb' => $request->tip_cmb,
            'est'     => 'act',
        ]);

        return redirect()->back()->with('success', 'Vehículo registrado correctamente.');
    }

public function update(Request $request, $id)
{
    $vehiculo = Vehiculo::findOrFail($id);

    $request->validate([
        'pla'     => 'required|string|max:15|unique:vehiculos,pla,' . $id,
        'mar'     => 'nullable|string|max:50',
        'mdl'     => 'nullable|string|max:50',
        'tip'     => 'required|string|max:20',
        'cap_asi' => 'required|integer|min:1',
        'tip_cmb' => 'nullable|string|max:20',
        'est'     => 'required|string',
    ]);

    if ($request->est === 'inact') {
        DB::table('salidas_tour')
            ->where('veh_id', $vehiculo->id)
            ->where('est', 'programada')
            ->update(['veh_id' => null]);
    }

    $vehiculo->update([
        'pla'     => strtoupper(trim($request->pla)),
        'mar'     => $request->mar,
        'mdl'     => $request->mdl,
        'tip'     => $request->tip,
        'cap_asi' => $request->cap_asi,
        'tip_cmb' => $request->tip_cmb,
        'est'     => $request->est,
    ]);

    return redirect()->back()->with('success', 'Vehículo actualizado correctamente.');
}
}