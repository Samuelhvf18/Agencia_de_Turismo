<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemEquipo;
use App\Models\TipoEquipo;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        $items = ItemEquipo::with('tipoEquipo')->get();
        return view('admin.panels.inventario', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_equipo' => 'required|string|max:80',
            'tal'        => 'required|string|max:20',
            'cnt_tot'    => 'required|integer|min:0',
            'cnt_dis'    => 'required|integer|min:0|lte:cnt_tot',
            'con'        => 'required|string|max:20',
        ], [
            'cnt_dis.lte' => 'El stock disponible no puede ser mayor al stock total.',
        ]);

        $tipoEquipo = TipoEquipo::firstOrCreate(
            ['nom' => trim($request->nom_equipo)],
            ['des' => 'Equipo registrado desde inventario', 'pre_alq_bas' => 0]
        );

        ItemEquipo::create([
            'tip_eqp_id' => $tipoEquipo->id,
            'tal'        => $request->tal,
            'cnt_tot'    => $request->cnt_tot,
            'cnt_dis'    => $request->cnt_dis,
            'con'        => $request->con,
        ]);

        return redirect()->back()->with('success', 'Equipo registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $item = ItemEquipo::findOrFail($id);

        if ($request->has('con') && !$request->has('tal')) {
            $item->update(['con' => $request->con]);
            return redirect()->back();
        }

        $request->validate([
            'nom_equipo' => 'required|string|max:80',
            'tal'        => 'required|string|max:20',
            'cnt_tot'    => 'required|integer|min:0',
            'cnt_dis'    => 'required|integer|min:0|lte:cnt_tot',
            'con'        => 'required|string',
        ], [
            'cnt_dis.lte' => 'El stock disponible no puede ser mayor al stock total.',
        ]);

        $tipoEquipo = TipoEquipo::firstOrCreate(
            ['nom' => trim($request->nom_equipo)],
            ['des' => 'Equipo actualizado desde inventario', 'pre_alq_bas' => 0]
        );

        $item->update([
            'tip_eqp_id' => $tipoEquipo->id,
            'tal'        => $request->tal,
            'cnt_tot'    => $request->cnt_tot,
            'cnt_dis'    => $request->cnt_dis,
            'con'        => $request->con,
        ]);

        return redirect()->back()->with('success', 'Equipo actualizado correctamente.');
    }
}