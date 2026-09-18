<?php

namespace App\Livewire;

use App\Support\SearchQuery;
use Livewire\Component;

class QuickSearch extends Component
{
    public string $q = '';

    public function updatedQ(string $value): void
    {
        $this->q = mb_substr(trim($value), 0, SearchQuery::MAX_LENGTH);
    }

    public function search(): void
    {
        $term = SearchQuery::normalize($this->q);

        if ($term === null) {
            return;
        }

        $this->redirect(route('search.global', ['q' => $term]), navigate: true);
    }

    public function render()
    {
        return view('livewire.quick-search');
    }
}
