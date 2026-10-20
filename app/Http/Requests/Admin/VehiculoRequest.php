<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** Vehículo de la flota (RF-19, RF-39): placa boliviana única, tipo, asientos y estado. */
class VehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['pla' => strtoupper(trim((string) $this->input('pla')))]);
    }

    public function rules(): array
    {
        $vehiculo = $this->route('vehiculo');

        return [
            'pla' => ['required', 'string', 'max:15', 'regex:/^[0-9]{3,4}-?[A-Z]{3}$/', Rule::unique('vehiculos', 'pla')->ignore($vehiculo?->id)],
            'mar' => ['nullable', 'string', 'max:50'],
            'mdl' => ['nullable', 'string', 'max:50'],
            'tip' => ['required', 'in:minibus,van,otro'],
            'cap_asi' => ['required', 'integer', 'min:1', 'max:60'],
            'tip_cmb' => ['nullable', 'in:gasolina,diesel,gnv,electrico'],
            'est' => [$vehiculo ? 'required' : 'nullable', 'in:activo,mantenimiento,inactivo'],
        ];
    }

    public function messages(): array
    {
        return ['pla.regex' => 'La placa debe tener el formato boliviano 1234-ABC.'];
    }
}
