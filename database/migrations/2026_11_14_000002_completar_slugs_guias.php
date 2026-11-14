<?php

use App\Services\Catalogo\SlugService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('perfiles_guia as g')->join('users as u', 'u.id', '=', 'g.usr_id')->whereNull('g.rta_amg')->get(['g.id', 'u.nom']) as $guia) {
            DB::table('perfiles_guia')->where('id', $guia->id)->update([
                'rta_amg' => SlugService::unico($guia->nom, 'perfiles_guia', $guia->id, 130),
            ]);
        }
    }

    public function down(): void
    {
        // Se preservan las URL ya publicadas.
    }
};
