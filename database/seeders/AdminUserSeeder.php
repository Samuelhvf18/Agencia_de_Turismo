<?php
namespace Database\Seeders;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin = Role::where('nom', 'admin')->firstOrFail();
        $correo = env('PIPOCAS_ADMIN_EMAIL');
        $usuario = env('PIPOCAS_ADMIN_USERNAME');
        $password = env('PIPOCAS_ADMIN_PASSWORD');
        if (!$correo || !$usuario || !$password) {
            throw new \RuntimeException(
                'Configura las credenciales del administrador en el archivo .env.'
            );
        }
        User::updateOrCreate(
            ['cor' => $correo],
            [
                'rol_id' => $rolAdmin->id,
                'nom' => 'Administrador Pipocas',
                'pat' => 'Sistema',
                'mat' => null,
                'usr' => $usuario,
                'clv' => Hash::make($password),
                'ci_cmp' => '',
            ]
        );
    }
}
