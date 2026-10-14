<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50)->unique();
            $table->string('des', 255)->nullable();
        });

        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->char('cod_iso', 2)->unique();
        });

        Schema::create('idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 50);
            $table->char('cod_iso', 2)->unique();
        });

        Schema::create('departamentos_bolivia', function (Blueprint $table) {
            $table->id();
            $table->char('cod', 2)->unique();
            $table->string('nom', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departamentos_bolivia');
        Schema::dropIfExists('idiomas');
        Schema::dropIfExists('paises');
        Schema::dropIfExists('roles');
    }
};