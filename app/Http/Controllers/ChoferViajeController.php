<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SalidaTour;
use App\Http\Controllers\Controller;

class ChoferViajeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $perfilChofer = $user->perfilChofer;

        $salidas = collect();

        if ($perfilChofer) {
            $salidas = SalidaTour::with(['vehiculo', 'tour', 'guia.user', 'reservas'])
                ->where(function ($query) use ($perfilChofer) {
                    $query->where('prf_cho_id', $perfilChofer->id)
                          ->orWhereHas('vehiculo', function ($q) use ($perfilChofer) {
                              $q->where('prf_cho_id', $perfilChofer->id);
                          });
                })
                ->get();
        }

        // Enviamos la colección con los 3 nombres posibles para que la vista la agarre de una
        return view('chofer.viajes', [
            'salidas' => $salidas,
            'viajes' => $salidas,
            'expediciones' => $salidas,
        ]);
    }
}