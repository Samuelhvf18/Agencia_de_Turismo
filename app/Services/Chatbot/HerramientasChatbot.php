<?php

namespace App\Services\Chatbot;

use App\Models\ConversacionChatbot;
use App\Models\Reserva;
use App\Models\Tour;
use App\Services\Notificaciones\NotificacionService;

/**
 * Herramientas del chatbot (RF-28 a RF-31). Las usa el agente de n8n a
 * través de /api/n8n/chatbot/* y también el respaldo local cuando n8n no
 * responde. Solo leen datos, salvo la derivación a una persona.
 */
class HerramientasChatbot
{
    /** Preguntas frecuentes: clave => [es, en] */
    public const FAQ = [
        'altura' => [
            'La Paz está a 3.650 m. Descansa el primer día, toma mate de coca y mucha agua, come liviano y evita el alcohol. Si tienes dolor de cabeza fuerte o mareo persistente, avisa a tu guía.',
            'La Paz sits at 3,650 m. Rest on your first day, drink coca tea and plenty of water, eat light and avoid alcohol. Tell your guide if you have a strong headache or persistent dizziness.',
        ],
        'que_llevar' => [
            'Lleva ropa en capas, chaqueta impermeable, gorro y guantes, protector solar, lentes de sol, agua (1,5 L) y algo de comida. Puedes alquilar equipo al reservar.',
            'Bring layered clothing, a waterproof jacket, hat and gloves, sunscreen, sunglasses, water (1.5 L) and snacks. You can rent equipment when booking.',
        ],
        'pagos' => [
            'Puedes pagar en línea con tarjeta, QR bancario o transferencia, o en efectivo en oficina. Se acepta un anticipo de al menos 30 %; los tickets QR se emiten al completar el pago.',
            'You can pay online by card, bank QR or transfer, or in cash at our office. A deposit of at least 30% is accepted; QR tickets are issued once the payment is complete.',
        ],
        'cancelacion' => [
            'Puedes cancelar desde «Consultar reserva» hasta el día anterior a la salida; lo pagado se registra como reembolso. Las reservas sin pago se liberan a las 24 horas.',
            'You can cancel from "Check booking" until the day before departure; any payment is registered as a refund. Unpaid bookings are released after 24 hours.',
        ],
        'horarios' => [
            'La mayoría de las salidas parten a las 08:00 desde nuestra oficina en la calle Sagárnaga. Llega 15 minutos antes con tu ticket QR.',
            'Most departures leave at 08:00 from our office on Sagárnaga street. Arrive 15 minutes early with your QR ticket.',
        ],
        'ficha_medica' => [
            'En tours de alto riesgo (difíciles, expertos o sobre 4.500 m) cada participante debe firmar la ficha médica en línea; sin ella no se emite el boleto.',
            'On high-risk tours (hard, expert or above 4,500 m) each participant must sign the medical form online; without it no ticket is issued.',
        ],
    ];

    /**
     * Tours activos que cumplen tiempo, condición física y presupuesto.
     *
     * @return array<int, array>
     */
    public function recomendarTours(?float $horasMax = null, ?string $dificultad = null, ?float $presupuesto = null, ?string $categoria = null, int $limite = 3): array
    {
        $orden = ['facil' => 1, 'moderado' => 2, 'dificil' => 3, 'experto' => 4];

        return Tour::with('categoria')
            ->where('act', true)->whereNull('eli_en')
            ->when($horasMax, fn ($q, $h) => $q->where('dur_hrs', '<=', $h))
            ->when($presupuesto, fn ($q, $p) => $q->where('pre_bas', '<=', $p))
            ->when($categoria, fn ($q, $c) => $q->whereHas('categoria', fn ($w) => $w->where('rta_amg', $c)->orWhereRaw('LOWER(nom) = ?', [mb_strtolower($c)])))
            ->get()
            ->filter(fn (Tour $t) => ! $dificultad || ($orden[$t->niv_dif] ?? 9) <= ($orden[$dificultad] ?? 9))
            ->sortBy([['pre_bas', 'asc']])
            ->take($limite)
            ->map(fn (Tour $t) => [
                'nombre' => $t->nom,
                'categoria' => $t->categoria->nom,
                'duracion_horas' => (float) $t->dur_hrs,
                'dificultad' => $t->niv_dif,
                'altitud_max_m' => $t->alt_max_m,
                'precio_bs' => (float) $t->pre_bas,
                'url' => route('tours.show', $t),
                'proxima_salida' => $t->salidas()->where('est', 'programada')->whereDate('fec_sal', '>=', today())->orderBy('fec_sal')->value('fec_sal')?->format('d/m/Y'),
            ])
            ->values()
            ->all();
    }

    /** Estado de una reserva en solo lectura (requiere código y correo del contacto). */
    public function consultarReserva(string $codigo, string $correo): ?array
    {
        $r = Reserva::with('salida.tour')
            ->where('cod', strtoupper(trim($codigo)))
            ->where('cor_ctc', mb_strtolower(trim($correo)))
            ->first();

        return $r ? [
            'codigo' => $r->cod,
            'tour' => $r->salida->tour->nom,
            'fecha' => $r->salida->fec_sal->format('d/m/Y'),
            'hora' => substr($r->salida->hra_sal, 0, 5),
            'participantes' => $r->cnt_ptc,
            'estado' => $r->est,
            'estado_pago' => $r->est_pag,
            'total_bs' => (float) $r->pre_tot,
            'url' => route('reservas.consultar', ['cod' => $r->cod]),
        ] : null;
    }

    public function faq(?string $tema = null, string $idioma = 'es'): array
    {
        $i = $idioma === 'en' ? 1 : 0;
        $lista = array_map(fn ($par) => $par[$i], self::FAQ);

        return $tema ? array_intersect_key($lista, [$tema => true]) : $lista;
    }

    /** Deriva la conversación a una persona y avisa al equipo de operaciones (RF-31). */
    public function derivar(ConversacionChatbot $conversacion, string $motivo = ''): void
    {
        if ($conversacion->der_hum) {
            return;
        }
        $conversacion->update(['der_hum' => true]);
        app(NotificacionService::class)->aPersonal('derivacion_chatbot', [
            'conversacion' => $conversacion->id,
            'mensaje' => 'Un cliente pidió hablar con una persona en el chat'.($motivo ? ": «{$motivo}»" : '.'),
            'url' => route('admin.chatbot.show', $conversacion),
        ], ['operaciones', 'admin']);
    }
}
