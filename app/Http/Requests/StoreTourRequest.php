<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cat_id' => 'required|exists:categorias_tour,id',
            'cod' => 'required|string|max:20|unique:tours,cod',
            'nom' => 'required|string|max:150',
            'des' => 'nullable|string',
            'niv_dif' => 'required|in:facil,moderado,dificil,experto',
            'alt_min_m' => 'nullable|integer',
            'alt_max_m' => 'nullable|integer',
            'dur_hrs' => 'required|numeric|between:1,14',
            'req_fis' => 'nullable|string',
            'pre_bas' => 'required|numeric|min:0',
            'cap_max' => 'required|integer|min:1|max:25',
            'act' => 'boolean',
        ];
    }

    public function messages(): array
{
    return [
        'cap_max.max' => 'La capacidad máxima no puede ser mayor a 25 personas.',
        'cap_max.min' => 'La capacidad máxima debe ser de al menos 1 persona.',
        'cap_max.required' => 'La capacidad máxima es obligatoria.',
    ];
}
}