<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nom' => 'admin', 'des' => 'Administrador general del sistema'],
            ['nom' => 'operaciones', 'des' => 'Personal de logistica y salidas'],
            ['nom' => 'guia', 'des' => 'Guia turistico autorizado'],
            ['nom' => 'chofer', 'des' => 'Chofer de la flota de vehiculos'],
            ['nom' => 'turista', 'des' => 'Turista nacional o extranjero'],
        ]);

        DB::table('departamentos_bolivia')->insert([
            ['cod' => 'LP', 'nom' => 'La Paz'],
            ['cod' => 'CB', 'nom' => 'Cochabamba'],
            ['cod' => 'SC', 'nom' => 'Santa Cruz'],
            ['cod' => 'OR', 'nom' => 'Oruro'],
            ['cod' => 'PT', 'nom' => 'Potosí'],
            ['cod' => 'CH', 'nom' => 'Chuquisaca'],
            ['cod' => 'TJ', 'nom' => 'Tarija'],
            ['cod' => 'BE', 'nom' => 'Beni'],
            ['cod' => 'PD', 'nom' => 'Pando'],
        ]);

        $rolAdminId = DB::table('roles')->where('nom', 'admin')->value('id');
        $deptoLaPazId = DB::table('departamentos_bolivia')->where('cod', 'LP')->value('id');

        DB::table('users')->insert([
            'rol_id'     => $rolAdminId,
            'nom'        => 'Administrador Pipocas',
            'cor'        => 'admin@gmail.com',
            'clv'        => Hash::make('123qwe'),
            'rol'        => 'admin',
            'tel'        => '76845008',
            'tip_doc'    => 'CI',
            'num_doc'    => '1260084',
            'ci_cmp'     => '1A',
            'ci_dep_id'  => $deptoLaPazId,
            'act'        => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}