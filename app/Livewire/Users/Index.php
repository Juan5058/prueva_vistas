<?php

namespace App\Livewire\Users;

use App\Http\Middleware\VerifyUserSessionAndIp;
use App\Services\UserService;
use App\Support\WithUnifiedPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Usuarios RBAC')]
class Index extends Component
{
    use WithUnifiedPagination;

    public function inhabilitar(string $id, UserService $service): void
    {
        abort_unless(auth()->user()->hasPermission('users.delete'), 403);
        $service->inhabilitar($id, (string) auth()->id());
        session()->flash('status', 'Usuario inhabilitado (is_deleted: true).');
    }

    public function forceLogout(string $id, UserService $service): void
    {
        abort_unless(auth()->user()->hasPermission('users.force_logout'), 403);
        $service->forceLogout(
            $service->find($id),
            VerifyUserSessionAndIp::clientIp(request()),
            substr((string) request()->userAgent(), 0, 500),
        );
        session()->flash('status', 'force_logout activado.');
    }

    public function unblock(string $id, UserService $service): void
    {
        abort_unless(auth()->user()->hasPermission('users.edit'), 403);
        $service->unblock($service->find($id));
        session()->flash('status', 'Usuario desbloqueado.');
    }

    public function render(UserService $service)
    {
        return view('livewire.users.index', [
            'users' => $this->paginateCollection($service->list()),
        ]);
    }
}
