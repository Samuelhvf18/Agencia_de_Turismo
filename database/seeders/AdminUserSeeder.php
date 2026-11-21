<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Inicializa la cuenta administrativa. */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin = Role::where('nom', User::ROL_ADMIN)->firstOrFail();

        $correo = env('PIPOCAS_ADMIN_EMAIL');
        $password = env('PIPOCAS_ADMIN_PASSWORD');

        if (! $correo || ! $password) {
            if (app()->isProduction()) {
                throw new \RuntimeException('Configura PIPOCAS_ADMIN_EMAIL y PIPOCAS_ADMIN_PASSWORD en el archivo .env.');
            }
            $correo ??= 'admin@pipocas.test';
            $password ??= 'Pipocas2026';
        }

        $usuario = User::updateOrCreate(
            ['cor' => strtolower($correo)],
            [
                'rol_id' => $rolAdmin->id,
                'nom' => 'Administrador Andinist Bolivia',
                'clv' => $password,
                'ci_cmp' => '',
                'act' => true,
            ]
        );
        if (app()->environment(['local', 'testing'])) {
            $usuario->forceFill(['cor_vrf_en' => now()])->save();
        }
    }
}
