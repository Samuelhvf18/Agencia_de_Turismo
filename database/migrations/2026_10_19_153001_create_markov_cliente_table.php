<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Módulo 07 — Investigación Operativa II (complemento)
 * Cadenas de Markov aplicadas al ciclo de vida del cliente.
 *
 * Estados: prospecto → reservado → completado → fidelizado / perdido
 * Matriz P: probabilidades de transición entre estados por período.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Catálogo de estados del cliente dentro de la cadena.
        Schema::create('markov_estados_cliente', function (Blueprint $table) {
            $table->id();
            $table->string('cod', 20)->unique()->comment('prospecto | reservado | completado | fidelizado | perdido');
            $table->string('nom', 60);
            $table->text('des')->nullable();
            $table->boolean('es_abs')->default(false)->comment('Estado absorbente (fidelizado, perdido)');
            $table->smallInteger('ord_vis')->default(0);
            $table->string('col_hex', 7)->nullable()->comment('Color para visualización');
            $table->timestamp('cre_en')->useCurrent();
        });

        // Matriz de transición P[i,j] por período.
        Schema::create('markov_transiciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('est_ori_id')->constrained('markov_estados_cliente')->cascadeOnDelete();
            $table->foreignId('est_des_id')->constrained('markov_estados_cliente')->cascadeOnDelete();
            $table->decimal('prb', 6, 4)->comment('Probabilidad de transición [0.0000 — 1.0000]');
            $table->string('per', 15)->default('mensual')->comment('mensual | trimestral');
            $table->date('fec_cal')->useCurrent();
            $table->boolean('act')->default(true);
            $table->timestamp('cre_en')->useCurrent();

            $table->unique(['est_ori_id', 'est_des_id', 'per', 'fec_cal'], 'markov_trans_unica');
            $table->index(['per', 'act']);
        });

        // Historial de estado por cliente (una fila por cambio de estado).
        Schema::create('markov_cliente_estado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usr_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('est_id')->constrained('markov_estados_cliente')->restrictOnDelete();
            $table->foreignId('est_ant_id')->nullable()->constrained('markov_estados_cliente')->nullOnDelete();
            $table->date('fec_cmb')->useCurrent();
            $table->text('obs')->nullable();
            $table->timestamp('cre_en')->useCurrent();

            $table->index(['usr_id', 'fec_cmb']);
            $table->index('est_id');
        });

        // Cohorts: grupos de clientes captados en un mismo período para análisis de retención.
        Schema::create('markov_cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 80);
            $table->date('per_cap')->comment('Período de captación (primer día del mes)');
            $table->integer('tot_cli')->default(0);
            $table->text('obs')->nullable();
            $table->timestamp('cre_en')->useCurrent();
            $table->timestamp('mod_en')->useCurrent()->useCurrentOnUpdate();

            $table->unique('per_cap');
        });

        // Distribución de un cohort en cada período (período relativo desde captación).
        Schema::create('markov_cohort_distribucion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coh_id')->constrained('markov_cohorts')->cascadeOnDelete();
            $table->smallInteger('per_rel')->comment('Período relativo (0 = captación, 1 = mes siguiente, …)');
            $table->foreignId('est_id')->constrained('markov_estados_cliente')->restrictOnDelete();
            $table->integer('cnt')->default(0);
            $table->timestamp('cre_en')->useCurrent();

            $table->unique(['coh_id', 'per_rel', 'est_id'], 'markov_coh_dist_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('markov_cohort_distribucion');
        Schema::dropIfExists('markov_cohorts');
        Schema::dropIfExists('markov_cliente_estado');
        Schema::dropIfExists('markov_transiciones');
        Schema::dropIfExists('markov_estados_cliente');
    }
};
