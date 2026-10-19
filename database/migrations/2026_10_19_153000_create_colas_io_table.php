<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Módulo 07 — Investigación Operativa II
 * Teoría de Colas (modelo M/M/c/K) aplicada al motor de reservas.
 *
 * c = guías disponibles para el tour
 * K = capacidad del sistema (cupos por salida)
 * λ = tasa de llegada de reservas
 * μ = tasa de servicio (1 / duración promedio del tour en días)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Parámetros del modelo de colas por tour (configurables por el admin).
        Schema::create('cola_parametros_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->decimal('lmb', 8, 4)->comment('λ — tasa de llegada estimada (reservas/día)');
            $table->decimal('mu', 8, 4)->comment('μ — tasa de servicio (1 / días por tour)');
            $table->smallInteger('c_srv')->default(1)->comment('c — servidores (guías disponibles)');
            $table->smallInteger('k_cap')->comment('K — capacidad del sistema (cupos por salida)');
            $table->decimal('cos_esp', 8, 2)->nullable()->comment('Costo por hora de espera del cliente (Bs)');
            $table->decimal('cos_srv', 8, 2)->nullable()->comment('Costo por hora de servidor ocioso (Bs)');
            $table->date('fec_cal')->useCurrent();
            $table->text('obs')->nullable();
            $table->boolean('act')->default(true);
            $table->timestamp('cre_en')->useCurrent();
            $table->timestamp('mod_en')->useCurrent()->useCurrentOnUpdate();

            $table->index(['tur_id', 'act']);
        });

        // Log de eventos de llegada (reservas) para calcular λ observado y validar el modelo.
        Schema::create('cola_eventos_llegada', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('sal_tur_id')->nullable()->constrained('salidas_tour')->nullOnDelete();
            $table->foreignId('rsv_id')->nullable()->constrained('reservas')->nullOnDelete();
            $table->timestamp('fec_evt')->useCurrent();
            $table->smallInteger('cnt_sol')->default(1)->comment('Cupos solicitados en el evento');
            $table->string('est', 15)->default('admitido')->comment('admitido | rechazado | cancelado');
            $table->timestamp('cre_en')->useCurrent();

            $table->index(['tur_id', 'fec_evt']);
            $table->index('est');
        });

        // Snapshots diarios de las métricas calculadas (ρ, Lq, Wq, P0, Prechazo, etc.).
        Schema::create('cola_metricas_diarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->date('fec');
            $table->decimal('lmb_obs', 8, 4)->nullable()->comment('λ observado del día');
            $table->decimal('mu_obs', 8, 4)->nullable()->comment('μ observado');
            $table->decimal('rho', 6, 4)->nullable()->comment('ρ — utilización del sistema');
            $table->decimal('p0', 6, 4)->nullable()->comment('P0 — probabilidad de sistema vacío');
            $table->decimal('lq', 8, 4)->nullable()->comment('Lq — clientes promedio en cola');
            $table->decimal('l_sis', 8, 4)->nullable()->comment('L — clientes promedio en el sistema');
            $table->decimal('wq_hrs', 8, 4)->nullable()->comment('Wq — tiempo promedio en cola (hrs)');
            $table->decimal('w_hrs', 8, 4)->nullable()->comment('W — tiempo promedio en sistema (hrs)');
            $table->decimal('pq', 6, 4)->nullable()->comment('Probabilidad de tener que esperar');
            $table->decimal('p_rec', 6, 4)->nullable()->comment('Probabilidad de rechazo (sistema lleno)');
            $table->timestamp('cre_en')->useCurrent();

            $table->unique(['tur_id', 'fec']);
            $table->index('fec');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cola_metricas_diarias');
        Schema::dropIfExists('cola_eventos_llegada');
        Schema::dropIfExists('cola_parametros_tour');
    }
};
