<?php

namespace App\Livewire\Trd;

use App\Services\TrdService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Estructuras TRD')]
class Index extends Component
{
    #[Url]
    public string $q = '';

    public function inhabilitar(string $id, TrdService $trd): void
    {
        abort_unless(auth()->user()->hasPermission('trd.delete'), 403);
        $trd->inhabilitar($id);
        session()->flash('status', 'Estructura TRD inhabilitada (is_deleted: true).');
    }

    public function render(TrdService $trd)
    {
        return view('livewire.trd.index', [
            'structures' => $trd->list($this->q ?: null),
        ]);
    }
}
