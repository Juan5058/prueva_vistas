<?php

namespace App\Livewire\Documents;

use App\Services\DocumentService;
use App\Services\ProceedingService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Documento')]
class Form extends Component
{
    use WithFileUploads;

    public ?string $documentId = null;

    public string $proceedings_id = '';

    public string $document_type = 'Acta';

    public string $name = '';

    public string $description = '';

    public string $support = 'Electrónico';

    public string $document_creation_date = '';

    public string $state = 'Cerrado';

    public $file;

    public function mount(?string $document = null): void
    {
        $proceedings = app(ProceedingService::class)->list();
        $first = $proceedings->first();
        $this->proceedings_id = $first ? (string) $first->getKey() : '';
        $this->document_creation_date = now()->toDateString();

        if (request('proceeding_id')) {
            $this->proceedings_id = (string) request('proceeding_id');
        }

        if ($document) {
            abort_unless(auth()->user()->hasPermission('documents.edit'), 403);
            $model = app(DocumentService::class)->find($document);
            $this->documentId = (string) $model->getKey();
            $this->proceedings_id = (string) $model->proceedings_id;
            $this->document_type = $model->document_type;
            $this->name = $model->name;
            $this->description = (string) $model->description;
            $this->support = $model->support;
            $this->document_creation_date = optional($model->document_creation_date)?->toDateString() ?: now()->toDateString();
            $this->state = $model->state;
        } else {
            abort_unless(auth()->user()->hasPermission('documents.upload'), 403);
        }
    }

    public function save(DocumentService $service)
    {
        $this->validate([
            'proceedings_id' => 'required',
            'document_type' => 'required|string|max:80',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'support' => 'required|in:Físico,Electrónico',
            'state' => 'required|in:Abierto,Cerrado,Privado',
            'document_creation_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf|mimetypes:application/pdf|max:10240',
        ]);

        $payload = $this->only([
            'proceedings_id', 'document_type', 'name', 'description', 'support', 'state', 'document_creation_date',
        ]);

        if ($this->documentId) {
            $service->update($this->documentId, $payload, $this->file);
            session()->flash('status', 'Documento actualizado. El PDF permanece en Storage::disk(\'private\').');
        } else {
            $service->create($payload, $this->file);
            session()->flash('status', 'Documento indexado en Storage::disk(\'private\') con UUID.');
        }

        return $this->redirect(route('documents.index'), navigate: true);
    }

    public function render(ProceedingService $proceedings)
    {
        return view('livewire.documents.form', [
            'proceedings' => $proceedings->list(),
        ]);
    }
}
