<?php

namespace App\Livewire\Trd;

use App\Services\ProceedingService;
use App\Services\TrdService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Navegación TRD')]
class Show extends Component
{
    public string $trdId = '';

    public string $dependency = 'seccion';

    public ?string $serieId = null;

    public ?string $proceedingId = null;

    public string $viewLayer = 'organic';

    public function mount($trdId = null, $dependency = 'seccion', $serieId = null, $proceedingId = null): void
    {
        $this->trdId = (string) $trdId;
        $this->dependency = (string) ($dependency ?: 'seccion');
        $this->serieId = $serieId ? (string) $serieId : null;
        $this->proceedingId = $proceedingId ? (string) $proceedingId : null;
        $this->viewLayer = match (request()->route()?->getName()) {
            'trd.series' => 'series',
            'trd.holdings' => 'holdings',
            'trd.expediente' => 'expediente',
            default => 'organic',
        };
    }

    public function render(TrdService $trd, ProceedingService $proceedings)
    {
        $structure = $trd->find($this->trdId);

        $proceeding = null;
        if ($this->proceedingId) {
            $proceeding = $proceedings->find($this->proceedingId);
            abort_unless((string) $proceeding->trd_structure_id === $this->trdId, 404);
            $proceeding->load('documents');
        }

        return view('livewire.trd.explorer', [
            'structure' => $structure,
            'layer' => $this->viewLayer,
            'dependencyName' => $structure->dependencyName($this->dependency),
            'proceedings' => $this->serieId ? $proceedings->forSerie($this->trdId, $this->serieId) : collect(),
            'serie' => $this->serieId ? $structure->findSerie($this->serieId) : null,
            'proceeding' => $proceeding,
        ]);
    }
}
