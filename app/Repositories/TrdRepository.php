<?php

namespace App\Repositories;

use App\Models\TrdStructure;
use App\Support\SearchQuery;
use Illuminate\Support\Collection;

class TrdRepository
{
    public function all(?string $search = null): Collection
    {
        return TrdStructure::query()
            ->when($search, fn ($query) => SearchQuery::apply($query, $search, ['section_code', 'section_name', 'version']))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(string $id): TrdStructure
    {
        return TrdStructure::query()->findOrFail($id);
    }

    public function create(array $data): TrdStructure
    {
        return TrdStructure::query()->create($data);
    }

    public function update(TrdStructure $structure, array $data): TrdStructure
    {
        $structure->fill($data);
        $structure->save();

        return $structure;
    }

    public function softDelete(TrdStructure $structure): void
    {
        $structure->softDelete();
    }
}
