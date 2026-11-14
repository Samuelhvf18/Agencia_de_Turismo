<?php

namespace App\Http\Requests\Reservas;

use Illuminate\Foundation\Http\FormRequest;

class ElegirSalidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sal_tur_id' => ['required', 'integer', 'exists:salidas_tour,id'],
            'cnt_ptc' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'sal_tur_id.required' => 'Elige un horario de salida.',
            'sal_tur_id.exists' => __('La salida elegida no existe.'),
            'cnt_ptc.min' => 'Debe viajar al menos una persona.',
        ];
    }
}
