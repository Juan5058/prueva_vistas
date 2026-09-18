<?php

namespace App\Livewire\Trd;

use App\Services\TrdService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Formulario TRD')]
class Form extends Component
{
    public ?string $structureId = null;

    public string $section_code = '';

    public string $section_name = '';

    public string $version = '';

    public string $approved_at = '';

    public array $sub_sections = [];

    public array $series = [];

    public array $sub_series = [];

    public function mount(?string $trd = null): void
    {
        $service = app(TrdService::class);
        $this->version = 'TRD-V1-'.now()->year;
        $this->sub_sections = [['sub_section_code' => '', 'sub_section_name' => '']];
        $this->series = [['serie_code' => '', 'serie_name' => '', 'retencion_gestion' => 3, 'retencion_central' => 15, 'disposicion_final' => 'CT']];
        $this->sub_series = [['sub_serie_code' => '', 'sub_serie_name' => '']];

        if ($trd) {
            abort_unless(auth()->user()->hasPermission('trd.edit'), 403);
            $structure = $service->find($trd);
            $this->structureId = (string) $structure->getKey();
            $this->section_code = $structure->section_code;
            $this->section_name = $structure->section_name;
            $this->version = $structure->version;
            $this->approved_at = $structure->approved_at?->format('Y-m-d') ?? '';
            $this->sub_sections = $structure->sub_sections ?: $this->sub_sections;
            $this->series = $structure->series ?: $this->series;
            $this->sub_series = $structure->sub_series ?: $this->sub_series;
        } else {
            abort_unless(auth()->user()->hasPermission('trd.create'), 403);
        }
    }

    public function addSubSection(): void
    {
        $this->sub_sections[] = ['sub_section_code' => '', 'sub_section_name' => ''];
    }

    public function addSerie(): void
    {
        $this->series[] = ['serie_code' => '', 'serie_name' => '', 'retencion_gestion' => 3, 'retencion_central' => 15, 'disposicion_final' => 'CT'];
    }

    public function addSubSerie(): void
    {
        $this->sub_series[] = ['sub_serie_code' => '', 'sub_serie_name' => ''];
    }

    public function save(TrdService $service)
    {
        $this->validate([
            'section_code' => 'required|string|max:50',
            'section_name' => 'required|string|max:255',
            'version' => 'required|string|max:80',
            'approved_at' => 'nullable|date',
        ]);

        $payload = $this->only(['section_code', 'section_name', 'version', 'approved_at', 'sub_sections', 'series', 'sub_series']);
        $payload['approved_at'] = $this->approved_at !== '' ? $this->approved_at : null;

        if ($this->structureId) {
            $service->update($this->structureId, $payload);
            session()->flash('status', 'Estructura TRD actualizada.');

            return $this->redirect(route('trd.show', $this->structureId), navigate: true);
        }

        $created = $service->create($payload);
        session()->flash('status', 'Estructura TRD creada.');

        return $this->redirect(route('trd.show', $created->getKey()), navigate: true);
    }

    public function render()
    {
        return view('livewire.trd.form');
    }
}
