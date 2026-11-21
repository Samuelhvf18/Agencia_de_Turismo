<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// n8n ejecuta Investigación Operativa II.
// Ejemplos para programación local.
// Activar con schedule:work.
// Schedule::command('io:metricas-colas')->dailyAt('23:55');
// Schedule::command('io:recalibrar-markov --sincronizar')->monthlyOn(1, '02:00');
// Schedule::command('io:propuestas --tipo=guia --dias=14')->dailyAt('06:00');
// Schedule::command('io:alertas-eoq')->dailyAt('07:00');

// Expira reservas impagas tras 24h.
Schedule::command('reservas:expirar')->hourly();

// Recordatorio antes de la salida.
Schedule::command('reservas:recordatorios')->dailyAt('18:00');

// Respaldo diario, retención de 30d.
Schedule::command('respaldo:bd')->dailyAt('02:30');

// Recuperación de notificaciones persistentes.
Schedule::command('notificaciones:recuperar')->everyMinute()->withoutOverlapping();
