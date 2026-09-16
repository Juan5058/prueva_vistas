<?php

namespace App\Http\Middleware;

use App\Models\UserLoginLog;
use App\Support\IpRange;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyUserSessionAndIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->trashed() || ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('security_alert', [
                'code' => 'USER_INACTIVE_OR_DELETED',
                'message' => 'El usuario no existe, ha sido inhabilitado o dado de baja.',
            ]);
        }

        $clientIp = $this->clientIp($request);

        if ($user->force_logout) {
            UserLoginLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $clientIp,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'status' => 'BLOCKED',
                'reason' => 'SESIÓN TERMINADA REMOTAMENTE: Administrador activó la bandera force_logout.',
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('security_alert', [
                'code' => 'FORCE_LOGOUT_ACTIVE',
                'message' => 'Tu sesión ha sido finalizada remotamente por el Administrador de Seguridad.',
            ]);
        }

        if (! IpRange::allows($clientIp, $user->allowed_ip_range)) {
            $user->forceFill([
                'force_logout' => true,
                'is_active' => false,
            ])->save();

            UserLoginLog::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip_address' => $clientIp,
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'status' => 'BLOCKED',
                'reason' => "VIOLACIÓN DE SEGURIDAD IP: Intento de acceso desde IP no autorizada ({$clientIp}). Rango permitido: {$user->allowed_ip_range}. Sesión bloqueada de inmediato.",
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('security_alert', [
                'code' => 'IP_NOT_AUTHORIZED',
                'message' => "Acceso restringido: Tu dirección IP ({$clientIp}) no está autorizada para esta cuenta. Sesión bloqueada.",
            ]);
        }

        if ($user->current_ip !== $clientIp) {
            $user->forceFill(['current_ip' => $clientIp])->save();
        }

        $request->attributes->set('client_ip', $clientIp);

        return $next($request);
    }

    public static function clientIp(Request $request): string
    {
        $simulated = $request->hasSession()
            ? $request->session()->get('simulated_ip')
            : null;

        if (is_string($simulated) && $simulated !== '') {
            return IpRange::normalize($simulated);
        }

        return IpRange::normalize($request->ip() ?: '127.0.0.1');
    }
}
