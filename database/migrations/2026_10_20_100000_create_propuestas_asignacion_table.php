<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Módulo 07 — IO II, RF-24: propuestas de asignación generadas por los
 * modelos (método húngaro para guías, costo de combustible para vehículos).
 * Nunca se aplican solas: el personal las revisa, ajusta, confirma o rechaza.
 * Tabla nueva respecto al esquema v5 (no tenía dónde guardar las propuestas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propuestas_asignacion', function (Blueprint $table) {
            $table->id();
            $table->uuid('lote')->comment('Corrida del modelo que generó la propuesta');
            $table->string('tip', 10)->comment('guia | vehiculo');
            $table->foreignId('sal_tur_id')->constrained('salidas_tour')->cascadeOnDelete();
            $table->unsignedBigInteger('rec_act_id')->nullable()->comment('Recurso asignado al generar (prf_gui_id o veh_id)');
            $table->unsignedBigInteger('rec_pro_id')->nullable()->comment('Recurso propuesto por el modelo');
            $table->decimal('cos', 12, 2)->nullable()->comment('Costo de la celda elegida');
            $table->jsonb('det')->nullable()->comment('Desglose del costo y motivos');
            $table->string('est', 12)->default('pendiente')->comment('pendiente | aplicada | ajustada | rechazada');
            $table->foreignId('rev_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rev_en')->nullable();
            $table->timestamp('gen_en')->useCurrent();

            $table->index(['tip', 'est']);
            $table->index('lote');
        });

        DB::statement("ALTER TABLE propuestas_asignacion ADD CONSTRAINT chk_pro_asg_tip CHECK (tip IN ('guia', 'vehiculo'))");
        DB::statement("ALTER TABLE propuestas_asignacion ADD CONSTRAINT chk_pro_asg_est CHECK (est IN ('pendiente', 'aplicada', 'ajustada', 'rechazada'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('propuestas_asignacion');
    }
};
