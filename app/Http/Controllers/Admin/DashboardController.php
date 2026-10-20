<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\SalidaTour;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $vehiculosDisponibles = Vehiculo::where('est', 'act')->count();
        $vehiculosMantenimiento = Vehiculo::where('est', 'inact')->count();
        $totalVehiculos = Vehiculo::count();

        $salidasProgramadas = SalidaTour::count();

        $equiposDisponibles = DB::table('items_equipo')->sum('cnt_dis') ?? 0;
        $equiposTotal = DB::table('items_equipo')->sum('cnt_tot') ?? 0;

        $salidas = SalidaTour::with(['tour', 'vehiculo'])->take(5)->get();

        return view('admin.dashboard', compact(
            'vehiculosDisponibles',
            'vehiculosMantenimiento',
            'totalVehiculos',
            'salidasProgramadas',
            'equiposDisponibles',
            'equiposTotal',
            'salidas'
        ));
    }
}