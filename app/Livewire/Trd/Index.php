<?php

namespace App\Livewire\Trd;

use App\Services\TrdService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Control TRD')]
class Index extends Component
{
    #[Url]
    public string $q = '';

    /**
     * @var array<string, string>
     */
    public array $versions = [];

    /**
     * @var array<string, string>
     */
    public array $approved = [];

    public function inhabilitar(string $id, TrdService $trd): void
    {
        abort_unless(auth()->user()->hasPermission('trd.delete'), 403);
        $trd->inhabilitar($id);
        session()->flash('status', 'Estructura TRD inhabilitada (is_deleted: true).');
    }

    public function saveControl(string $id, TrdService $trd): void
    {
        abort_unless(auth()->user()->hasPermission('trd.edit'), 403);

        $this->validate([
            "versions.{$id}" => 'required|string|max:80',
            "approved.{$id}" => 'nullable|date',
        ]);

        $trd->updateControl($id, $this->versions[$id], $this->approved[$id] ?: null);
        session()->flash('status', 'Parametrización de la TRD actualizada.');
    }

    public function toggleActive(string $id, TrdService $trd): void
    {
        $structure = $trd->find($id);

        if ($structure->is_active) {
            $trd->setActive($id, false);
            session()->flash('status', 'TRD inhabilitada. Solo un superusuario puede reactivarla.');

            return;
        }

        $trd->setActive($id, true);
        session()->flash('status', 'TRD reactivada.');
    }

    public function render(TrdService $trd)
    {
        $structures = $trd->list($this->q ?: null);

        foreach ($structures as $structure) {
            $key = (string) $structure->getKey();
            if (! array_key_exists($key, $this->versions)) {
                $this->versions[$key] = (string) $structure->version;
                $this->approved[$key] = $structure->approved_at?->format('Y-m-d') ?? '';
            }
        }

        return view('livewire.trd.index', [
            'structures' => $structures,
        ]);
    }
}
