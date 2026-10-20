<?php

namespace Database\Seeders;

use App\Models\Idioma;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Una cuenta de prueba por rol (guía, chofer, operaciones y turista) para
 * probar los accesos en desarrollo. No se ejecuta en producción.
 * Contraseña de todas: Pipocas2026
 */
class UsuariosDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $roles = Role::pluck('id', 'nom');
        $crear = fn (string $rol, string $nom, string $cor) => User::updateOrCreate(
            ['cor' => $cor],
            ['rol_id' => $roles[$rol], 'nom' => $nom, 'clv' => 'Pipocas2026', 'ci_cmp' => '', 'act' => true]
        );

        $crear(User::ROL_OPERACIONES, 'Operaciones Demo', 'operaciones@pipocas.test');
        $crear(User::ROL_TURISTA, 'Turista Demo', 'turista@pipocas.test');

        $guia = $crear(User::ROL_GUIA, 'Guía Demo', 'guia@pipocas.test');
        $perfil = $guia->perfilGuia()->updateOrCreate([], [
            'rta_amg' => 'guia-demo', 'ani_exp' => 5, 'lim_hrs_sem' => 40, 'act' => true,
            'bio' => 'Guía de alta montaña certificado.',
        ]);
        $perfil->idiomas()->syncWithoutDetaching(Idioma::whereIn('cod_iso', ['es', 'en'])->pluck('id'));

        $chofer = $crear(User::ROL_CHOFER, 'Chofer Demo', 'chofer@pipocas.test');
        $chofer->perfilChofer()->updateOrCreate([], [
            'num_lic' => 'LP-1234567', 'tip_lic' => 'C', 'fec_ven_lic' => now()->addYears(2)->toDateString(), 'act' => true,
        ]);
    }
}
