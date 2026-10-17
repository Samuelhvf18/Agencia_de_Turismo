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
