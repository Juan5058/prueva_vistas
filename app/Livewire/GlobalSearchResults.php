<?php

namespace App\Livewire;

use App\Services\QuickSearchService;
use App\Support\SearchQuery;
use Livewire\Component;
use Livewire\WithPagination;

class GlobalSearchResults extends Component
{
    use WithPagination;

    public string $q = '';

    public string $nombre = '';

    public string $identificacion = '';

    public string $modulo = 'todos';

    protected $queryString = ['q', 'nombre', 'identificacion', 'modulo', 'page'];

    public function mount(): void
    {
        $this->q = request()->query('q', '');
        $this->nombre = request()->query('nombre', '');
        $this->identificacion = request()->query('identificacion', '');
        $this->modulo = request()->query('modulo', 'todos');
    }

    public function updatingNombre(): void { $this->resetPage(); }
    public function updatingIdentificacion(): void { $this->resetPage(); }
    public function updatingModulo(): void { $this->resetPage(); }
    public function updatingQ(): void { $this->resetPage(); }

    public function buscar(): void
    {
        // Resets pagination when filters are explicitly applied
        $this->resetPage();
    }

    public function limpiar(): void
    {
        $this->nombre = '';
        $this->identificacion = '';
        $this->modulo = 'todos';
        $this->resetPage();
    }

    public function render(QuickSearchService $search)
    {
        $user = auth()->user();
        $perPage = 20;

        // Determine which search term to use
        $term = '';
        if (filled($this->nombre)) {
            $term = $this->nombre;
        } elseif (filled($this->identificacion)) {
            $term = $this->identificacion;
        } elseif (filled($this->q)) {
            $term = $this->q;
        }

        // Map modulo filter to service filter
        $filterMap = [
            'todos'       => 'todos',
            'legal'       => 'expediente',
            'financiero'  => 'nombre',
            'inventarios' => 'codigo',
        ];
        $filter = $filterMap[$this->modulo] ?? 'todos';

        // If identificacion is set, force 'codigo' filter
        if (filled($this->identificacion)) {
            $filter = 'codigo';
        }
        // If nombre is set without specific module, force 'nombre' filter
        if (filled($this->nombre) && $this->modulo === 'todos') {
            $filter = 'nombre';
        }

        $allItems = [];
        $error = false;

        if (filled($term) && SearchQuery::normalize($term) !== null) {
            try {
                $result = $search->search($user, $term, $filter);
                if ($result['ready']) {
                    $allItems = $result['items'];
                }
            } catch (\Throwable) {
                $error = true;
            }
        }

        $ready = filled($term) && SearchQuery::normalize($term) !== null;
        $total = count($allItems);

        // Manual pagination over the items array
        $page = $this->getPage();
        $offset = ($page - 1) * $perPage;
        $pageItems = array_slice($allItems, $offset, $perPage);
        $lastPage = max(1, (int) ceil($total / $perPage));

        return view('livewire.global-search-results', [
            'items'      => $pageItems,
            'total'      => $total,
            'ready'      => $ready,
            'error'      => $error,
            'perPage'    => $perPage,
            'currentPage' => $page,
            'lastPage'   => $lastPage,
            'minLength'  => SearchQuery::MIN_LENGTH,
            'term'       => $term,
            'filters'    => QuickSearchService::FILTERS,
            'modulosDisponibles' => [
                'todos'       => 'Todos',
                'legal'       => 'Legal',
                'financiero'  => 'Financiero',
                'inventarios' => 'Inventarios',
            ],
        ]);
    }
}
