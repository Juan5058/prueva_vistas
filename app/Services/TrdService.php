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

    public function updateControl(string $id, string $version, ?string $approvedAt): TrdStructure
    {
        $structure = $this->repository->find($id);

        return $this->repository->update($structure, [
            'version' => $version,
            'approved_at' => $approvedAt ?: null,
        ]);
    }

    public function setActive(string $id, bool $active): TrdStructure
    {
        $user = auth()->user();
        $structure = $this->repository->find($id);

        if (! $active) {
            abort_unless($user?->hasPermission('trd.edit'), 403, 'No puede inhabilitar esta TRD.');

            return $this->repository->update($structure, ['is_active' => false]);
        }

        abort_unless($user?->isSuperAdmin(), 403, 'Solo el superusuario puede reactivar una TRD.');

        return $this->repository->update($structure, ['is_active' => true]);
    }

    public function create(array $payload): TrdStructure
    {
        $nested = TrdStructure::normalizeNested($payload);

        return $this->repository->create([
            'section_code' => $payload['section_code'],
            'section_name' => $payload['section_name'],
            'version' => $payload['version'] ?? 'TRD-V1-'.now()->year,
            'approved_at' => $payload['approved_at'] ?? null,
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
            'approved_at' => array_key_exists('approved_at', $payload)
                ? $payload['approved_at']
                : $structure->approved_at,
            ...$nested,
        ]);
    }

    public function inhabilitar(string $id): void
    {
        $this->repository->softDelete($this->repository->find($id));
    }
}
