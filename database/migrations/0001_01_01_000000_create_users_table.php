<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tablas de framework para la recuperación de contraseñas.
 *
 * La tabla `users` (diccionario v4) se crea en
 * 2026_10_12_033612_create_usuarios_y_perfiles_table y `sessions` en
 * 2026_10_12_040000_create_sessions_table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
    }
};
