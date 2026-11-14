<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registros_notificacion', function (Blueprint $table) {
            $table->string('clave_evento', 150)->nullable()->unique();
            $table->unsignedSmallInteger('intentos')->default(0);
            $table->timestamp('disponible_en')->nullable()->index();
            $table->timestamp('bloqueado_hasta')->nullable();
            $table->string('token_entrega', 64)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('registros_notificacion', fn (Blueprint $t) => $t->dropColumn(['clave_evento', 'intentos', 'disponible_en', 'bloqueado_hasta', 'token_entrega']));
    }
};
