<?php

namespace App\Http\Controllers;

use App\Models\ConversacionChatbot;
use App\Services\Chatbot\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Widget público del asistente turístico (RF-28). */
class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbot) {}

    public function index(Request $request): View
    {
        $conversacion = $this->conversacion($request);

        return view('chatbot.index', [
            'mensajes' => $this->chatbot->mensajesDesde($conversacion, 0),
            'derivado' => (bool) $conversacion->der_hum,
        ]);
    }

    public function enviar(Request $request): JsonResponse
    {
        $datos = $request->validate(['mensaje' => ['required', 'string', 'max:500']]);
        $conversacion = $this->conversacion($request);
        $resultado = $this->chatbot->responder($conversacion, $datos['mensaje']);

        return response()->json($resultado + ['mensajes' => $this->chatbot->mensajesDesde($conversacion, (int) $request->input('ultimo', 0))]);
    }

    /** Sondeo: mensajes nuevos (p. ej. respuestas de un asesor tras la derivación). */
    public function mensajes(Request $request): JsonResponse
    {
        $conversacion = $this->conversacion($request);

        return response()->json([
            'mensajes' => $this->chatbot->mensajesDesde($conversacion, (int) $request->query('desde', 0)),
            'derivado' => (bool) $conversacion->der_hum,
        ]);
    }

    private function conversacion(Request $request): ConversacionChatbot
    {
        $sesion = $request->session()->get('chatbot.sesion');
        if (! $sesion) {
            $sesion = (string) Str::uuid();
            $request->session()->put('chatbot.sesion', $sesion);
        }

        return $this->chatbot->conversacion($sesion, $request->user());
    }
}
