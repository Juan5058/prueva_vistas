<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository
{
    public function all(): Collection
    {
        return User::query()->orderBy('created_at', 'desc')->get();
    }

    public function find(string $id): User
    {
        return User::query()->findOrFail($id);
    }

    public function findByEmail(string $email, bool $withTrashed = false): ?User
    {
        $query = $withTrashed ? User::withTrashed() : User::query();

        return $query->where('email', strtolower($email))->first();
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user;
    }

    public function softDelete(User $user): void
    {
        $user->softDelete();
    }
}
