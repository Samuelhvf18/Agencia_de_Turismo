<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Módulo 07 — Investigación Operativa II (Colas + Cadenas de Markov).
        // Catálogo de estados Markov, matriz de transición inicial y parámetros
        // iniciales M/M/c/K por tour. Requiere que exista al menos la tabla `tours`;
        // los parámetros se saltan si no hay tours cargados.
        $this->call(IoColasMarkovSeeder::class);
    }
}
