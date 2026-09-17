<?php

namespace App\Livewire\Proceedings;

use App\Services\ProceedingService;
use App\Support\WithUnifiedPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Expedientes')]
class Index extends Component
{
    use WithUnifiedPagination;

    #[Url]
    public string $q = '';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function inhabilitar(string $id, ProceedingService $service): void
    {
        abort_unless(auth()->user()->hasPermission('proceedings.delete'), 403);
        $service->inhabilitar($id);
        session()->flash('status', 'Expediente inhabilitado (is_deleted: true).');
    }

    public function render(ProceedingService $service)
    {
        return view('livewire.proceedings.index', [
            'proceedings' => $this->paginateCollection($service->list($this->q ?: null)),
        ]);
    }
}
