<?php

namespace App\Livewire\Security;

use App\Services\SecurityService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Logs de auditoría')]
class Logs extends Component
{
    public function render(SecurityService $security)
    {
        return view('livewire.security.logs', [
            'logs' => $security->logs(),
        ]);
    }
}
