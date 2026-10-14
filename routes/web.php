<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chatbot', function () {
    return view('chatbot.index');
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

    Route::redirect('/admin/dashboard', '/dashboard');

    Route::view('/admin/tours', 'admin.panels.tours')
        ->name('admin.tours');

    Route::view('/admin/operaciones', 'admin.panels.operaciones')
        ->name('admin.operaciones');

    Route::view('/admin/reservas', 'admin.panels.reservas')
        ->name('admin.reservas');

    Route::view('/admin/personal', 'admin.panels.personal')
        ->name('admin.personal');

    Route::view('/admin/transporte', 'admin.panels.transporte')
        ->name('admin.transporte');

    Route::view('/admin/inventario', 'admin.panels.inventario')
        ->name('admin.inventario');

    Route::view('/admin/finanzas', 'admin.panels.finanzas')
        ->name('admin.finanzas');

    Route::view('/admin/auditoria', 'admin.panels.auditoria')
        ->name('admin.auditoria');
});