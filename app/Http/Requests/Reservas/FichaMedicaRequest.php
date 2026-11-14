<?php

namespace App\Http\Requests\Reservas;

use App\Models\SalidaTour;
use App\Services\Reservas\BorradorReserva;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Ficha médica y consentimiento (RF-08). En tours de alto riesgo todos los
 * participantes deben completarla y firmarla; en los demás es opcional, pero
 * si alguien firma debe aceptar ambas declaraciones.
 *
 * Los participantes, el tour y las firmas ya guardadas se leen del borrador.
 */
class FichaMedicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fichas' => ['nullable', 'array'],
            'fichas.*.con_med' => ['nullable', 'string', 'max:1000'],
            'fichas.*.nom_ctc_eme' => ['nullable', 'string', 'max:150', ParticipantesRequest::NOMBRE],
            'fichas.*.tel_ctc_eme' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s-]{7,20}$/'],
            'fichas.*.fir_b64' => ['nullable', 'string', 'max:420000'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $v) {
            $borrador = app(BorradorReserva::class);
            $salida = SalidaTour::with('tour')->find($borrador->get('sal_tur_id'));
            $altoRiesgo = (bool) $salida?->tour->esAltoRiesgo();
            $indices = array_keys($borrador->get('participantes', []));
            $firmados = array_keys(array_filter($borrador->get('fichas', []), fn ($f) => ! empty($f['rta_fir'])));

            foreach ($indices as $i) {
                $f = $this->input("fichas.$i", []);
                $firma = ! empty($f['fir_b64']) || in_array($i, $firmados, true);
                $llenoAlgo = $firma || ! empty($f['con_med']) || ! empty($f['nom_ctc_eme']) || ! empty($f['tel_ctc_eme']);

                if (! $altoRiesgo && ! $llenoAlgo) {
                    continue; // ficha opcional y vacía
                }

                foreach (['con_med' => __('Indica tus condiciones médicas o escribe «Ninguna».'),
                    'nom_ctc_eme' => __('Ingresa un contacto de emergencia.'),
                    'tel_ctc_eme' => __('Ingresa el teléfono de emergencia.')] as $campo => $mensaje) {
                    if (empty($f[$campo])) {
                        $v->errors()->add("fichas.$i.$campo", $mensaje);
                    }
                }
                if (empty($f['acp_ver'])) {
                    $v->errors()->add("fichas.$i.acp_ver", __('Debes aceptar la declaración.'));
                }
                if (empty($f['acp_rie'])) {
                    $v->errors()->add("fichas.$i.acp_rie", __('Debes aceptar los riesgos de la actividad.'));
                }
                if (! $firma) {
                    $v->errors()->add("fichas.$i.fir_b64", __('Falta la firma.'));
                }
            }
        }];
    }

    public function messages(): array
    {
        return [
            'fichas.*.nom_ctc_eme.regex' => __('El nombre solo puede contener letras.'),
            'fichas.*.tel_ctc_eme.regex' => __('Ingresa un teléfono válido.'),
        ];
    }
}
