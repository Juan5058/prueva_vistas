<?php

namespace App\Livewire\Proceedings;

use App\Services\ProceedingService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Expediente')]
class Form extends Component
{
    public ?string $proceedingId = null;

    public string $trd_structure_id = '';

    public string $serie_id = '';

    public string $sub_serie_id = '';

    public string $file_number = '';

    public string $name = '';

    public string $description = '';

    public string $opening_date = '';

    public string $deadline = '';

    public string $state = 'Público';

    public array $physical_location = [];

    public function mount(?string $proceeding = null): void
    {
        $service = app(ProceedingService::class);
        $this->file_number = 'EXP-'.now()->year.'-'.random_int(1000, 9999);
        $this->opening_date = now()->toDateString();
        $this->physical_location = [
            'deposit' => 'Depósito Central 01',
            'shelf' => 'E-04',
            'module' => 'M-2',
            'box' => 'C-12',
            'folder' => '01',
        ];

        $structures = $service->structures();
        if ($structures->isNotEmpty()) {
            $first = $structures->first();
            $this->trd_structure_id = (string) $first->getKey();
            $this->serie_id = $first->series[0]['serie_id'] ?? '';
            $this->sub_serie_id = $first->sub_series[0]['sub_serie_id'] ?? '';
        }

        if ($proceeding) {
            abort_unless(auth()->user()->hasPermission('proceedings.edit'), 403);
            $model = $service->find($proceeding);
            $this->proceedingId = (string) $model->getKey();
            $this->trd_structure_id = (string) $model->trd_structure_id;
            $this->serie_id = $model->serie_id;
            $this->sub_serie_id = $model->sub_serie_id;
            $this->file_number = $model->file_number;
            $this->name = $model->name;
            $this->description = (string) $model->description;
            $this->opening_date = optional($model->opening_date)?->toDateString() ?? '';
            $this->deadline = optional($model->deadline)?->toDateString() ?? '';
            $this->state = $model->state;
            $this->physical_location = $model->physical_location ?? $this->physical_location;
        } else {
            abort_unless(auth()->user()->hasPermission('proceedings.create'), 403);
        }
    }

    public function save(ProceedingService $service)
    {
        $this->validate([
            'trd_structure_id' => 'required',
            'serie_id' => 'required',
            'sub_serie_id' => 'required',
            'file_number' => 'required|string|max:80',
            'name' => 'required|string|max:255',
            'state' => 'required|in:Público,Privado,Reservado',
            'opening_date' => 'required|date',
        ]);

        $payload = $this->only([
            'trd_structure_id', 'serie_id', 'sub_serie_id', 'file_number', 'name',
            'description', 'opening_date', 'deadline', 'state', 'physical_location',
        ]);

        if ($this->proceedingId) {
            $model = $service->update($this->proceedingId, $payload);
        } else {
            $model = $service->create($payload);
        }

        session()->flash('status', 'Expediente guardado.');

        return $this->redirect(route('proceedings.show', $model->getKey()), navigate: true);
    }

    public function render(ProceedingService $service)
    {
        return view('livewire.proceedings.form', [
            'structures' => $service->structures(),
        ]);
    }
}
