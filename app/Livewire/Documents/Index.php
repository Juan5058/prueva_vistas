<?php

namespace App\Livewire\Documents;

use App\Services\DocumentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Documentos')]
class Index extends Component
{
    #[Url]
    public string $q = '';

    public function inhabilitar(string $id, DocumentService $service): void
    {
        abort_unless(auth()->user()->hasPermission('documents.delete'), 403);
        $service->inhabilitar($id);
        session()->flash('status', 'Documento inhabilitado (is_deleted: true).');
    }

    public function render(DocumentService $service)
    {
        return view('livewire.documents.index', [
            'documents' => $service->list($this->q ?: null),
        ]);
    }
}
