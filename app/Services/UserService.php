<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\SecurityRepository;
use App\Repositories\UserRepository;
use App\Support\RoleCatalog;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function __construct(
        private UserRepository $users,
        private SecurityRepository $security,
    ) {}

    public function list(): Collection
    {
        return $this->users->all();
    }

    public function find(string $id): User
    {
        return $this->users->find($id);
    }

    public function create(array $payload): User
    {
        $role = $payload['role'];

        return $this->users->create([
            'name' => $payload['name'],
            'email' => strtolower($payload['email']),
            'password' => $payload['password'],
            'role' => $role,
            'roles' => [$role],
            'permissions' => RoleCatalog::forRole($role),
            'allowed_ip_range' => $payload['allowed_ip_range'] ?: '*',
            'current_ip' => '127.0.0.1',
            'is_active' => true,
            'force_logout' => false,
            'is_deleted' => false,
        ]);
    }

    public function update(string $id, array $payload): User
    {
        $user = $this->users->find($id);
        $data = [
            'name' => $payload['name'],
            'email' => strtolower($payload['email']),
            'role' => $payload['role'],
            'roles' => [$payload['role']],
            'permissions' => RoleCatalog::forRole($payload['role']),
            'allowed_ip_range' => $payload['allowed_ip_range'] ?: '*',
        ];

        if (filled($payload['password'] ?? null)) {
            $data['password'] = $payload['password'];
        }

        return $this->users->update($user, $data);
    }

    public function inhabilitar(string $id, string $actorId): void
    {
        if ($id === $actorId) {
            throw ValidationException::withMessages([
                'user' => 'No puede inhabilitar su propia cuenta.',
            ]);
        }

        $this->users->softDelete($this->users->find($id));
    }

    public function forceLogout(User $user, string $ip, string $agent): void
    {
        $user->force_logout = true;
        $user->save();

        $this->security->recordLoginLog([
            'user_id' => $user->getKey(),
            'user_email' => $user->email,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'status' => 'SUCCESS',
            'reason' => 'Administrador activó cierre forzado de sesión.',
        ]);
    }

    public function unblock(User $user): void
    {
        $user->force_logout = false;
        $user->is_active = true;
        $user->save();
    }
}
