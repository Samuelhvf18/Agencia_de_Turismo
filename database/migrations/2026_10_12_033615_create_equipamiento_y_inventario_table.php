<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_equipo', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 80);
            $table->text('des')->nullable();
            $table->decimal('pre_alq_bas', 8, 2)->default(0);
            $table->decimal('cos_ord', 10, 2)->nullable();
            $table->decimal('cos_mnt_anu', 10, 2)->nullable();
            $table->boolean('eqp_cri')->default(false);
        });

        Schema::create('items_equipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tip_eqp_id')->constrained('tipos_equipo')->cascadeOnDelete();
            $table->string('tal', 20);
            $table->integer('cnt_tot')->default(0);
            $table->integer('cnt_dis')->default(0);
            $table->string('con', 20)->default('bueno');
            $table->unique(['tip_eqp_id', 'tal']);
        });

        Schema::create('equipo_por_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tur_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('tip_eqp_id')->constrained('tipos_equipo')->restrictOnDelete();
            $table->decimal('pre_alq_esp', 8, 2)->nullable();
            $table->boolean('act')->default(true);
            $table->unique(['tur_id', 'tip_eqp_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_por_tour');
        Schema::dropIfExists('items_equipo');
        Schema::dropIfExists('tipos_equipo');
    }
};
