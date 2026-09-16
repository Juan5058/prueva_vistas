<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\RoleCatalog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $role = RoleCatalog::APRENDIZ;

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => $role,
            'roles' => [$role],
            'permissions' => RoleCatalog::forRole($role),
            'current_ip' => '127.0.0.1',
            'allowed_ip_range' => '*',
            'is_active' => true,
            'force_logout' => false,
            'is_deleted' => false,
            'deleted_at' => null,
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn () => [
            'role' => RoleCatalog::SUPER_ADMIN,
            'roles' => [RoleCatalog::SUPER_ADMIN],
            'permissions' => RoleCatalog::forRole(RoleCatalog::SUPER_ADMIN),
        ]);
    }

    public function liderAmbiental(): static
    {
        return $this->state(fn () => [
            'role' => RoleCatalog::LIDER_AMBIENTAL,
            'roles' => [RoleCatalog::LIDER_AMBIENTAL],
            'permissions' => RoleCatalog::forRole(RoleCatalog::LIDER_AMBIENTAL),
        ]);
    }

    public function aprendiz(): static
    {
        return $this->state(fn () => [
            'role' => RoleCatalog::APRENDIZ,
            'roles' => [RoleCatalog::APRENDIZ],
            'permissions' => RoleCatalog::forRole(RoleCatalog::APRENDIZ),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
