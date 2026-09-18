<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\DocumentRepository;
use App\Repositories\ProceedingRepository;
use App\Repositories\TrdRepository;
use App\Repositories\UserRepository;
use App\Support\SearchQuery;

class QuickSearchService
{
    public const FILTERS = ['todos', 'codigo', 'nombre', 'expediente', 'persona'];

    public function __construct(
        private TrdRepository $trd,
        private ProceedingRepository $proceedings,
        private DocumentRepository $documents,
        private UserRepository $users,
    ) {}

    /**
     * @return array{ready: bool, term: ?string, filter: string, items: list<array<string, string>>}
     */
    public function search(?User $user, string $query, string $filter): array
    {
        $filter = in_array($filter, self::FILTERS, true) ? $filter : 'todos';
        $term = SearchQuery::normalize($query);

        if ($user === null || $term === null) {
            return [
                'ready' => false,
                'term' => $term,
                'filter' => $filter,
                'items' => [],
            ];
        }

        $items = [];

        if ($this->wants($filter, ['todos', 'codigo', 'nombre']) && $user->hasPermission('trd.view')) {
            $columns = $filter === 'codigo' ? ['section_code'] : ($filter === 'nombre' ? ['section_name'] : ['section_code', 'section_name']);
            foreach ($this->trd->suggest($term, $columns, $filter === 'codigo') as $structure) {
                $items[] = [
                    'module' => 'TRD',
                    'title' => (string) $structure->section_name,
                    'meta' => trim($structure->section_code.' · '.$structure->version),
                    'url' => route('trd.show', $structure->getKey()),
                ];
            }
        }

        if ($this->wants($filter, ['todos', 'codigo', 'nombre', 'expediente']) && $user->hasPermission('proceedings.view')) {
            $columns = match ($filter) {
                'codigo' => ['file_number', 'section_code'],
                'nombre' => ['name'],
                default => ['file_number', 'name'],
            };
            foreach ($this->proceedings->suggest($term, $columns, $filter === 'codigo') as $proceeding) {
                $items[] = [
                    'module' => 'Expediente',
                    'title' => (string) $proceeding->name,
                    'meta' => (string) $proceeding->file_number,
                    'url' => route('proceedings.show', $proceeding->getKey()),
                ];
            }
        }

        if ($this->wants($filter, ['todos', 'nombre']) && $user->hasPermission('documents.view')) {
            foreach ($this->documents->suggest($term, ['name']) as $document) {
                $items[] = [
                    'module' => 'Documento',
                    'title' => (string) $document->name,
                    'meta' => (string) $document->document_type,
                    'url' => route('documents.show', $document->getKey()),
                ];
            }
        }

        if ($this->wants($filter, ['todos', 'persona']) && $user->hasPermission('users.view')) {
            foreach ($this->users->suggest($term, ['name', 'email']) as $person) {
                $canEdit = $user->hasPermission('users.edit');
                $items[] = [
                    'module' => 'Persona',
                    'title' => (string) $person->name,
                    'meta' => (string) $person->email,
                    'url' => $canEdit
                        ? route('users.edit', $person->getKey())
                        : route('users.index'),
                ];
            }
        }

        return [
            'ready' => true,
            'term' => $term,
            'filter' => $filter,
            'items' => $items,
        ];
    }

    /**
     * @param  list<string>  $allowed
     */
    private function wants(string $filter, array $allowed): bool
    {
        return in_array($filter, $allowed, true);
    }
}
