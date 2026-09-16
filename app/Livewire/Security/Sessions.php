<?php

namespace App\Livewire\Security;

use App\Http\Middleware\VerifyUserSessionAndIp;
use App\Services\SecurityService;
use App\Services\UserService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Sesiones')]
class Sessions extends Component
{
    public function forceLogout(string $id, UserService $users): void
    {
        abort_unless(auth()->user()->hasPermission('security.force_logout'), 403);
        $users->forceLogout(
            $users->find($id),
            VerifyUserSessionAndIp::clientIp(request()),
            substr((string) request()->userAgent(), 0, 500),
        );
        session()->flash('status', 'Sesión finalizada de forma remota.');
    }

    public function render(SecurityService $security)
    {
        return view('livewire.security.sessions', [
            'sessions' => $security->sessions(),
        ]);
    }
}
