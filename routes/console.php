<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Módulo 07 — Investigación Operativa II
Schedule::command('io:metricas-colas')->dailyAt('23:55');
Schedule::command('io:recalibrar-markov --sincronizar')->monthlyOn(1, '02:00');

// Módulo 05 — reservas pendientes sin pago tras 24 h liberan sus cupos.
Schedule::command('reservas:expirar')->hourly();
