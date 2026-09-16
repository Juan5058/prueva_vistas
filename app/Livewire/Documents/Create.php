<?php

namespace App\Livewire\Documents;

use App\Services\DocumentService;
use App\Services\ProceedingService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Cargar documento')]
class Create extends Component
{
    use WithFileUploads;

    public string $proceedings_id = '';

    public string $document_type = 'Acta';

    public string $name = '';

    public string $description = '';

    public string $support = 'Electrónico';

    public string $document_creation_date = '';

    public string $state = 'Cerrado';

    public $file;

    public function mount(ProceedingService $proceedings): void
    {
        abort_unless(auth()->user()->hasPermission('documents.upload'), 403);
        $this->document_creation_date = now()->toDateString();
        $first = $proceedings->list()->first();
        $this->proceedings_id = $first ? (string) $first->getKey() : '';
        if (request('proceeding_id')) {
            $this->proceedings_id = (string) request('proceeding_id');
        }
    }

    public function save(DocumentService $service)
    {
        $this->validate([
            'proceedings_id' => 'required',
            'document_type' => 'required|string|max:80',
            'name' => 'required|string|max:255',
            'support' => 'required|in:Físico,Electrónico',
            'state' => 'required|in:Abierto,Cerrado,Privado',
            'document_creation_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:10240',
        ]);

        $service->create($this->only([
            'proceedings_id', 'document_type', 'name', 'description', 'support', 'state', 'document_creation_date',
        ]), $this->file);

        session()->flash('status', 'Documento indexado en Storage::private con UUID.');

        return $this->redirect(route('documents.index'), navigate: true);
    }

    public function render(ProceedingService $proceedings)
    {
        return view('livewire.documents.create', [
            'proceedings' => $proceedings->list(),
        ]);
    }
}
