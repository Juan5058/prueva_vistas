<?php

namespace App\Livewire;

use App\Services\SecurityService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Dashboard TRD')]
class Dashboard extends Component
{
    public function render(SecurityService $security)
    {
        return view('livewire.dashboard', $security->dashboard());
    }
}
