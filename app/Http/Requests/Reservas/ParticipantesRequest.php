<?php

namespace App\Http\Requests\Reservas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Datos de contacto y participantes (RF-06, RF-39). Regla de documentos:
 * CI boliviano con complemento opcional y departamento de expedición
 * obligatorio; pasaporte o documento extranjero sin complemento ni departamento.
 */
class ParticipantesRequest extends FormRequest
{
    public const NOMBRE = "regex:/^[\pL\s'.-]+$/u";

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $participantes = collect($this->input('participantes', []))
            ->map(function ($p) {
                $p = (array) $p;
                $p['tip_doc'] = strtoupper(trim($p['tip_doc'] ?? ''));
                $p['num_doc'] = strtoupper(preg_replace('/\s+/', '', $p['num_doc'] ?? ''));
                $p['ci_cmp'] = strtoupper(trim($p['ci_cmp'] ?? ''));
                $p['nom_cpl'] = trim(preg_replace('/\s+/', ' ', $p['nom_cpl'] ?? ''));

                return $p;
            })
            ->values()
            ->all();

        $this->merge([
            'participantes' => $participantes,
            'cor_ctc' => strtolower(trim((string) $this->input('cor_ctc'))),
            'nom_ctc' => trim(preg_replace('/\s+/', ' ', (string) $this->input('nom_ctc'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'nom_ctc' => ['required', 'string', 'max:150', self::NOMBRE],
            'cor_ctc' => ['required', 'email', 'max:150'],
            'tel_ctc' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9\s-]{7,20}$/'],
            'participantes' => ['required', 'array', 'min:1', 'max:50'],
            'participantes.*.nom_cpl' => ['required', 'string', 'max:150', self::NOMBRE],
            'participantes.*.tip_doc' => ['required', 'in:CI,PASAPORTE'],
            'participantes.*.num_doc' => ['required', 'string', 'max:15', 'regex:/^[A-Z0-9-]+$/'],
            'participantes.*.ci_cmp' => ['nullable', 'string', 'max:5', 'regex:/^[A-Z0-9]*$/'],
            'participantes.*.ci_dep_id' => ['nullable', 'required_if:participantes.*.tip_doc,CI', 'integer', 'exists:departamentos_bolivia,id'],
            'participantes.*.eda' => ['required', 'integer', 'min:1', 'max:110'],
            'participantes.*.pai_id' => ['required', 'integer', 'exists:paises,id'],
            'participantes.*.es_ctc_pri' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $v) {
            $participantes = $this->input('participantes', []);
            foreach ($participantes as $i => $p) {
                if (($p['tip_doc'] ?? null) === 'CI' && ! preg_match('/^[0-9]{5,10}$/', $p['num_doc'] ?? '')) {
                    $v->errors()->add("participantes.$i.num_doc", __('El CI debe tener solo números (5 a 10 dígitos).'));
                }
            }

            $documentos = collect($participantes)->map(fn ($p) => ($p['tip_doc'] ?? '').($p['num_doc'] ?? '').($p['ci_cmp'] ?? ''));
            foreach ($documentos->duplicates() as $i => $doc) {
                $v->errors()->add("participantes.$i.num_doc", __('Este documento está repetido en la reserva.'));
            }

            if (collect($participantes)->filter(fn ($p) => ! empty($p['es_ctc_pri']))->count() !== 1) {
                $v->errors()->add('participantes', 'Marca exactamente un contacto principal.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'nom_ctc.regex' => __('El nombre solo puede contener letras.'),
            'tel_ctc.regex' => __('Ingresa un teléfono válido (solo números, + y guiones).'),
            'participantes.*.nom_cpl.required' => __('Ingresa el nombre completo.'),
            'participantes.*.nom_cpl.regex' => __('El nombre solo puede contener letras.'),
            'participantes.*.num_doc.required' => __('Ingresa el número de documento.'),
            'participantes.*.num_doc.regex' => __('El documento solo puede tener letras, números y guiones.'),
            'participantes.*.ci_dep_id.required_if' => __('Indica dónde se expidió el CI.'),
            'participantes.*.eda.required' => __('Ingresa la edad.'),
            'participantes.*.eda.*' => __('La edad debe estar entre 1 y 110 años.'),
            'participantes.*.pai_id.required' => __('Elige la nacionalidad.'),
        ];
    }
}
