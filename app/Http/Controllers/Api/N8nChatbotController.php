<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConversacionChatbot;
use App\Services\Chatbot\HerramientasChatbot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Herramientas que usa el agente de IA de n8n (flujo docs/n8n/chatbot.json).
 * Todas son de solo lectura salvo la derivación a una persona.
 */
class N8nChatbotController extends Controller
{
    public function __construct(private HerramientasChatbot $herramientas) {}

    public function tours(Request $request): JsonResponse
    {
        $d = $request->validate([
            'horas_max' => ['nullable', 'numeric', 'min:1', 'max:14'],
            'dificultad' => ['nullable', 'in:facil,moderado,dificil,experto'],
            'presupuesto' => ['nullable', 'numeric', 'min:0'],
            'categoria' => ['nullable', 'string', 'max:50'],
        ]);

        return response()->json(['tours' => $this->herramientas->recomendarTours(
            $d['horas_max'] ?? null, $d['dificultad'] ?? null, $d['presupuesto'] ?? null, $d['categoria'] ?? null, 5
        )]);
    }

    public function faq(Request $request): JsonResponse
    {
        return response()->json(['faq' => $this->herramientas->faq($request->query('tema'), $request->query('idioma', 'es') === 'en' ? 'en' : 'es')]);
    }

    public function reserva(Request $request): JsonResponse
    {
        $d = $request->validate(['codigo' => ['required', 'string', 'max:20'], 'correo' => ['required', 'email']]);
        $reserva = $this->herramientas->consultarReserva($d['codigo'], $d['correo']);

        return response()->json(['encontrada' => (bool) $reserva, 'reserva' => $reserva]);
    }

    public function derivar(Request $request): JsonResponse
    {
        $d = $request->validate(['conversacion_id' => ['required', 'integer', 'exists:conversaciones_chatbot,id'], 'motivo' => ['nullable', 'string', 'max:300']]);
        $this->herramientas->derivar(ConversacionChatbot::findOrFail($d['conversacion_id']), $d['motivo'] ?? '');

        return response()->json(['ok' => true]);
    }
}
