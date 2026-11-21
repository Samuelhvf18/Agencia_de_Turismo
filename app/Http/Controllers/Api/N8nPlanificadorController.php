<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/** Automatiza tareas de Artisan. */
class N8nPlanificadorController extends Controller
{
    /** tarea => [comando, opciones permitidas] */
    private const TAREAS = [
        'metricas-colas' => ['io:metricas-colas', ['--fecha', '--dias']],
        'recalibrar-markov' => ['io:recalibrar-markov', ['--sincronizar']],
        'propuestas-guias' => ['io:propuestas', ['--tipo' => 'guia', '--dias']],
        'propuestas-vehiculos' => ['io:propuestas', ['--tipo' => 'vehiculo', '--dias']],
        'alertas-eoq' => ['io:alertas-eoq', []],
        'proyectar-demanda' => ['io:proyectar-demanda', ['--meses']],
        'recordatorios' => ['reservas:recordatorios', []],
        'expirar-reservas' => ['reservas:expirar', ['--horas']],
    ];

    public function ejecutar(Request $request, string $tarea): JsonResponse
    {
        abort_unless(isset(self::TAREAS[$tarea]), 404, 'Tarea desconocida.');
        [$comando, $permitidas] = self::TAREAS[$tarea];

        $opciones = [];
        foreach ($permitidas as $clave => $valor) {
            if (is_string($clave)) {
                $opciones[$clave] = $valor; // fija
            } elseif ($request->has(ltrim($valor, '-'))) {
                $opciones[$valor] = $request->input(ltrim($valor, '-'));
            }
        }

        $inicio = microtime(true);
        $codigo = Artisan::call($comando, $opciones);

        return response()->json([
            'ok' => $codigo === 0,
            'tarea' => $tarea,
            'comando' => $comando,
            'salida' => trim(Artisan::output()),
            'segundos' => round(microtime(true) - $inicio, 3),
        ], $codigo === 0 ? 200 : 500);
    }

    public function tareas(): JsonResponse
    {
        return response()->json(['tareas' => array_keys(self::TAREAS)]);
    }
}
