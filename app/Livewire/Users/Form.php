<?php

namespace App\Livewire\Users;

use App\Services\UserService;
use App\Support\RoleCatalog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Usuario')]
class Form extends Component
{
    public ?string $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = RoleCatalog::APRENDIZ;

    public string $allowed_ip_range = '*';

    public function mount(?string $user = null): void
    {
        if ($user) {
            abort_unless(auth()->user()->hasPermission('users.edit'), 403);
            $model = app(UserService::class)->find($user);
            $this->userId = (string) $model->getKey();
            $this->name = $model->name;
            $this->email = $model->email;
            $this->role = $model->role;
            $this->allowed_ip_range = $model->allowed_ip_range ?: '*';
        } else {
            abort_unless(auth()->user()->hasPermission('users.create'), 403);
        }
    }

    public function save(UserService $service)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:super_admin,lider_ambiental,aprendiz',
            'allowed_ip_range' => 'nullable|string|max:120',
            'password' => $this->userId ? 'nullable|string|min:6' : 'required|string|min:6',
        ];
        $this->validate($rules);

        if ($this->userId) {
            $service->update($this->userId, $this->only(['name', 'email', 'password', 'role', 'allowed_ip_range']));
        } else {
            $service->create($this->only(['name', 'email', 'password', 'role', 'allowed_ip_range']));
        }

        session()->flash('status', 'Usuario guardado.');

        return $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.users.form', [
            'roles' => RoleCatalog::labels(),
        ]);
    }
}
