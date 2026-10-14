<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_tour', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 80);
            $table->string('rta_amg', 100)->unique();
            $table->text('des')->nullable();
            $table->string('ico', 255)->nullable();
        });

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

        Schema::create('fotos_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->string('rta_arc', 255);
            $table->smallInteger('ord_vis')->default(0);
            $table->string('txt_atv', 150)->nullable();
        });

        Schema::create('puntos_ruta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->smallInteger('ord_sec');
            $table->string('nom', 120)->nullable();
            $table->decimal('lat', 10, 7);
            $table->decimal('lon', 10, 7);
            $table->smallInteger('alt_m')->nullable();
            $table->string('tip_pun', 15)->default('checkpoint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntos_ruta');
        Schema::dropIfExists('fotos_tour');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('categorias_tour');
    }
};