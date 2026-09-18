<?php

namespace App\Repositories;

use App\Models\User;
use App\Support\SearchQuery;
use Illuminate\Support\Collection;

class UserRepository
{
    public function all(): Collection
    {
        return User::query()->orderBy('created_at', 'desc')->get();
    }

    /**
     * @param  list<string>  $columns
     */
    public function suggest(string $term, array $columns, bool $prefix = false, int $limit = SearchQuery::MAX_RESULTS): Collection
    {
        return SearchQuery::applyLimited(User::query(), $term, $columns, $prefix, $limit)->get();
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
