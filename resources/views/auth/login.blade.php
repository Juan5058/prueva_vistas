<x-layouts.guest>
@section('title', 'Ingreso TRD')
<div class="min-h-screen flex flex-col justify-center items-center p-4 relative">
    <div class="w-full max-w-md relative z-10">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 text-white mb-3">
                <x-icon name="book" class="w-7 h-7" />
            </div>
            <h1 class="text-xl font-bold text-white tracking-tight">Sistema de Gestión e Inventario TRD</h1>
            <p class="text-xs text-slate-400 mt-1">Tablas de Retención Documental · Laravel 13</p>
        </div>

        @if(session('security_alert'))
            <div class="mb-5 p-4 rounded-2xl bg-white border-2 border-rose-500 text-center">
                <h2 class="text-sm font-bold text-slate-900 mb-2">
                    {{ session('security_alert.code') === 'IP_NOT_AUTHORIZED' ? 'ALERTA DE SEGURIDAD: IP NO AUTORIZADA' : 'SESIÓN FINALIZADA POR SEGURIDAD' }}
                </h2>
                <p class="text-xs text-rose-800 font-mono bg-rose-50 border border-rose-200 rounded-xl p-3">{{ session('security_alert.message') }}</p>
            </div>
        @endif

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-8">
            @if($errors->any())
                <div class="mb-5 p-3 rounded-xl bg-rose-950/50 border border-rose-800/80 text-rose-300 text-xs">
                    <span class="font-semibold block text-rose-200">Acceso Denegado</span>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-4" id="login-form">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Correo Electrónico Institucional</label>
                    <input id="login-input-email" name="email" type="email" required value="{{ old('email', 'admin@trd.gob') }}"
                           class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">Contraseña de Acceso</label>
                    <input id="login-input-password" name="password" type="password" required
                           class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-700 rounded-xl text-white text-xs">
                </div>
                <div class="p-2.5 rounded-lg bg-slate-950/50 border border-slate-800/80 text-[11px] text-slate-400 flex items-center justify-between font-mono">
                    <span>IP Solicitante:</span>
                    <span class="text-blue-400 font-semibold">{{ $currentIp ?? '127.0.0.1' }}</span>
                </div>
                <button class="w-full mt-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl flex items-center justify-center gap-2">
                    Ingresar al Sistema TRD
                    <x-icon name="arrow" class="w-3.5 h-3.5" />
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-800">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5">Cuentas de demostración (RBAC)</p>
                <div class="space-y-2">
                    @foreach([
                        ['admin@trd.gob', 'admin123', 'Super Admin', 'Todos los permisos', 'blue'],
                        ['lider@trd.gob', 'lider123', 'Líder Ambiental', 'Gestión TRD y documentos', 'emerald'],
                        ['aprendiz@trd.gob', 'aprendiz123', 'Aprendiz', 'Consulta y bitácoras', 'amber'],
                    ] as [$email, $pass, $label, $hint, $color])
                        <button type="button" class="quick-login w-full text-left p-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 border border-slate-700/60 text-xs flex items-center justify-between"
                                data-email="{{ $email }}" data-password="{{ $pass }}">
                            <div>
                                <div class="text-slate-200 font-medium">{{ $label }}</div>
                                <div class="text-[10px] text-slate-400">{{ $email }} · {{ $hint }}</div>
                            </div>
                            <span class="text-[10px] font-mono bg-{{ $color }}-500/20 text-{{ $color }}-300 px-1.5 py-0.5 rounded">{{ $pass }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
        <p class="mt-4 text-center text-[11px] text-slate-500 flex items-center justify-center gap-1.5">
            <x-icon name="shield" class="w-3.5 h-3.5 text-blue-500" />
            Acceso protegido por middleware VerifyUserSessionAndIp
        </p>
    </div>
</div>
</x-layouts.guest>
