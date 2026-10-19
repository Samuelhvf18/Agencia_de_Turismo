<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogoController;

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
