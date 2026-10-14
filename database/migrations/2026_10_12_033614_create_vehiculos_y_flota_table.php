<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};