<?php

use App\Http\Controllers\Admin\AnalisisColasController;
use App\Http\Controllers\Admin\CicloVidaClienteController;
use App\Http\Controllers\Admin\FinanzasController;
use App\Http\Controllers\Admin\ReservasAdminController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\Pagos\PagoController;
use App\Http\Controllers\Reservas\ConsultaReservaController;
use App\Http\Controllers\Reservas\ReservaController;
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

/*
|--------------------------------------------------------------------------
| Motor de reservas (Módulo 05) y pagos (Módulo 06): públicos, admiten invitados
|--------------------------------------------------------------------------
*/

Route::get('/tours/{tour:rta_amg}/reservar', [ReservaController::class, 'salida'])->name('reservas.salida');
Route::post('/tours/{tour:rta_amg}/reservar', [ReservaController::class, 'guardarSalida'])->name('reservas.salida.store');

Route::prefix('reservas')->name('reservas.')->group(function () {
    Route::get('/participantes', [ReservaController::class, 'participantes'])->name('participantes');
    Route::post('/participantes', [ReservaController::class, 'guardarParticipantes'])->name('participantes.store');
    Route::get('/equipo', [ReservaController::class, 'equipo'])->name('equipo');
    Route::post('/equipo', [ReservaController::class, 'guardarEquipo'])->name('equipo.store');
    Route::get('/ficha-medica', [ReservaController::class, 'ficha'])->name('ficha');
    Route::post('/ficha-medica', [ReservaController::class, 'guardarFicha'])->name('ficha.store');
    Route::get('/resumen', [ReservaController::class, 'resumen'])->name('resumen');
    Route::post('/confirmar', [ReservaController::class, 'confirmar'])->name('confirmar');

    Route::get('/consultar', [ConsultaReservaController::class, 'consultar'])->name('consultar');
    Route::post('/consultar', [ConsultaReservaController::class, 'buscar'])->middleware('throttle:10,1')->name('consultar.buscar');

    Route::get('/{reserva}/confirmada', [ReservaController::class, 'confirmada'])->name('confirmada');
    Route::get('/{reserva}', [ConsultaReservaController::class, 'estado'])->name('estado');
    Route::post('/{reserva}/cancelar', [ConsultaReservaController::class, 'cancelar'])->name('cancelar');
    Route::get('/{reserva}/boletos.pdf', [ConsultaReservaController::class, 'boletosPdf'])->name('boletos');
    Route::get('/{reserva}/comprobante.pdf', [ConsultaReservaController::class, 'comprobantePdf'])->name('comprobante');
});

Route::get('/boletos/{boleto}.pdf', [ConsultaReservaController::class, 'boletoPdf'])->name('boletos.pdf');

Route::get('/reservas/{reserva}/pagar', [PagoController::class, 'create'])->name('pagos.create');
Route::post('/reservas/{reserva}/pagar', [PagoController::class, 'store'])->middleware('throttle:20,1')->name('pagos.store');

// Pasarela simulada (imita al proveedor externo) y su webhook firmado con HMAC.
Route::get('/pasarela/{ref}', [PagoController::class, 'pasarela'])->name('pasarela.show');
Route::post('/pasarela/{ref}', [PagoController::class, 'resolver'])->name('pasarela.resolver');
Route::post('/webhooks/pasarela', [PagoController::class, 'webhook'])->name('webhooks.pasarela');

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
        Route::get('/reservas', [ReservasAdminController::class, 'index'])->name('reservas');
        Route::get('/reservas/{reserva}', [ReservasAdminController::class, 'show'])->name('reservas.show');
        Route::post('/reservas/{reserva}/cancelar', [ReservasAdminController::class, 'cancelar'])->name('reservas.cancelar');

        Route::get('/finanzas', [FinanzasController::class, 'index'])->name('finanzas');
        Route::post('/finanzas/reservas/{reserva}/efectivo', [FinanzasController::class, 'efectivo'])->name('finanzas.efectivo');
        Route::get('/finanzas/comprobantes/{comprobante}.pdf', [FinanzasController::class, 'comprobantePdf'])->name('finanzas.comprobante');
        Route::post('/finanzas/comprobantes/{comprobante}/anular', [FinanzasController::class, 'anular'])
            ->middleware('rol:'.User::ROL_ADMIN)->name('finanzas.anular');
        Route::view('/analitica', 'admin.panels.analitica')->name('analitica');

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
