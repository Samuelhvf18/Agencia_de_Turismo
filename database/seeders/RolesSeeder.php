<?php
namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;
class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'turista' => 'Cliente o turista que realiza reservas de tours',
            'guia' => 'Guia de turismo',
            'chofer' => 'Chofer de la agencia',
            'operaciones' => 'Personal encargado de operaciones',
            'admin' => 'Administrador del sistema',
        ];
        foreach ($roles as $nombre => $descripcion) {
            Role::firstOrCreate(
                ['nom' => $nombre],
                ['des' => $descripcion]
            );
        }
    }
}
