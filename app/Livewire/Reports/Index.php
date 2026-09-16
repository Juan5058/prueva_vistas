<?php

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Reportes TRD')]
class Index extends Component
{
    public function render(ReportService $reports)
    {
        return view('livewire.reports.index', $reports->metrics());
    }
}
