<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE UNIQUE INDEX cola_parametro_unico_activo ON cola_parametros_tour (tur_id) WHERE act = true');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS cola_parametro_unico_activo');
    }
};
