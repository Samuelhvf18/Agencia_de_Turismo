<?php

use App\Http\Controllers\Api\N8nPlanificadorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Planificador n8n (Módulo 07 y tareas programadas): autenticado con N8N_TOKEN.
Route::prefix('n8n')->middleware(['n8n', 'throttle:60,1'])->group(function () {
    Route::get('/tareas', [N8nPlanificadorController::class, 'tareas']);
    Route::post('/tareas/{tarea}', [N8nPlanificadorController::class, 'ejecutar']);
});
