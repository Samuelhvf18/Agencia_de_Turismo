<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function enviar(Request $request)
    {
        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'max:500'],
        ]);

        $apiKey = config('services.gemini.key');
        $modelo = config('services.gemini.model');

        if (empty($apiKey) || empty($modelo)) {
            return response()->json([
                'error' => 'El servicio de inteligencia artificial no está configurado.',
            ], 503);
        }

        $instrucciones =
            'Eres el asistente turístico de Pipocas, agencia de La Paz, Bolivia. ' .
            'Responde siempre en español, de forma amable, clara y breve. ' .
            'Utiliza texto normal, sin escribir asteriscos de escape ni códigos Markdown literales. ' .
            'Puedes usar listas sencillas cuando sean útiles. ' .
            'Ayuda con destinos, actividades, transporte y consejos turísticos. ' .
            'No inventes precios, horarios oficiales ni disponibilidad. ' .
            'Si no conoces un dato, dilo claramente. ' .
            'No repitas saludos en cada respuesta.';

        try {
            set_time_limit(120);

            // Máximo dos intentos si Gemini devuelve un error temporal.
            for ($intento = 1; $intento <= 2; $intento++) {
                $respuesta = Http::connectTimeout(15)
                    ->timeout(45)
                    ->withHeaders([
                        'x-goog-api-key' => $apiKey,
                    ])
                    ->post(
                        'https://generativelanguage.googleapis.com/v1beta/interactions',
                        [
                            'model' => $modelo,
                            'input' => $datos['mensaje'],
                            'system_instruction' => $instrucciones,
                            'generation_config' => [
                                'max_output_tokens' => 1024,
                            ],
                        ]
                    );

                $cuerpo = $respuesta->json();

                Log::info('Respuesta de Gemini', [
                    'http_status' => $respuesta->status(),
                    'status' => data_get($cuerpo, 'status'),
                    'model' => data_get($cuerpo, 'model'),
                    'intento' => $intento,
                    'error' => data_get($cuerpo, 'error.message'),
                ]);

                // Reintentar únicamente ante errores temporales.
                if (
                    in_array($respuesta->status(), [429, 500, 502, 503, 504], true)
                    && $intento < 2
                ) {
                    usleep(700000);
                    continue;
                }

                if (!$respuesta->successful()) {
                    $estado = $respuesta->status();

                    if ($estado === 429) {
                        return response()->json([
                            'error' => 'Gemini alcanzó su límite de consultas. Inténtalo más tarde.',
                        ], 429);
                    }

                    if (in_array($estado, [500, 502, 503, 504], true)) {
                        return response()->json([
                            'error' => 'El asistente está temporalmente ocupado. Inténtalo nuevamente en unos segundos.',
                        ], 503);
                    }

                    Log::warning('Error de la API de Gemini', [
                        'status' => $estado,
                        'error' => data_get($cuerpo, 'error.message'),
                    ]);

                    return response()->json([
                        'error' => 'No se pudo procesar la pregunta. Revisa la configuración del asistente.',
                    ], 502);
                }

                if (data_get($cuerpo, 'status') !== 'completed') {
                    Log::warning('Respuesta incompleta de Gemini', [
                        'status' => data_get($cuerpo, 'status'),
                    ]);

                    return response()->json([
                        'error' => 'La respuesta quedó incompleta. Inténtalo nuevamente.',
                    ], 502);
                }

                $texto = collect(data_get($cuerpo, 'steps', []))
                    ->where('type', 'model_output')
                    ->flatMap(fn ($paso) => $paso['content'] ?? [])
                    ->where('type', 'text')
                    ->pluck('text')
                    ->implode("\n");

                $texto = trim($texto);

                // Quitar escapes literales que pueden aparecer en la respuesta.
                $texto = str_replace(
                    ["\\r\\n", "\\n", "\\r", "\\t", '\\"'],
                    ["\n", "\n", "\n", " ", '"'],
                    $texto
                );

                // Convertir Markdown escapado a texto legible.
                $texto = preg_replace('/\\\\([*`_~])/', '$1', $texto);
                $texto = preg_replace('/\\\\([.!?(){}\[\]#+\-])/', '$1', $texto);
                $texto = preg_replace("/[ \t]+\n/", "\n", $texto);
                $texto = preg_replace("/\n{3,}/", "\n\n", $texto);
                $texto = trim($texto);

                if ($texto === '') {
                    return response()->json([
                        'error' => 'El asistente no generó una respuesta. Inténtalo nuevamente.',
                    ], 502);
                }

                return response()->json([
                    'respuesta' => $texto,
                ]);
            }

            return response()->json([
                'error' => 'El asistente está temporalmente ocupado. Inténtalo nuevamente.',
            ], 503);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::warning('No se pudo conectar con Gemini', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'No se pudo conectar con el asistente. Revisa tu conexión.',
            ], 503);

        } catch (\Throwable $e) {
            Log::error('Error interno del chatbot', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Ocurrió un error al procesar tu pregunta.',
            ], 500);
        }
    }
}