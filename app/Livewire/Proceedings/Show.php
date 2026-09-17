<?php

namespace App\Livewire\Proceedings;

use App\Services\ProceedingService;
use App\Support\WithUnifiedPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detalle de expediente')]
class Show extends Component
{
    use WithUnifiedPagination;

    public string $proceedingId = '';

    public string $sub_code = '';

    public string $sub_name = '';

    public function mount($proceedingId = null): void
    {
        $this->proceedingId = (string) $proceedingId;
    }

    public function addSub(ProceedingService $service): void
    {
        abort_unless(auth()->user()->hasPermission('proceedings.create'), 403);
        $this->validate([
            'sub_code' => 'required|string|max:50',
            'sub_name' => 'required|string|max:255',
        ]);
        $service->addSubProceeding($this->proceedingId, $this->sub_code, $this->sub_name);
        $this->reset('sub_code', 'sub_name');
        session()->flash('status', 'Subexpediente agregado.');
    }

    public function render(ProceedingService $service)
    {
        $proceeding = $service->find($this->proceedingId);

        return view('livewire.proceedings.show', [
            'proceeding' => $proceeding,
            'documents' => $this->paginateCollection($proceeding->documents ?? []),
        ]);
    }
}
