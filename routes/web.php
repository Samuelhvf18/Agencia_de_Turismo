<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\Admin\AnalisisColasController;
use App\Http\Controllers\Admin\CicloVidaClienteController;
use App\Services\Io\IoDemoData;

Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::get('/tours/{tour:rta_amg}', [CatalogoController::class, 'show'])->name('tours.show');

Route::get('/chatbot', function () {
    return view('chatbot.index');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
});

Route::get('/guias/checkin', function () {
    return view('guias.checkin');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Módulo 07 — Investigación Operativa II: paneles de teoría de colas y cadenas de Markov
    Route::get('/admin/io/colas', [AnalisisColasController::class, 'index'])->name('admin.io.colas');
    Route::get('/admin/io/ciclo-vida', [CicloVidaClienteController::class, 'index'])->name('admin.io.ciclo-vida');
});

// PREVIEW FRONTEND — reemplazar por rutas del controlador
// Vistas del Motor de Reservas (Módulo 05) con datos de ejemplo. Aceptan GET y POST
// para poder recorrer el flujo enviando los formularios sin backend.
Route::prefix('reservas/preview')->name('reservas.preview.')->group(function () {
    foreach (['salida', 'participantes', 'equipo', 'ficha', 'resumen', 'confirmada', 'consultar', 'estado'] as $vista) {
        Route::match(['get', 'post'], $vista, fn () => view("reservas.$vista"))->name($vista);
    }
});
// FIN PREVIEW FRONTEND

// PREVIEW FRONTEND — Paneles de IO II (Módulo 07) con datos de maqueta, sin iniciar sesión.
Route::prefix('admin/io/preview')->name('admin.io.preview.')->group(function () {
    Route::get('colas', fn (\Illuminate\Http\Request $r) => view('admin.io.colas', app(IoDemoData::class)->colas(
        $r->integer('tour') ?: null, $r->filled('c') ? $r->integer('c') : null, $r->filled('k') ? $r->integer('k') : null,
        $r->filled('lambda') ? (float) $r->input('lambda') : null) + ['formAction' => url('admin/io/preview/colas')]))->name('colas');
    Route::get('ciclo-vida', fn (\Illuminate\Http\Request $r) => view('admin.io.ciclo-vida', app(IoDemoData::class)->cicloVida(
        $r->input('inicio')) + ['formAction' => url('admin/io/preview/ciclo-vida')]))->name('ciclo-vida');
});
// FIN PREVIEW FRONTEND
