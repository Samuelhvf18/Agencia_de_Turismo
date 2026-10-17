<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categorías de Tour
        Schema::create('categorias_tour', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 80);
            $table->string('rta_amg', 100)->unique();
            $table->text('des')->nullable();
            $table->string('ico')->nullable();
        });

        // 2. Tours / Rutas
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cat_id')->constrained('categorias_tour')->restrictOnDelete();
            $table->string('cod', 20)->unique();
            $table->string('rta_amg', 180)->unique();
            $table->string('nom', 150);
            $table->text('des')->nullable();
            $table->string('niv_dif', 15);
            $table->smallInteger('alt_min_m')->nullable();
            $table->smallInteger('alt_max_m')->nullable();
            $table->decimal('dur_hrs', 5, 2);
            $table->text('req_fis')->nullable();
            $table->decimal('pre_bas', 10, 2);
            $table->smallInteger('cap_max');
            $table->jsonb('trz_geo')->nullable();
            $table->boolean('act')->default(true);
            $table->timestamp('cre_en')->useCurrent();
            $table->timestamp('mod_en')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('eli_en')->nullable();
        });

        // 3. Vehículos y Flota
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('pla', 15)->unique();
            $table->string('mar', 50)->nullable();
            $table->string('mdl', 50)->nullable();
            $table->string('tip', 15);
            $table->smallInteger('cap_asi');
            $table->string('tip_cmb', 20)->nullable();
            $table->string('est', 15)->default('act');
        });

        // 4. Salidas Programadas
        Schema::create('salidas_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->restrictOnDelete();
            $table->date('fec_sal');
            $table->time('hra_sal');
            $table->foreignId('veh_id')->nullable()->constrained('vehiculos')->nullOnDelete();
            $table->smallInteger('cap_max');
            $table->string('est', 15)->default('programada');
            $table->timestamp('hra_ini_rea')->nullable();
            $table->timestamp('hra_fin_rea')->nullable();
            $table->timestamp('cre_en')->useCurrent();
            $table->timestamp('mod_en')->useCurrent()->useCurrentOnUpdate();
        });

        // 5. Reservas
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

        // 6. Bitácora de Auditoría
        Schema::create('bitacora_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nom_tbl', 100);
            $table->unsignedBigInteger('id_reg');
            $table->string('acc', 10);
            $table->jsonb('val_ant')->nullable();
            $table->jsonb('val_nvo')->nullable();
            $table->string('dir_ip', 45)->nullable();
            $table->text('age_usr')->nullable();
            $table->timestamp('cre_en')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_auditoria');
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('salidas_tour');
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('categorias_tour');
    }
};
