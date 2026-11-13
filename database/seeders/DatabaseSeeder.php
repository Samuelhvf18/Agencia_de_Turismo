<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'guia']);
        Role::firstOrCreate(['name' => 'chofer']);
        Role::firstOrCreate(['name' => 'turista']);

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

        $deptoLaPazId = DB::table('departamentos_bolivia')->where('cod', 'LP')->value('id');

        $adminUser = User::create([
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
        $adminUser->assignRole('admin');

        $guia = User::create([
            'nom'        => 'Guía Turístico',
            'cor'        => 'guia@gmail.com',
            'clv'        => Hash::make('123qwe'),
            'rol'        => 'guia',
            'tel'        => '76845010',
            'tip_doc'    => 'CI',
            'num_doc'    => '1260086',
            'ci_cmp'     => '1A',
            'ci_dep_id'  => $deptoLaPazId,
            'act'        => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $guia->assignRole('guia');

        $chofer = User::create([
            'nom'        => 'Chofer Flota',
            'cor'        => 'chofer@gmail.com',
            'clv'        => Hash::make('123qwe'),
            'rol'        => 'chofer',
            'tel'        => '76845011',
            'tip_doc'    => 'CI',
            'num_doc'    => '1260087',
            'ci_cmp'     => '1A',
            'ci_dep_id'  => $deptoLaPazId,
            'act'        => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $chofer->assignRole('chofer');

        $this->call([
            PaisesSeeder::class,
            ToursSystemSeeder::class,
        ]);
    }
}