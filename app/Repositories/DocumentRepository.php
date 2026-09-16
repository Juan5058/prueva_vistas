<?php

namespace App\Repositories;

use App\Models\ArchiveDocument;
use App\Support\SearchQuery;
use Illuminate\Support\Collection;

class DocumentRepository
{
    public function all(?string $search = null): Collection
    {
        return ArchiveDocument::query()
            ->with('proceeding')
            ->when($search, fn ($query) => SearchQuery::apply($query, $search, ['name', 'document_type']))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(string $id): ArchiveDocument
    {
        return ArchiveDocument::query()->findOrFail($id);
    }

    public function create(array $data): ArchiveDocument
    {
        return ArchiveDocument::query()->create($data);
    }

    public function softDelete(ArchiveDocument $document): void
    {
        $document->softDelete();
    }
}
