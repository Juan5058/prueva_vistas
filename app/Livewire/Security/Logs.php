<?php

namespace App\Livewire\Security;

use App\Services\SecurityService;
use App\Support\WithUnifiedPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Logs de auditoría')]
class Logs extends Component
{
    use WithUnifiedPagination;

    public function render(SecurityService $security)
    {
        return view('livewire.security.logs', [
            'logs' => $this->paginateCollection($security->logs()),
        ]);
    }
}
