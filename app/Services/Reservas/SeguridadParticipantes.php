<?php

namespace App\Services\Reservas;

use App\Models\ParticipanteReserva;
use App\Models\SalidaTour;
use App\Support\ReglasEntrada;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/** Política interna de acompañamiento. */
class SeguridadParticipantes
{
    public const VERSION = 'menores-2026-11-v1';

    public const NOMBRE = ReglasEntrada::NOMBRE;

    public function validar(SalidaTour $salida, array $participantes): array
    {
        Validator::make(['participantes' => $participantes], [
            'participantes' => ['required', 'array', 'min:1', 'max:50'],
            'participantes.*.nom_cpl' => ['required', 'string', 'max:150', self::NOMBRE],
            'participantes.*.fecha_nacimiento' => ['required', 'date_format:Y-m-d', 'before_or_equal:today', 'after:'.today()->subYears(111)->toDateString()],
        ], ['participantes.*.fecha_nacimiento.*' => 'Ingresa una fecha de nacimiento válida, no futura (hasta 110 años).'])->validate();

        $errores = [];
        foreach ($participantes as $i => &$p) {
            $p['eda'] = $this->edad($p['fecha_nacimiento'], $salida);
            $minima = $salida->tour->edadMinimaEfectiva();
            if ($p['eda'] > 110 || $p['eda'] < $minima || ($p['eda'] < 18 && ! $salida->tour->admiteMenores())) {
                $errores["participantes.$i.fecha_nacimiento"] = $salida->tour->esAltoRiesgo()
                    ? 'Tour de alto riesgo: solo se admiten participantes de 18 años o más el día de la salida. Una autorización no elimina esta restricción.'
                    : "Este tour requiere una edad mínima de {$minima} años y cumplir su política de menores.";
            }
        }
        unset($p);

        foreach ($participantes as $i => $p) {
            if ($p['eda'] >= 18) {
                continue;
            }
            $acompanante = filter_var($p['acompanante_indice'] ?? null, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
            if ($acompanante === null || $acompanante === $i || ! isset($participantes[$acompanante]) || $participantes[$acompanante]['eda'] < 18) {
                $errores["participantes.$i.acompanante_indice"] = 'Elige un adulto de esta reserva que acompañará al menor durante todo el tour.';
            }
            $v = Validator::make($p, [
                'responsable_nombre' => ['required', 'string', 'max:150', self::NOMBRE],
                'responsable_documento' => ['required', 'string', 'regex:/^[A-Za-z0-9-]{5,20}$/'],
                'vinculo' => ['required', 'in:ambos_padres,un_progenitor,tutor,tercero'],
                'consentimiento_menor' => ['accepted'],
            ], ['required' => 'Completa los datos del representante y la autorización del menor.', 'accepted' => 'El representante debe aceptar la declaración para el menor.']);
            foreach ($v->errors()->messages() as $campo => $mensajes) {
                $errores["participantes.$i.$campo"] = $mensajes[0];
            }
        }
        if ($errores) {
            throw ValidationException::withMessages($errores);
        }

        return $participantes;
    }

    public function edad(string $nacimiento, SalidaTour $salida): int
    {
        return (int) CarbonImmutable::createFromFormat('!Y-m-d', $nacimiento)->diffInYears($salida->fec_sal->startOfDay());
    }

    /** Invalida revisión ante cambios. */
    public function huella(ParticipanteReserva $p, SalidaTour $salida): string
    {
        return hash('sha256', json_encode([
            self::VERSION, $salida->id, $salida->fec_sal->toDateString(), $salida->hra_sal,
            $salida->tur_id, $salida->tour->edadMinimaEfectiva(), $salida->tour->admiteMenores(),
            $p->nom_cpl, $p->fecha_nacimiento?->toDateString(), $p->num_doc, $p->acompanante_id,
            $p->acompanante?->only(['nom_cpl', 'num_doc', 'fecha_nacimiento']),
            array_diff_key($p->autorizacion_menor ?? [], array_flip(['verificada_en', 'verificada_por', 'huella'])),
        ], JSON_THROW_ON_ERROR));
    }

    /** Bloqueo incluso con boleto emitido. */
    public function bloqueo(ParticipanteReserva $p, SalidaTour $salida): ?string
    {
        $edad = $p->fecha_nacimiento ? $this->edad($p->fecha_nacimiento->toDateString(), $salida) : $p->eda;
        if ($edad === null || $edad < $salida->tour->edadMinimaEfectiva() || ($edad < 18 && ! $salida->tour->admiteMenores())) {
            return 'El participante no cumple la edad o la política de seguridad de este tour.';
        }
        if ($edad < 18) {
            $a = $p->acompanante;
            if (! $p->fecha_nacimiento || ! $a || $a->rsv_id !== $p->rsv_id || ! $a->fecha_nacimiento || $this->edad($a->fecha_nacimiento->toDateString(), $salida) < 18) {
                return 'Falta un acompañante adulto válido y la fecha de nacimiento del menor.';
            }
            $aut = $p->autorizacion_menor ?? [];
            if (empty($aut['verificada_en']) || empty($aut['huella']) || ! hash_equals($aut['huella'], $this->huella($p, $salida))) {
                return 'La agencia debe verificar los documentos y la autorización del menor para esta salida antes de emitir o validar su boleto.';
            }
        }

        return null;
    }
}
