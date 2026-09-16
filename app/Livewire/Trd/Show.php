<?php

namespace App\Livewire\Trd;

use App\Services\TrdService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Detalle TRD')]
class Show extends Component
{
    public string $trdId = '';

    public function mount($trdId = null): void
    {
        $this->trdId = (string) $trdId;
    }

    public function render(TrdService $service)
    {
        return view('livewire.trd.show', [
            'structure' => $service->find($this->trdId),
        ]);
    }
}
