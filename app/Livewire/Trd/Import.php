<?php

namespace App\Livewire\Trd;

use App\Services\SecurityService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Carga masiva TRD')]
class Import extends Component
{
    use WithFileUploads;

    public $file;

    public function save(SecurityService $security)
    {
        abort_unless(auth()->user()->hasPermission('trd.import'), 403);

        $this->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $security->queueImport((string) auth()->id(), $this->file);
        session()->flash('status', 'Archivo enviado a la cola Redis (ImportTrdJob).');
        $this->reset('file');
    }

    public function render(SecurityService $security)
    {
        return view('livewire.trd.import', [
            'imports' => $security->imports(),
        ]);
    }
}
