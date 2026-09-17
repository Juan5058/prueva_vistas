<?php

namespace App\Livewire\Trd;

use App\Services\TrdService;
use App\Support\WithUnifiedPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Estructuras TRD')]
class Index extends Component
{
    use WithUnifiedPagination;

    #[Url]
    public string $q = '';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function inhabilitar(string $id, TrdService $trd): void
    {
        abort_unless(auth()->user()->hasPermission('trd.delete'), 403);
        $trd->inhabilitar($id);
        session()->flash('status', 'Estructura TRD inhabilitada (is_deleted: true).');
    }

    public function render(TrdService $trd)
    {
        return view('livewire.trd.index', [
            'structures' => $this->paginateCollection($trd->list($this->q ?: null)),
        ]);
    }
}
