<?php

namespace App\Http\Requests\Reservas;

use App\Models\SalidaTour;
use App\Services\Reservas\BorradorReserva;
use App\Services\Reservas\SeguridadParticipantes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

/** Documentos según tipo de participante. */
class ParticipantesRequest extends FormRequest
{
    public const NOMBRE = SeguridadParticipantes::NOMBRE;

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
            'cor_ctc' => $this->user()?->cor ?? strtolower(trim((string) $this->input('cor_ctc'))),
            'nom_ctc' => $this->user()?->nom ?? trim(preg_replace('/\s+/', ' ', (string) $this->input('nom_ctc'))),
            'tel_ctc' => $this->has('prefijo_ctc') ? (string) $this->input('prefijo_ctc').(string) $this->input('telefono_nacional') : (string) $this->input('tel_ctc'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nom_ctc' => ['required', 'string', 'max:150', self::NOMBRE],
            'cor_ctc' => ['required', 'email:rfc', 'max:150'],
            'tel_ctc' => ['required', 'string', 'regex:/^\+[1-9][0-9]{6,14}$/'],
            'prefijo_ctc' => ['sometimes', 'string', Rule::in(array_keys(config('turismo.prefijos')))],
            'telefono_nacional' => ['required_with:prefijo_ctc', 'nullable', 'regex:/^[0-9]{4,14}$/'],
            'participantes' => ['required', 'array', 'min:1', 'max:50'],
            'participantes.*.nom_cpl' => ['required', 'string', 'max:150', self::NOMBRE],
            'participantes.*.tip_doc' => ['required', 'in:CI,PASAPORTE'],
            'participantes.*.num_doc' => ['required', 'string', 'max:15', 'regex:/^[A-Z0-9-]+$/'],
            'participantes.*.ci_cmp' => ['nullable', 'string', 'max:5', 'regex:/^[A-Z0-9]*$/'],
            'participantes.*.ci_dep_id' => ['nullable', 'required_if:participantes.*.tip_doc,CI', 'integer', 'exists:departamentos_bolivia,id'],
            'participantes.*.fecha_nacimiento' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after:'.today()->subYears(111)->toDateString()],
            'participantes.*.acompanante_indice' => ['nullable', 'integer', 'min:0'],
            'participantes.*.responsable_nombre' => ['nullable', 'string', 'max:150', self::NOMBRE],
            'participantes.*.responsable_documento' => ['nullable', 'string', 'max:20'],
            'participantes.*.vinculo' => ['nullable', 'in:ambos_padres,un_progenitor,tutor,tercero'],
            'participantes.*.consentimiento_menor' => ['nullable', 'boolean'],
            'participantes.*.pai_id' => ['required', 'integer', 'exists:paises,id'],
            'participantes.*.es_ctc_pri' => ['nullable', 'boolean'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $v) {
            $participantes = $this->input('participantes', []);
            if (! $v->errors()->isNotEmpty()) {
                $salida = SalidaTour::with('tour')->find(app(BorradorReserva::class)->get('sal_tur_id'));
                if ($salida) {
                    try {
                        app(SeguridadParticipantes::class)->validar($salida, $participantes);
                    } catch (ValidationException $e) {
                        foreach ($e->errors() as $campo => $mensajes) {
                            $v->errors()->add($campo, $mensajes[0]);
                        }
                    }
                }
            }
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
            'tel_ctc.regex' => __('Elige un prefijo e ingresa solo números (7 a 15 dígitos en total).'),
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
