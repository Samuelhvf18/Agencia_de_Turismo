<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\PerfilChofer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehiculoController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with(['chofer.user'])->get();
        
        $choferes = PerfilChofer::with('user')->get();

        return view('admin.panels.transporte', compact('vehiculos', 'choferes'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'pla' => 'required|string|max:15|unique:vehiculos,pla',
            'mar' => 'nullable|string|max:50',
            'mdl' => 'nullable|string|max:50',
            'tip' => 'required|string|max:20',
            'tip_cmb' => 'nullable|string|max:20',
            'prf_cho_id' => 'nullable|exists:perfiles_chofer,id',
        ], [
            'pla.unique' => 'La placa ingresada ya se encuentra registrada en la flota.',
        ]);

        Vehiculo::create([
            'pla' => strtoupper(trim($request->pla)),
            'mar' => $request->mar,
            'mdl' => $request->mdl,
            'tip' => $request->tip,
            'cap_asi' => $request->cap_asi,
            'tip_cmb' => $request->tip_cmb,
            'prf_cho_id' => $request->prf_cho_id ?: null,
            'est' => 'act',
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
            'prf_cho_id' => 'nullable|exists:perfiles_chofer,id',
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
            'prf_cho_id' => $request->prf_cho_id ?: null,
            'est'     => $request->est,
        ]);

        return redirect()->back()->with('success', 'Vehículo actualizado correctamente.');
    }
    
}