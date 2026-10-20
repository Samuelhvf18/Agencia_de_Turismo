<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Módulo 07 — Investigación Operativa II: los cálculos los programa n8n (decisión del equipo)
// llamando a POST /api/n8n/tareas/{tarea}; ver docs/n8n/planificador-io.json. Para correrlos
// sin n8n basta descomentar estas líneas y usar `php artisan schedule:work`.
// Schedule::command('io:metricas-colas')->dailyAt('23:55');
// Schedule::command('io:recalibrar-markov --sincronizar')->monthlyOn(1, '02:00');
// Schedule::command('io:propuestas --tipo=guia --dias=14')->dailyAt('06:00');
// Schedule::command('io:alertas-eoq')->dailyAt('07:00');

// Módulo 05 — reservas pendientes sin pago tras 24 h liberan sus cupos.
Schedule::command('reservas:expirar')->hourly();

// Módulo 10 — recordatorio el día anterior a la salida (n8n también puede dispararlo; no se duplica).
Schedule::command('reservas:recordatorios')->dailyAt('18:00');

// RNF-12 — respaldo diario de la base de datos (30 días de retención).
Schedule::command('respaldo:bd')->dailyAt('02:30');
