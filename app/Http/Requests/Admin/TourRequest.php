<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/** Datos de un tour (RF-39): formatos de nombre, montos, duración de un día y altitudes coherentes. */
class TourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'nom' => trim(preg_replace('/\s+/', ' ', (string) $this->input('nom'))),
            'act' => $this->boolean('act'),
        ]);
    }

    public function rules(): array
    {
        return [
            'cat_id' => ['required', 'integer', 'exists:categorias_tour,id'],
            'nom' => ['required', 'string', 'min:3', 'max:150', "regex:/^[\pL\pN\s&'.,()-]+$/u"],
            'des' => ['nullable', 'string', 'max:5000'],
            'niv_dif' => ['required', 'in:facil,moderado,dificil,experto'],
            'alt_min_m' => ['nullable', 'integer', 'between:0,7000'],
            'alt_max_m' => ['nullable', 'integer', 'between:0,7000', 'gte:alt_min_m'],
            'dur_hrs' => ['required', 'numeric', 'between:1,14', 'regex:/^\d{1,2}(\.\d{1,2})?$/'],
            'req_fis' => ['nullable', 'string', 'max:1000'],
            'pre_bas' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'regex:/^\d+(\.\d{1,2})?$/'],
            'cap_max' => ['required', 'integer', 'min:1', 'max:60'],
            'act' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.regex' => 'El nombre solo puede tener letras, números y signos básicos.',
            'dur_hrs.between' => 'Los tours son de un solo día: entre 1 y 14 horas.',
            'pre_bas.regex' => 'El precio admite hasta 2 decimales.',
            'alt_max_m.gte' => 'La altitud máxima no puede ser menor que la mínima.',
        ];
    }
}
