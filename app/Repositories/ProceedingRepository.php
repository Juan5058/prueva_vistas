<?php

namespace App\Repositories;

use App\Models\Proceeding;
use App\Support\SearchQuery;
use Illuminate\Support\Collection;

class ProceedingRepository
{
    public function all(?string $search = null): Collection
    {
        return Proceeding::query()
            ->when($search, fn ($query) => SearchQuery::apply($query, $search, ['file_number', 'name', 'serie_name']))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @param  list<string>  $columns
     */
    public function suggest(string $term, array $columns, bool $prefix = false, int $limit = SearchQuery::MAX_RESULTS): Collection
    {
        return SearchQuery::applyLimited(Proceeding::query(), $term, $columns, $prefix, $limit)->get();
    }

    public function forSerie(string $trdId, string $serieId): Collection
    {
        return Proceeding::query()
            ->where('trd_structure_id', $trdId)
            ->where('serie_id', $serieId)
            ->withCount('documents')
            ->orderBy('file_number')
            ->get();
    }

    public function find(string $id): Proceeding
    {
        return Proceeding::query()->findOrFail($id);
    }

    public function create(array $data): Proceeding
    {
        return Proceeding::query()->create($data);
    }

    public function update(Proceeding $proceeding, array $data): Proceeding
    {
        $proceeding->fill($data);
        $proceeding->save();

        return $proceeding;
    }

    public function softDelete(Proceeding $proceeding): void
    {
        $proceeding->documents()->get()->each->softDelete();
        $proceeding->softDelete();
    }
}
