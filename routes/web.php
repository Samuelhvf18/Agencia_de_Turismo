<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourController;
use App\Http\Controllers\Admin\OperacionController;
use App\Http\Controllers\Admin\VehiculoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\FinanzaController;
use App\Http\Controllers\Admin\AnaliticaController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\Turista\TuristaController;
use App\Http\Controllers\ChoferViajeController;
use App\Http\Middleware\RoleMiddleware;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Chatbot con IA
Route::get('/chatbot', function () {
    return view('chatbot.index');
})->name('chatbot');

Route::post('/chatbot/mensaje', [
    ChatbotController::class,
    'enviar',
])->name('chatbot.mensaje');

// Verificación / Checkin de guías
Route::get('/guias/checkin', function () {
    return view('guias.checkin');
})->name('guias.checkin');

// Autenticación con Google
Route::get('/auth/google/redirect', [
    GoogleAuthController::class,
    'redirect',
])->name('auth.google.redirect');

Route::get('/auth/google/callback', [
    GoogleAuthController::class,
    'callback',
])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Rutas del Panel Administrativo y Roles (Autenticadas)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Redirección inteligente según el rol del usuario
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // 1. Si es admin
        if ($user->hasRole('admin') || ($user->rol ?? '') === 'admin' || optional($user->rol)->nom === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // 2. Si es guía
        if ($user->hasRole('guia') || ($user->rol ?? '') === 'guia' || optional($user->rol)->nom === 'guia') {
            return redirect()->route('guia.mis-tours');
        }

        // 3. Si es chofer
        if ($user->hasRole('chofer') || ($user->rol ?? '') === 'chofer' || optional($user->rol)->nom === 'chofer') {
            return redirect()->route('chofer.mis-viajes');
        }

        // 4. Si es turista
        if ($user->hasRole('turista') || ($user->rol ?? '') === 'turista' || optional($user->rol)->nom === 'turista') {
            return redirect()->route('turista.tours.index');
        }

        return redirect('/');
    })->name('dashboard');

    // Panel Principal de Administrador
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware(RoleMiddleware::class . ':admin')
        ->name('admin.dashboard');

    // Módulos del Administrador
    Route::prefix('admin')->name('admin.')->middleware(RoleMiddleware::class . ':admin')->group(function () {

        Route::patch('/tours/{tour}/toggle', [TourController::class, 'toggleStatus'])->name('tours.toggle');
        Route::resource('tours', TourController::class);

        Route::get('/operaciones', [OperacionController::class, 'index'])->name('operaciones');
        Route::post('/operaciones', [OperacionController::class, 'store'])->name('operaciones.store');
        Route::put('/operaciones/{id}', [OperacionController::class, 'update'])->name('operaciones.update');
        Route::delete('/operaciones/{id}', [OperacionController::class, 'destroy'])->name('operaciones.destroy');

        Route::get('/transporte', [VehiculoController::class, 'index'])->name('transporte');
        Route::post('/transporte', [VehiculoController::class, 'store'])->name('transporte.store');
        Route::put('/transporte/{id}', [VehiculoController::class, 'update'])->name('transporte.update');

        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
        Route::post('/inventario', [InventarioController::class, 'store'])->name('inventario.store');
        Route::put('/inventario/{id}', [InventarioController::class, 'update'])->name('inventario.update');

        Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas');
        Route::get('/reservas/create-data', [ReservaController::class, 'getCreateData'])->name('reservas.create-data');
        Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
        Route::get('/reservas/{id}', [ReservaController::class, 'show'])->name('reservas.show');
        Route::put('/reservas/{id}', [ReservaController::class, 'update'])->name('reservas.update');
        Route::delete('/reservas/{id}', [ReservaController::class, 'destroy'])->name('reservas.destroy');

        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('seguridad');
        Route::post('/usuarios', [AuditoriaController::class, 'storeUsuario'])->name('usuarios.store');
        Route::put('/usuarios/{id}', [AuditoriaController::class, 'updateUsuario'])->name('usuarios.update');
        Route::delete('/usuarios/{id}', [AuditoriaController::class, 'destroyUsuario'])->name('usuarios.destroy');

        Route::view('/personal', 'admin.panels.personal')->name('personal');

        Route::get('/finanzas', [FinanzaController::class, 'index'])->name('finanzas');
        Route::get('/finanzas/exportar', [FinanzaController::class, 'exportarExcel'])->name('finanzas.exportar');

        Route::get('/analitica', [AnaliticaController::class, 'index'])->name('analitica');
        Route::get('/analitica/exportar', [AnaliticaController::class, 'exportarExcel'])->name('analitica.exportar');
    });

    // Portal Turista
    Route::prefix('turista')->name('turista.')
        ->middleware(RoleMiddleware::class . ':turista,admin')
        ->group(function () {
            Route::get('/dashboard', [TuristaController::class, 'dashboard'])->name('dashboard');
            Route::get('/tours', [TuristaController::class, 'tours'])->name('tours.index');
            Route::get('/tours/{tour}', [TuristaController::class, 'showTour'])->name('tours.show');
            Route::get('/reservas', [TuristaController::class, 'reservas'])->name('reservas.index');
            Route::post('/reservas', [TuristaController::class, 'solicitarReserva'])->name('reservas.solicitar');
            Route::get('/perfil', [TuristaController::class, 'perfil'])->name('perfil');
            Route::put('/perfil', [TuristaController::class, 'actualizarPerfil'])->name('perfil.update');
        });
});

/*
|--------------------------------------------------------------------------
| Rutas del Portal Operativo (Guías y Choferes)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', RoleMiddleware::class . ':guia,admin'])->prefix('guia')->name('guia.')->group(function () {
    Route::get('/tours', function () {
        return view('guia.tours');
    })->name('mis-tours');
});

Route::middleware(['auth', RoleMiddleware::class . ':chofer,admin'])->prefix('chofer')->name('chofer.')->group(function () {
    Route::get('/viajes', [ChoferViajeController::class, 'index'])->name('mis-viajes');
});
