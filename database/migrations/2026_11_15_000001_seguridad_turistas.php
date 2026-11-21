<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admite esquemas anteriores sin idioma.
        if (! Schema::hasColumn('reservas', 'idioma')) {
            Schema::table('reservas', fn (Blueprint $t) => $t->string('idioma', 2)->default('es'));
        }
        Schema::table('tours', function (Blueprint $t) {
            $t->boolean('alto_riesgo')->default(false);
            $t->unsignedSmallInteger('edad_minima')->default(0);
            $t->boolean('admite_menores')->default(true);
        });
        Schema::table('participantes_reserva', function (Blueprint $t) {
            // Conserva la edad declarada.
            $t->date('fecha_nacimiento')->nullable();
            $t->foreignId('acompanante_id')->nullable()->constrained('participantes_reserva')->restrictOnDelete();
            $t->json('autorizacion_menor')->nullable();
        });
        Schema::table('users', fn (Blueprint $t) => $t->string('google_id')->nullable()->unique());
        Schema::create('codigos_correo', function (Blueprint $t) {
            $t->id();
            $t->string('correo', 150);
            $t->string('proposito', 20);
            $t->string('hash');
            $t->timestamp('vence_en');
            $t->unsignedSmallInteger('intentos')->default(0);
            $t->timestamp('creado_en');
            $t->unique(['correo', 'proposito']);
        });
        Schema::create('suscripciones_novedades', function (Blueprint $t) {
            $t->id();
            $t->string('correo', 150)->unique();
            $t->timestamp('consentimiento_en');
            $t->string('version_politica', 30);
            $t->timestamp('confirmada_en')->nullable();
            $t->timestamp('baja_en')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscripciones_novedades');
        Schema::dropIfExists('codigos_correo');
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('google_id'));
        Schema::table('participantes_reserva', function (Blueprint $t) {
            $t->dropConstrainedForeignId('acompanante_id');
            $t->dropColumn(['fecha_nacimiento', 'autorizacion_menor']);
        });
        Schema::table('tours', fn (Blueprint $t) => $t->dropColumn(['alto_riesgo', 'edad_minima', 'admite_menores']));
    }
};
