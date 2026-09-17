<?php

namespace App\Support;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\WithPagination;

trait WithUnifiedPagination
{
    use WithPagination;

    public string $perPage = '10';

    public string $customPerPage = '';

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function updatedCustomPerPage(): void
    {
        if ($this->perPage === 'custom') {
            $this->resetPage();
        }
    }

    public function getPerPageInteger(): int
    {
        if ($this->perPage === 'custom') {
            $val = (int) $this->customPerPage;
            return $val > 0 ? $val : 10;
        }

        $val = (int) $this->perPage;
        return $val > 0 ? $val : 10;
    }

    protected function paginateCollection(Collection|array $items): LengthAwarePaginator
    {
        $items = $items instanceof Collection ? $items : collect($items);
        $total = $items->count();
        $perPage = $this->getPerPageInteger();

        $page = (int) $this->getPage();
        $maxPage = max(1, (int) ceil($total / $perPage));
        if ($page > $maxPage) {
            $page = $maxPage;
            $this->setPage($page);
        }

        $slice = $items->slice(($page - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }
}
