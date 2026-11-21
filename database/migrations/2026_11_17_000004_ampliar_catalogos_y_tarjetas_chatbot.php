<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mensajes_chatbot', function (Blueprint $table) {
            $table->json('tours_ids')->nullable();
        });
        DB::table('tours')->where('nom', 'Valle de la Luna & Teleféricos')->update(['nom' => 'Valle de la Luna y Teleféricos']);
        DB::table('tours')->where('nom', 'Tiwanaku & Puma Punku')->update(['nom' => 'Tiwanaku y Puma Punku']);
        foreach (['paises' => 'paises', 'idiomas' => 'idiomas'] as $tabla => $catalogo) {
            foreach (config('catalogos.'.$catalogo) as $iso => $nombre) {
                DB::table($tabla)->insertOrIgnore(['cod_iso' => $iso, 'nom' => $nombre]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('mensajes_chatbot', fn (Blueprint $table) => $table->dropColumn('tours_ids'));
        // Conserva catálogos referenciados.
    }
};
