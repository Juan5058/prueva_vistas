<?php

namespace App\Repositories;

use App\Models\Document;
use App\Support\SearchQuery;
use Illuminate\Support\Collection;

class DocumentRepository
{
    public function all(?string $search = null): Collection
    {
        return Document::query()
            ->with('proceeding')
            ->when($search, fn ($query) => SearchQuery::apply($query, $search, ['name', 'document_type']))
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(string $id): Document
    {
        return Document::query()->findOrFail($id);
    }

    public function create(array $data): Document
    {
        return Document::query()->create($data);
    }

    public function update(Document $document, array $data): Document
    {
        $document->fill($data);
        $document->save();

        return $document;
    }

    public function softDelete(Document $document): void
    {
        $document->softDelete();
    }
}
