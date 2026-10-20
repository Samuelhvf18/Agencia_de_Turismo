<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'turista' => 'Cliente que reserva tours',
            'guia' => 'Guía de turismo asignado a salidas',
            'chofer' => 'Conductor de la flota',
            'operaciones' => 'Personal de operaciones de la agencia',
            'admin' => 'Administrador del sistema',
        ];

        foreach ($roles as $nombre => $descripcion) {
            Role::updateOrCreate(['nom' => $nombre], ['des' => $descripcion]);
        }
    }
}
