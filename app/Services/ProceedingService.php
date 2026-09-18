<?php

namespace App\Services;

use App\Models\Proceeding;
use App\Models\TrdStructure;
use App\Repositories\ProceedingRepository;
use App\Repositories\TrdRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProceedingService
{
    public function __construct(
        private ProceedingRepository $repository,
        private TrdRepository $trdRepository,
    ) {}

    public function list(?string $search = null): Collection
    {
        return $this->repository->all($search);
    }

    public function find(string $id): Proceeding
    {
        return $this->repository->find($id);
    }

    public function structures(): Collection
    {
        return $this->trdRepository->all()->where('is_active', true)->values();
    }

    public function forSerie(string $trdId, string $serieId): Collection
    {
        return $this->repository->forSerie($trdId, $serieId);
    }

    public function create(array $payload): Proceeding
    {
        return $this->repository->create($this->compose($payload));
    }

    public function update(string $id, array $payload): Proceeding
    {
        $proceeding = $this->repository->find($id);

        return $this->repository->update($proceeding, $this->compose($payload, $proceeding->getKey()));
    }

    public function inhabilitar(string $id): void
    {
        $this->repository->softDelete($this->repository->find($id));
    }

    public function addSubProceeding(string $id, string $code, string $name): Proceeding
    {
        $proceeding = $this->repository->find($id);
        $subs = $proceeding->sub_proceedings ?? [];
        $subs[] = [
            'sub_proceeding_id' => (string) Str::uuid(),
            'code' => $code,
            'name' => $name,
            'creation_date' => now()->toIso8601String(),
        ];

        return $this->repository->update($proceeding, ['sub_proceedings' => $subs]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function compose(array $payload, mixed $ignoreId = null): array
    {
        $exists = Proceeding::query()
            ->where('file_number', $payload['file_number'])
            ->when($ignoreId, function ($q) use ($ignoreId) {
                $q->where($q->getModel()->getKeyName(), '!=', $ignoreId);
            })
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'file_number' => 'El número de expediente ya está registrado.',
            ]);
        }

        $trd = TrdStructure::query()->findOrFail($payload['trd_structure_id']);
        $serie = $trd->findSerie($payload['serie_id']);
        $subSerie = $trd->findSubSerie($payload['sub_serie_id']);

        return [
            'trd_structure_id' => $trd->getKey(),
            'section_code' => $trd->section_code,
            'serie_id' => $payload['serie_id'],
            'sub_serie_id' => $payload['sub_serie_id'],
            'serie_name' => $serie['serie_name'] ?? null,
            'sub_serie_name' => $subSerie['sub_serie_name'] ?? null,
            'file_number' => $payload['file_number'],
            'name' => $payload['name'],
            'description' => $payload['description'] ?? '',
            'opening_date' => $payload['opening_date'] ?? now(),
            'deadline' => $payload['deadline'] ?? null,
            'state' => $payload['state'] ?? 'Público',
            'physical_location' => $payload['physical_location'] ?? [],
            'sub_proceedings' => $payload['sub_proceedings'] ?? [],
            'is_deleted' => false,
        ];
    }
}
