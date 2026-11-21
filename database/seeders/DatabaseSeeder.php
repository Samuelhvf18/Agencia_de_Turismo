<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            CatalogosBaseSeeder::class,
            AdminUserSeeder::class,
            UsuariosDemoSeeder::class,
            CatalogoLandingSeeder::class,
            RutasCatalogoSeeder::class,
            OperacionDemoSeeder::class,

            // Investigación Operativa II.
            // Estados y transiciones Markov.
            // Parámetros iniciales de colas.
            IoColasMarkovSeeder::class,
        ]);
    }
}
