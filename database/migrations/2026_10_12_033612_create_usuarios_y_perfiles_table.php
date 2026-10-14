<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rol_id')->nullable()->constrained('roles')->restrictOnDelete();
            $table->foreignId('pai_id')->nullable()->constrained('paises')->nullOnDelete();
            $table->string('nom', 150);
            $table->string('cor', 150)->unique();
            $table->timestamp('cor_vrf_en')->nullable();
            $table->string('clv');
            $table->string('rol', 30)->default('turista');
            $table->string('tel', 20)->nullable();
            $table->string('tip_doc', 20)->nullable();
            $table->string('num_doc', 15)->nullable();
            $table->string('ci_cmp', 5)->default('');
            $table->foreignId('ci_dep_id')->nullable()->constrained('departamentos_bolivia');
            $table->string('fot_prf', 255)->nullable();
            $table->string('tok_rec', 100)->nullable();
            $table->boolean('act')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('eli_en')->nullable();

            $table->unique(['num_doc', 'ci_cmp']);
        });

        Schema::create('perfiles_guia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usr_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('rta_amg', 150)->unique()->nullable();
            $table->smallInteger('ani_exp')->default(0);
            $table->smallInteger('lim_hrs_sem')->default(40);
            $table->text('bio')->nullable();
            $table->boolean('act')->default(true);
        });

        Schema::create('idiomas_guia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prf_gui_id')->constrained('perfiles_guia')->cascadeOnDelete();
            $table->foreignId('idi_id')->constrained('idiomas')->restrictOnDelete();
            $table->unique(['prf_gui_id', 'idi_id']);
        });

        Schema::create('perfiles_chofer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usr_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('num_lic', 30);
            $table->string('tip_lic', 20);
            $table->date('fec_ven_lic');
            $table->boolean('act')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles_chofer');
        Schema::dropIfExists('idiomas_guia');
        Schema::dropIfExists('perfiles_guia');
        Schema::dropIfExists('users');
    }
};