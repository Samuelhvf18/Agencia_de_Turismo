<?php

namespace App\Http\Controllers\Reservas;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Resena;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Reseñas verificadas (RF-32): solo quien tiene acceso a una reserva
 * completada puede calificar el tour, una vez por reserva.
 */
class ResenaController extends Controller
{
    public function store(Request $request, Reserva $reserva): RedirectResponse
    {
        AccesoReserva::exigir($request, $reserva);

        if ($reserva->est !== Reserva::EST_COMPLETADA) {
            return back()->withErrors(['cal' => 'Solo puedes reseñar un tour que ya realizaste.']);
        }
        if (Resena::where('rsv_id', $reserva->id)->exists()) {
            return back()->withErrors(['cal' => 'Ya dejaste una reseña para esta reserva.']);
        }

        $datos = $request->validate([
            'cal' => ['required', 'integer', 'between:1,5'],
            'com' => ['nullable', 'string', 'max:1000'],
        ], ['cal.*' => 'Elige una calificación de 1 a 5 estrellas.']);

        Resena::create([
            'tur_id' => $reserva->salida->tur_id,
            'rsv_id' => $reserva->id,
            'cal' => $datos['cal'],
            'com' => isset($datos['com']) ? strip_tags($datos['com']) : null,
            'mdt' => [
                'verificada' => true,
                'autor' => \Illuminate\Support\Str::before($reserva->nom_ctc, ' '),
                'fecha_tour' => $reserva->salida->fec_sal->toDateString(),
                'participantes' => $reserva->cnt_ptc,
            ],
        ]);

        return redirect()->route('reservas.estado', $reserva)->with('estado', '¡Gracias por tu reseña!');
    }
}
