<?php

use Illuminate\Support\Facades\Route;

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

    // Dashboard Principal (Con ambos nombres 'dashboard' y 'admin.dashboard' para evitar errores)
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // 🔴 Módulos Desarrollador 1 (Logística & Operaciones)
    Route::view('/admin/operaciones', 'admin.panels.operaciones')->name('admin.operaciones');
    Route::view('/admin/inventario', 'admin.panels.inventario')->name('admin.inventario');
    Route::view('/admin/transporte', 'admin.panels.transporte')->name('admin.transporte');
    Route::view('/admin/tours', 'admin.panels.tours')->name('admin.tours');
    Route::view('/admin/personal', 'admin.panels.personal')->name('admin.personal');

    // 🔵 Módulos Desarrollador 2 (Comercial, BI & Seguridad)
    Route::view('/admin/reservas', 'admin.panels.reservas')->name('admin.reservas');
    Route::view('/admin/finanzas', 'admin.panels.finanzas')->name('admin.finanzas');
    Route::view('/admin/analitica', 'admin.panels.analitica')->name('admin.analitica');
    Route::view('/admin/auditoria', 'admin.panels.auditoria')->name('admin.seguridad');

});