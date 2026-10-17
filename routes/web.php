<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
