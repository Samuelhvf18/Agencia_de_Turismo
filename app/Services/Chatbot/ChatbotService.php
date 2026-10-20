<?php

namespace App\Services\Chatbot;

use App\Models\ConversacionChatbot;
use App\Models\MensajeChatbot;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Chatbot turístico (Módulo 08, RF-28 a RF-31, RNF-20). Cada mensaje se
 * guarda en conversaciones_chatbot / mensajes_chatbot y se envía al agente
 * de IA orquestado en n8n (N8N_WEBHOOK_CHATBOT). Si n8n no está configurado,
 * tarda demasiado o falla, responde el AsistenteLocal (fallo controlado).
 */
class ChatbotService
{
    public const TIMEOUT_SEGUNDOS = 20;

    public function __construct(
        private AsistenteLocal $local,
        private HerramientasChatbot $herramientas,
    ) {}

    public function conversacion(string $sesion, ?User $usuario): ConversacionChatbot
    {
        $conversacion = ConversacionChatbot::where('id_ses', $sesion)->whereNull('fin_en')->latest('id')->first()
            ?? ConversacionChatbot::create(['id_ses' => $sesion, 'usr_id' => $usuario?->id, 'can' => 'web']);

        if ($usuario && ! $conversacion->usr_id) {
            $conversacion->update(['usr_id' => $usuario->id]);
        }

        return $conversacion;
    }

    /** @return array{respuesta: string, intencion: ?string, derivado: bool, modo: string, idioma: string} */
    public function responder(ConversacionChatbot $conversacion, string $texto): array
    {
        $texto = trim(mb_substr($texto, 0, 500));
        $idioma = AsistenteLocal::idioma($texto);
        $conversacion->mensajes()->create(['rem' => 'usuario', 'txt_msj' => $texto]);

        // Con la conversación derivada, responde una persona desde el panel.
        if ($conversacion->der_hum) {
            return ['respuesta' => '', 'intencion' => 'derivada', 'derivado' => true, 'modo' => 'humano', 'idioma' => $idioma];
        }

        $resultado = $this->consultarN8n($conversacion, $texto, $idioma);
        $modo = 'ia';
        if ($resultado === null) {
            $resultado = $this->local->responder($conversacion, $texto, $idioma);
            $modo = 'local';
        } elseif (! empty($resultado['derivar'])) {
            $this->herramientas->derivar($conversacion, $texto);
        }

        $conversacion->mensajes()->create([
            'rem' => 'bot',
            'txt_msj' => $resultado['respuesta'],
            'int_det' => mb_substr((string) ($resultado['intencion'] ?? $modo), 0, 50),
        ]);

        return [
            'respuesta' => $resultado['respuesta'],
            'intencion' => $resultado['intencion'] ?? null,
            'derivado' => (bool) $conversacion->fresh()->der_hum,
            'modo' => $modo,
            'idioma' => $idioma,
        ];
    }

    /** Mensajes nuevos de la conversación (para el sondeo del widget). */
    public function mensajesDesde(ConversacionChatbot $conversacion, int $ultimoId): array
    {
        return $conversacion->mensajes()->where('id', '>', $ultimoId)->orderBy('id')->get()
            ->map(fn (MensajeChatbot $m) => ['id' => $m->id, 'rem' => $m->rem, 'txt' => $m->txt_msj, 'humano' => $m->int_det === 'humano', 'hora' => $m->cre_en?->format('H:i')])
            ->all();
    }

    private function consultarN8n(ConversacionChatbot $conversacion, string $texto, string $idioma): ?array
    {
        $url = config('services.n8n.webhook_chatbot');
        if (! $url) {
            return null;
        }

        try {
            $historial = $conversacion->mensajes()->latest('id')->limit(10)->get()->reverse()
                ->map(fn ($m) => ['rol' => $m->rem === 'usuario' ? 'user' : 'assistant', 'texto' => $m->txt_msj])->values()->all();

            $respuesta = Http::timeout(self::TIMEOUT_SEGUNDOS)
                ->withHeaders(['X-Pipocas-Token' => (string) config('services.n8n.token')])
                ->post($url, [
                    'conversacion_id' => $conversacion->id,
                    'sesion' => $conversacion->id_ses,
                    'mensaje' => $texto,
                    'idioma' => $idioma,
                    'historial' => $historial,
                    'usuario' => $conversacion->usuario?->only(['nom', 'cor']),
                ])->throw()->json();

            if (! is_array($respuesta) || empty($respuesta['respuesta'])) {
                throw new \RuntimeException('Respuesta vacía de n8n');
            }

            return [
                'respuesta' => mb_substr((string) $respuesta['respuesta'], 0, 4000),
                'intencion' => $respuesta['intencion'] ?? null,
                'derivar' => (bool) ($respuesta['derivar'] ?? false),
            ];
        } catch (Throwable $e) {
            Log::warning('Chatbot: n8n no respondió, se usa el asistente local', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
