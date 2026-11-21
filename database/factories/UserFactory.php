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
    /** Contraseña reutilizada por la fábrica. */
    protected static ?string $password;

    /**
     * Estado inicial del usuario.
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

    /** Usuario con rol asignado. */
    public function rol(string $nombre): static
    {
        return $this->state(fn () => [
            'rol_id' => Role::firstOrCreate(['nom' => $nombre])->id,
        ]);
    }

    /** Correo pendiente de verificar. */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'cor_vrf_en' => null,
        ]);
    }

    /** Compatibilidad de Jetstream sin equipos. */
    public function withPersonalTeam(?callable $callback = null): static
    {
        return $this->state([]);
    }
}
