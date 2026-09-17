<?php

namespace App\Livewire\Documents;

use App\Services\DocumentService;
use App\Support\WithUnifiedPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detalle de documento')]
class Show extends Component
{
    use WithUnifiedPagination;

    public string $documentId = '';

    public function mount($documentId = null): void
    {
        $this->documentId = (string) $documentId;
        app(DocumentService::class)->view($this->documentId);
    }

    public function render(DocumentService $service)
    {
        $document = $service->find($this->documentId);

        return view('livewire.documents.show', [
            'document' => $document,
            'audits' => $this->paginateCollection($document->auditLogs()->with('user')->orderBy('created_at', 'desc')->get()),
        ]);
    }
}
