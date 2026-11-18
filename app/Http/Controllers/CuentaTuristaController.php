<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CuentaTuristaController extends Controller
{
    public function show(Request $request)
    {
        return view('turista.cuenta', ['usuario' => $request->user()]);
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'prefijo' => ['required', 'string', Rule::in(array_keys(config('turismo.prefijos')))],
            'telefono' => ['required', 'string', 'regex:/^[0-9]{4,14}$/'],
        ], ['telefono.regex' => 'Escribe solo números, sin espacios ni signos.']);
        $tel = $datos['prefijo'].$datos['telefono'];
        validator(['tel' => $tel], ['tel' => ['regex:/^\+[1-9][0-9]{6,14}$/']],
            ['tel.regex' => 'El teléfono debe tener entre 7 y 15 dígitos, incluyendo el prefijo.'])->validate();
        $request->user()->update(['tel' => $tel]);

        return redirect()->route('turista.cuenta')->with('estado', 'Tu teléfono fue actualizado.');
    }
}
