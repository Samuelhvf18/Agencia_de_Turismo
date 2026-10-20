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

            // Módulo 07 — Investigación Operativa II (Colas + Cadenas de Markov).
            // Catálogo de estados Markov, matriz de transición inicial y parámetros
            // iniciales M/M/c/K por tour.
            IoColasMarkovSeeder::class,
        ]);
    }
}
