<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rol_id' => fn () => Role::firstOrCreate(['nom' => User::ROL_TURISTA])->id,
            'nom' => fake()->name(),
            'cor' => fake()->unique()->safeEmail(),
            'cor_vrf_en' => now(),
            'clv' => static::$password ??= Hash::make('password'),
            'ci_cmp' => '',
            'act' => true,
        ];
    }

    /** Usuario con el rol indicado (turista, guia, chofer, operaciones, admin). */
    public function rol(string $nombre): static
    {
        return $this->state(fn () => [
            'rol_id' => Role::firstOrCreate(['nom' => $nombre])->id,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'cor_vrf_en' => null,
        ]);
    }

    /** Jetstream sin equipos: se mantiene por compatibilidad con sus tests. */
    public function withPersonalTeam(?callable $callback = null): static
    {
        return $this->state([]);
    }
}
