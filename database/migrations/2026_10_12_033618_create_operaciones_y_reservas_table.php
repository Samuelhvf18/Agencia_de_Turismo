<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salidas_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->restrictOnDelete();
            $table->date('fec_sal');
            $table->time('hra_sal');
            $table->foreignId('prf_gui_id')->nullable()->constrained('perfiles_guia')->nullOnDelete();
            $table->foreignId('prf_cho_id')->nullable()->constrained('perfiles_chofer')->nullOnDelete();
            $table->foreignId('veh_id')->nullable()->constrained('vehiculos')->nullOnDelete();
            $table->smallInteger('cap_max');
            $table->string('est', 15)->default('programada');
            $table->timestamp('hra_ini_rea')->nullable();
            $table->timestamp('hra_fin_rea')->nullable();
            $table->timestamp('cre_en')->useCurrent();
            $table->timestamp('mod_en')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('bitacora_viajes_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('veh_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->foreignId('sal_tur_id')->constrained('salidas_tour')->cascadeOnDelete();
            $table->decimal('dst_km', 6, 2)->nullable();
            $table->decimal('cos_cmb', 8, 2)->nullable();
            $table->timestamp('reg_en')->useCurrent();
        });

        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('cod', 20)->unique();
            $table->foreignId('sal_tur_id')->constrained('salidas_tour')->restrictOnDelete();
            $table->foreignId('usr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom_ctc', 150);
            $table->string('cor_ctc', 150);
            $table->string('tel_ctc', 20);
            $table->smallInteger('cnt_ptc');
            $table->string('est', 15)->default('pendiente');
            $table->decimal('pre_tot', 10, 2);
            $table->string('est_pag', 15)->default('pendiente');
            $table->timestamp('cre_en')->useCurrent();
            $table->timestamp('mod_en')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('participantes_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rsv_id')->constrained('reservas')->cascadeOnDelete();
            $table->string('nom_cpl', 150);
            $table->string('tip_doc', 20)->nullable();
            $table->string('num_doc', 15)->nullable();
            $table->string('ci_cmp', 5)->default('');
            $table->foreignId('ci_dep_id')->nullable()->constrained('departamentos_bolivia');
            $table->smallInteger('eda')->nullable();
            $table->foreignId('pai_id')->nullable()->constrained('paises')->nullOnDelete();
            $table->boolean('es_ctc_pri')->default(false);
        });

        Schema::create('alquileres_equipo_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ptc_rsv_id')->constrained('participantes_reserva')->cascadeOnDelete();
            $table->foreignId('itm_eqp_id')->constrained('items_equipo')->restrictOnDelete();
            $table->decimal('pre', 8, 2);
        });

        Schema::create('boletos', function (Blueprint $table) {
            $table->id();
            $table->string('cod', 64)->unique();
            $table->foreignId('ptc_rsv_id')->unique()->constrained('participantes_reserva')->cascadeOnDelete();
            $table->string('est', 15)->default('emitido');
            $table->timestamp('emt_en')->useCurrent();
            $table->timestamp('vld_en')->nullable();
            $table->foreignId('vld_por')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('fichas_medicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ptc_rsv_id')->unique()->constrained('participantes_reserva')->cascadeOnDelete();
            $table->text('con_med')->nullable();
            $table->string('nom_ctc_eme', 150)->nullable();
            $table->string('tel_ctc_eme', 20)->nullable();
            $table->string('rta_fir', 255);
            $table->timestamp('fir_en');
            $table->string('dir_ip', 45)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichas_medicas');
        Schema::dropIfExists('boletos');
        Schema::dropIfExists('alquileres_equipo_reserva');
        Schema::dropIfExists('participantes_reserva');
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('bitacora_viajes_vehiculo');
        Schema::dropIfExists('salidas_tour');
    }
};