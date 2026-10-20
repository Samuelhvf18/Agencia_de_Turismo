<?php

use App\Http\Controllers\Admin\AnalisisColasController;
use App\Http\Controllers\Admin\CicloVidaClienteController;
use App\Http\Controllers\CatalogoController;
use App\Models\User;
use App\Services\Io\IoDemoData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

Route::get('/', [CatalogoController::class, 'index'])->name('home');
Route::get('/tours/{tour:rta_amg}', [CatalogoController::class, 'show'])->name('tours.show');

// Asistente turístico: es público (turistas e invitados, RF-28). Hoy es maqueta; la fase 6 lo conecta a n8n.
Route::view('/chatbot', 'chatbot.index')->name('chatbot');

/*
|--------------------------------------------------------------------------
| Rutas autenticadas, protegidas por rol (middleware `rol:`)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Inicio genérico: cada rol va a su propia pantalla.
    Route::get('/dashboard', fn (Request $request) => redirect($request->user()->rutaInicio()))->name('dashboard');

    // Panel administrativo: administrador y personal de operaciones.
    Route::prefix('admin')->name('admin.')->middleware('rol:'.User::ROL_ADMIN.','.User::ROL_OPERACIONES)->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        Route::view('/operaciones', 'admin.panels.operaciones')->name('operaciones');
        Route::view('/inventario', 'admin.panels.inventario')->name('inventario');
        Route::view('/transporte', 'admin.panels.transporte')->name('transporte');
        Route::view('/tours', 'admin.panels.tours')->name('tours');
        Route::view('/personal', 'admin.panels.personal')->name('personal');
        Route::view('/reservas', 'admin.panels.reservas')->name('reservas');
        Route::view('/finanzas', 'admin.panels.finanzas')->name('finanzas');

        // Módulo 07 — Investigación Operativa II: paneles de teoría de colas y cadenas de Markov
        Route::get('/io/colas', [AnalisisColasController::class, 'index'])->name('io.colas');
        Route::get('/io/ciclo-vida', [CicloVidaClienteController::class, 'index'])->name('io.ciclo-vida');

        // Bitácora de auditoría: solo administrador.
        Route::view('/auditoria', 'admin.panels.auditoria')->middleware('rol:'.User::ROL_ADMIN)->name('auditoria');
    });

    // Operación en campo: guías y choferes (y supervisión de operaciones/admin).
    Route::middleware('rol:'.User::ROL_GUIA.','.User::ROL_CHOFER.','.User::ROL_OPERACIONES.','.User::ROL_ADMIN)->group(function () {
        Route::view('/guias/checkin', 'guias.checkin')->name('guias.checkin');
    });
});

/*
|--------------------------------------------------------------------------
| Maquetas de frontend (solo en entorno local)
|--------------------------------------------------------------------------
| Vistas con datos de ejemplo para revisar el diseño mientras se construye el
| backend. No se registran fuera de APP_ENV=local.
*/

if (app()->environment('local')) {
    // Motor de Reservas (Módulo 05). Aceptan GET y POST para recorrer el flujo sin backend.
    Route::prefix('reservas/preview')->name('reservas.preview.')->group(function () {
        foreach (['salida', 'participantes', 'equipo', 'ficha', 'resumen', 'confirmada', 'consultar', 'estado'] as $vista) {
            Route::match(['get', 'post'], $vista, fn () => view("reservas.$vista"))->name($vista);
        }
    });

    // Paneles de IO II (Módulo 07) con datos de maqueta, sin iniciar sesión.
    Route::prefix('admin/io/preview')->name('admin.io.preview.')->group(function () {
        Route::get('colas', fn (Request $r) => view('admin.io.colas', app(IoDemoData::class)->colas(
            $r->integer('tour') ?: null, $r->filled('c') ? $r->integer('c') : null, $r->filled('k') ? $r->integer('k') : null,
            $r->filled('lambda') ? (float) $r->input('lambda') : null) + ['formAction' => url('admin/io/preview/colas')]))->name('colas');
        Route::get('ciclo-vida', fn (Request $r) => view('admin.io.ciclo-vida', app(IoDemoData::class)->cicloVida(
            $r->input('inicio')) + ['formAction' => url('admin/io/preview/ciclo-vida')]))->name('ciclo-vida');
    });
}
