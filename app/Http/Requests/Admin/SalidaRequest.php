<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Formato y existencia de datos. */
class SalidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reglas = [
            'cap_max' => ['required', 'integer', 'min:1', 'max:60'],
            'prf_gui_id' => ['nullable', 'integer', 'exists:perfiles_guia,id'],
            'prf_cho_id' => ['nullable', 'integer', 'exists:perfiles_chofer,id'],
            'veh_id' => ['nullable', 'integer', 'exists:vehiculos,id'],
        ];

        if ($this->isMethod('post')) {
            $reglas += [
                'tur_id' => ['required', 'integer', 'exists:tours,id'],
                'fec_sal' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before:+1 year'],
                'hra_sal' => ['required', 'date_format:H:i'],
            ];
        }

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'fec_sal.after_or_equal' => 'La salida no puede programarse en una fecha pasada.',
            'fec_sal.before' => 'Las salidas se programan con hasta un año de anticipación.',
        ];
    }

    /** Normaliza datos de asignación. */
    public function datos(): array
    {
        $datos = $this->validated();
        if (isset($datos['hra_sal'])) {
            $datos['hra_sal'] .= ':00';
        }
        foreach (['prf_gui_id', 'prf_cho_id', 'veh_id'] as $campo) {
            $datos[$campo] = $datos[$campo] ?? null;
        }

        return $datos;
    }
}
