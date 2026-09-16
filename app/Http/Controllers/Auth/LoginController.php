<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\VerifyUserSessionAndIp;
use App\Models\User;
use App\Models\UserLoginLog;
use App\Support\IpRange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login', [
            'currentIp' => VerifyUserSessionAndIp::clientIp(request()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = strtolower(trim($credentials['email']));
        $clientIp = VerifyUserSessionAndIp::clientIp($request);
        $userAgent = substr((string) $request->userAgent(), 0, 500);

        $user = User::withTrashed()->where('email', $email)->first();

        if (! $user) {
            UserLoginLog::create([
                'user_id' => null,
                'user_email' => $email,
                'ip_address' => $clientIp,
                'user_agent' => $userAgent,
                'status' => 'BLOCKED',
                'reason' => 'Credenciales inválidas: Usuario no encontrado en el sistema.',
            ]);

            return back()->withErrors(['email' => 'Usuario o contraseña incorrectos.'])->onlyInput('email');
        }

        if ($user->trashed()) {
            UserLoginLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $clientIp,
                'user_agent' => $userAgent,
                'status' => 'BLOCKED',
                'reason' => 'Acceso rechazado: Cuenta dada de baja (soft-deleted).',
            ]);

            return back()->withErrors(['email' => 'Esta cuenta ha sido dada de baja del sistema.'])->onlyInput('email');
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            UserLoginLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $clientIp,
                'user_agent' => $userAgent,
                'status' => 'BLOCKED',
                'reason' => 'Contraseña errónea para el usuario '.$user->email,
            ]);

            return back()->withErrors(['email' => 'Usuario o contraseña incorrectos.'])->onlyInput('email');
        }

        if (! $user->is_active) {
            UserLoginLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $clientIp,
                'user_agent' => $userAgent,
                'status' => 'BLOCKED',
                'reason' => 'Acceso rechazado: cuenta inactiva.',
            ]);

            return back()->withErrors(['email' => 'Esta cuenta está inactiva o bloqueada.'])->onlyInput('email');
        }

        if (! IpRange::allows($clientIp, $user->allowed_ip_range)) {
            UserLoginLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $clientIp,
                'user_agent' => $userAgent,
                'status' => 'BLOCKED',
                'reason' => "Acceso denegado: IP de origen ({$clientIp}) fuera del rango permitido ({$user->allowed_ip_range}).",
            ]);

            return back()->withErrors([
                'email' => "Tu dirección IP ({$clientIp}) no está autorizada para acceder con este rol.",
            ])->onlyInput('email');
        }

        $user->forceFill([
            'last_login_at' => now(),
            'current_ip' => $clientIp,
            'force_logout' => false,
            'is_active' => true,
        ])->save();

        UserLoginLog::create([
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip_address' => $clientIp,
            'user_agent' => $userAgent,
            'status' => 'SUCCESS',
            'reason' => 'Inicio de sesión exitoso con verificación de IP y credenciales.',
        ]);

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        $clientIp = VerifyUserSessionAndIp::clientIp($request);

        if ($user) {
            UserLoginLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $clientIp,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'status' => 'SUCCESS',
                'reason' => 'Cierre voluntario de sesión de usuario.',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
