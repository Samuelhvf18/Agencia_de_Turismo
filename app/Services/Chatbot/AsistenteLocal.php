<?php

namespace App\Services\Chatbot;

use App\Models\ConversacionChatbot;

/**
 * Respaldo del chatbot cuando la IA de n8n no responde (RNF-20): reconoce
 * las intenciones principales con reglas y usa las mismas herramientas.
 * Responde en el idioma del usuario (español o inglés).
 */
class AsistenteLocal
{
    public function __construct(private HerramientasChatbot $herramientas) {}

    /** Idioma del mensaje: 'en' si predominan palabras en inglés, si no 'es'. */
    public static function idioma(string $texto): string
    {
        $t = ' '.mb_strtolower($texto).' ';
        $en = preg_match_all('/\b(the|what|how|where|when|which|is|are|can|i|you|my|tour|tours|booking|price|cheap|easy|hours|day|want|need|please|hello|hi|thanks)\b/u', $t);
        $es = preg_match_all('/\b(el|la|los|las|que|qué|cómo|como|donde|dónde|cuando|cuándo|es|son|puedo|quiero|necesito|mi|reserva|precio|barato|fácil|facil|horas|día|dia|hola|gracias|por|para)\b/u', $t);

        return $en > $es ? 'en' : 'es';
    }

    /** @return array{respuesta: string, intencion: string, derivar: bool} */
    public function responder(ConversacionChatbot $conversacion, string $texto, string $idioma): array
    {
        $t = mb_strtolower($texto);
        $en = $idioma === 'en';

        if (preg_match('/\b(humano|persona|asesor|agente|operador|human|person|agent|someone)\b/u', $t)) {
            $this->herramientas->derivar($conversacion, $texto);

            return ['intencion' => 'derivacion', 'derivar' => true, 'respuesta' => $en
                ? 'I have passed your conversation to our team; an advisor will reply here shortly.'
                : 'Derivé tu conversación a nuestro equipo; un asesor te responderá aquí en breve.'];
        }

        if (preg_match('/\bres-([2-9a-hjkmnp-z]{6})\b/u', $t, $m)) {
            if (! preg_match('/[\w.+-]+@[\w-]+\.[\w.-]+/u', $texto, $correo)) {
                return ['intencion' => 'consulta_reserva', 'derivar' => false, 'respuesta' => $en
                    ? 'To check booking RES-'.strtoupper($m[1]).', please also send the contact email used when booking.'
                    : 'Para consultar la reserva RES-'.strtoupper($m[1]).' envíame también el correo de contacto con el que reservaste.'];
            }
            $r = $this->herramientas->consultarReserva('RES-'.strtoupper($m[1]), $correo[0]);

            return ['intencion' => 'consulta_reserva', 'derivar' => false, 'respuesta' => $r
                ? ($en
                    ? "Booking {$r['codigo']}: {$r['tour']} on {$r['fecha']} at {$r['hora']}, {$r['participantes']} participant(s). Status: {$r['estado']}, payment: {$r['estado_pago']}. Details: {$r['url']}"
                    : "Reserva {$r['codigo']}: {$r['tour']} el {$r['fecha']} a las {$r['hora']}, {$r['participantes']} participante(s). Estado: {$r['estado']}, pago: {$r['estado_pago']}. Detalle: {$r['url']}")
                : ($en ? 'I could not find a booking with that code and email.' : 'No encontré una reserva con ese código y correo.')];
        }

        foreach ($this->temasFaq() as $tema => $patron) {
            if (preg_match($patron, $t)) {
                return ['intencion' => 'faq_'.$tema, 'derivar' => false, 'respuesta' => $this->herramientas->faq($tema, $idioma)[$tema]];
            }
        }

        if (preg_match('/(recomiend|suger|qué hacer|que hacer|tour|excursi|paseo|presupuesto|barat|recommend|suggest|budget|cheap|activity|trip)/u', $t)) {
            [$horas, $dificultad, $presupuesto] = $this->criterios($t);
            $tours = $this->herramientas->recomendarTours($horas, $dificultad, $presupuesto);
            if (! $tours) {
                return ['intencion' => 'recomendacion', 'derivar' => false, 'respuesta' => $en
                    ? 'I found no tours with those conditions. Try a higher budget or more hours.'
                    : 'No encontré tours con esas condiciones. Prueba con más presupuesto o más horas.'];
            }
            $lineas = array_map(fn ($x) => "• {$x['nombre']} ({$x['categoria']}, {$x['duracion_horas']} h, ".($en ? 'level ' : 'nivel ')."{$x['dificultad']}, Bs. {$x['precio_bs']}) — {$x['url']}", $tours);

            return ['intencion' => 'recomendacion', 'derivar' => false, 'respuesta' => ($en ? "These tours fit what you are looking for:\n" : "Estos tours se ajustan a lo que buscas:\n").implode("\n", $lineas)];
        }

        return ['intencion' => 'saludo', 'derivar' => false, 'respuesta' => $en
            ? "Hi! I'm Pipocas' travel assistant. I can recommend tours by time, fitness and budget, answer questions (altitude, what to bring, payments, cancellations) and check your booking with its code and email. You can also ask to talk to a person."
            : "¡Hola! Soy el asistente de Pipocas. Puedo recomendarte tours según tu tiempo, condición física y presupuesto, responder dudas (altura, qué llevar, pagos, cancelaciones) y consultar tu reserva con su código y correo. También puedes pedir hablar con una persona."];
    }

    /** @return array{0: ?float, 1: ?string, 2: ?float} horas máximas, dificultad máxima y presupuesto */
    private function criterios(string $t): array
    {
        $horas = null;
        if (preg_match('/(\d+(?:[.,]\d+)?)\s*(h\b|hrs?|horas?|hours?)/u', $t, $m)) {
            $horas = (float) str_replace(',', '.', $m[1]);
        } elseif (preg_match('/(medio día|medio dia|half day|mañana|tarde|morning|afternoon)/u', $t)) {
            $horas = 5;
        }

        $dificultad = match (true) {
            (bool) preg_match('/(fácil|facil|tranquil|suave|niños|ninos|mayores|easy|relaxed|kids)/u', $t) => 'facil',
            (bool) preg_match('/(moderad|medio|moderate|intermediate)/u', $t) => 'moderado',
            (bool) preg_match('/(difícil|dificil|exigente|aventura|hard|challenging|adventure)/u', $t) => 'dificil',
            default => null,
        };

        $presupuesto = null;
        if (preg_match('/(?:bs\.?|bob|\$|usd)\s*(\d+)|(\d+)\s*(?:bs|bolivianos|bob|dólares|dolares|usd|\$)/u', $t, $m)) {
            $monto = (float) ($m[1] ?: $m[2]);
            $presupuesto = preg_match('/(\$|usd|dólar|dolar)/u', $t) ? $monto * 6.96 : $monto;
        }

        return [$horas, $dificultad, $presupuesto];
    }

    private function temasFaq(): array
    {
        return [
            'altura' => '/(altura|soroche|altitud|mal de altura|aclimat|altitude|sickness|acclimat)/u',
            'que_llevar' => '/(qué llevar|que llevar|llevo|llevar|ropa|equipaje|bring|pack|clothes|wear)/u',
            'pagos' => '/(pago|pagar|tarjeta|qr|transferencia|efectivo|anticipo|pay|payment|card|cash|deposit)/u',
            'cancelacion' => '/(cancel|reembols|devoluci|refund)/u',
            'horarios' => '/(horario|hora de salida|a qué hora|a que hora|punto de encuentro|schedule|what time|meeting point)/u',
            'ficha_medica' => '/(ficha|médic|medic|firma|medical|form)/u',
        ];
    }
}
