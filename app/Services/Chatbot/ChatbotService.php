<?php

namespace App\Services\Chatbot;

use App\Models\ConversacionChatbot;
use App\Models\MensajeChatbot;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/** Orquestación del chatbot turístico. */
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

        // El asesor asume la conversación.
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
            'tours_ids' => array_column($this->herramientas->tarjetas($resultado['tours_ids'] ?? []), 'id'),
        ]);

        return [
            'respuesta' => $resultado['respuesta'],
            'intencion' => $resultado['intencion'] ?? null,
            'derivado' => (bool) $conversacion->fresh()->der_hum,
            'modo' => $modo,
            'idioma' => $idioma,
        ];
    }

    /** Sondeo de mensajes nuevos. */
    public function mensajesDesde(ConversacionChatbot $conversacion, int $ultimoId): array
    {
        $mensajes = $conversacion->mensajes()->where('id', '>', max(0, $ultimoId))->orderByDesc('id')->limit(100)->get()->reverse()->values();
        $ids = $mensajes->flatMap(fn ($m) => $m->tours_ids ?? [])->unique()->values()->all();
        $tarjetas = collect($this->herramientas->tarjetas($ids, 100))->keyBy('id');

        return $mensajes->map(fn (MensajeChatbot $m) => [
            'id' => $m->id, 'rem' => $m->rem, 'txt' => $m->txt_msj, 'humano' => $m->int_det === 'humano',
            'hora' => $m->cre_en?->copy()->timezone(config('turismo.zona_horaria'))->format('H:i'),
            'tours' => collect($m->tours_ids ?? [])->take(3)->filter(fn ($id) => isset($tarjetas[$id]))->map(fn ($id) => $tarjetas[$id])->values()->all(),
        ])->all();
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

            $ids = is_array($respuesta['tours_ids'] ?? null) ? $respuesta['tours_ids']
                : $this->herramientas->mencionados($texto.' '.(string) $respuesta['respuesta']);
            if (! $ids && empty($respuesta['derivar']) && preg_match('/(recomiend|recommend|tour|excursi)/iu', $texto)) {
                $ids = $this->local->responder($conversacion, $texto, $idioma)['tours_ids'] ?? [];
            }

            return [
                'respuesta' => mb_substr((string) $respuesta['respuesta'], 0, 4000),
                'intencion' => $respuesta['intencion'] ?? null,
                'derivar' => (bool) ($respuesta['derivar'] ?? false),
                'tours_ids' => $ids,
            ];
        } catch (Throwable $e) {
            Log::warning('Chatbot: n8n no respondió, se usa el asistente local', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
