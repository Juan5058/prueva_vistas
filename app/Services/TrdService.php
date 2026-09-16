<?php

namespace App\Services;

use App\Models\TrdStructure;
use App\Repositories\TrdRepository;
use Illuminate\Support\Collection;

class TrdService
{
    public function __construct(private TrdRepository $repository) {}

    public function list(?string $search = null): Collection
    {
        return $this->repository->all($search);
    }

    public function find(string $id): TrdStructure
    {
        return $this->repository->find($id);
    }

    public function create(array $payload): TrdStructure
    {
        $nested = TrdStructure::normalizeNested($payload);

        return $this->repository->create([
            'section_code' => $payload['section_code'],
            'section_name' => $payload['section_name'],
            'version' => $payload['version'] ?? 'TRD-V1-'.now()->year,
            'is_active' => true,
            'is_deleted' => false,
            ...$nested,
        ]);
    }

    public function update(string $id, array $payload): TrdStructure
    {
        $structure = $this->repository->find($id);
        $nested = TrdStructure::normalizeNested($payload);

        return $this->repository->update($structure, [
            'section_code' => $payload['section_code'],
            'section_name' => $payload['section_name'],
            'version' => $payload['version'] ?? $structure->version,
            ...$nested,
        ]);
    }

    public function inhabilitar(string $id): void
    {
        $this->repository->softDelete($this->repository->find($id));
    }
}
