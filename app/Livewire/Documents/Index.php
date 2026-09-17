<?php

namespace App\Livewire\Documents;

use App\Services\DocumentService;
use App\Support\WithUnifiedPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Documentos')]
class Index extends Component
{
    use WithUnifiedPagination;

    #[Url]
    public string $q = '';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function inhabilitar(string $id, DocumentService $service): void
    {
        abort_unless(auth()->user()->hasPermission('documents.delete'), 403);
        $service->inhabilitar($id);
        session()->flash('status', 'Documento inhabilitado (is_deleted: true).');
    }

    public function render(DocumentService $service)
    {
        return view('livewire.documents.index', [
            'documents' => $this->paginateCollection($service->list($this->q ?: null)),
        ]);
    }
}
