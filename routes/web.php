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
use App\Http\Controllers\ChoferViajeController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chatbot', function () {
    return view('chatbot.index');
});

Route::get('/guias/checkin', function () {
    return view('guias.checkin');
});

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
| Rutas del Panel Administrativo (Autenticadas)
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->hasRole('guia')) {
            return redirect()->route('guia.mis-tours');
        }

        if ($user->hasRole('chofer')) {
            return redirect()->route('chofer.mis-viajes');
        }

        return app(DashboardController::class)->index();
    })->name('dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {

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

});

/*
|--------------------------------------------------------------------------
| Rutas del Portal Operativo (Guías y Choferes)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class.':guia'])->prefix('guia')->name('guia.')->group(function () {
    Route::get('/tours', function () {
        return view('guia.tours');
    })->name('mis-tours');
});

Route::middleware(['auth', \Spatie\Permission\Middleware\RoleMiddleware::class.':chofer'])->prefix('chofer')->name('chofer.')->group(function () {
    Route::get('/viajes', [ChoferViajeController::class, 'index'])->name('mis-viajes');
});