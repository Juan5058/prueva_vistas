<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserLoginLog;
use App\Support\RoleCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->latest()->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.form', [
            'managedUser' => new User(['role' => 'consulta', 'allowed_ip_range' => '*', 'is_active' => true]),
            'roles' => array_keys(RoleCatalog::defaults()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $role = $data['role'];

        User::query()->create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
            'role' => $role,
            'roles' => [$role],
            'permissions' => RoleCatalog::forRole($role),
            'allowed_ip_range' => $data['allowed_ip_range'] ?: '*',
            'current_ip' => '127.0.0.1',
            'is_active' => true,
            'force_logout' => false,
        ]);

        return redirect()->route('users.index')->with('status', 'Usuario creado con permisos RBAC.');
    }

    public function edit(User $user): View
    {
        return view('users.form', [
            'managedUser' => $user,
            'roles' => array_keys(RoleCatalog::defaults()),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user->id);
        $payload = [
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'roles' => [$data['role']],
            'permissions' => RoleCatalog::forRole($data['role']),
            'allowed_ip_range' => $data['allowed_ip_range'] ?: '*',
        ];

        if (filled($data['password'] ?? null)) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()->route('users.index')->with('status', 'Usuario actualizado.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === request()->user()->id) {
            return back()->withErrors(['user' => 'No puede eliminar su propia cuenta.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'Usuario dado de baja (soft delete).');
    }

    public function forceLogout(Request $request, User $user): RedirectResponse
    {
        $user->forceFill(['force_logout' => true])->save();

        UserLoginLog::create([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip_address' => $request->ip() ?: '127.0.0.1',
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'status' => 'SUCCESS',
            'reason' => 'Administrador activó cierre forzado de sesión.',
        ]);

        return back()->with('status', 'Se activó force_logout para '.$user->email);
    }

    public function unblock(User $user): RedirectResponse
    {
        $user->forceFill([
            'force_logout' => false,
            'is_active' => true,
        ])->save();

        return back()->with('status', 'Usuario desbloqueado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $passwordRule = $ignoreId ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'];

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'.($ignoreId ? ','.$ignoreId : '')],
            'password' => $passwordRule,
            'role' => ['required', 'in:admin,archivista,auditor,consulta'],
            'allowed_ip_range' => ['nullable', 'string', 'max:120'],
        ]);
    }
}
