<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TRD & Gestión') · Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
@php
    $user = auth()->user();
    $nav = request()->route()?->getName() ?? '';
    $isTrd = str_starts_with($nav, 'trd.') && $nav !== 'trd.import';
    $isProceedings = str_starts_with($nav, 'proceedings.');
    $isDocuments = str_starts_with($nav, 'documents.');
    $isUsers = str_starts_with($nav, 'users.');
    $simulated = session('simulated_ip');
    $currentIp = $currentIp ?? \App\Http\Middleware\VerifyUserSessionAndIp::clientIp(request());
@endphp
<div class="min-h-screen flex flex-col">
    <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-30">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-slate-700 tracking-tight">SISTEMA TRD EN LÍNEA</span>
            </div>
            <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-xs text-slate-600 font-mono">
                <x-icon name="wifi" class="w-3.5 h-3.5 text-blue-600" />
                <span>IP Origen:</span>
                <span class="font-semibold text-slate-800">{{ $currentIp ?? '127.0.0.1' }}</span>
                @if($simulated)
                    <span class="bg-amber-100 text-amber-800 text-[10px] px-1.5 rounded font-sans font-medium">Simulada</span>
                @endif
            </div>
            <button type="button" id="btn-open-ip-tester" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-medium">
                <x-icon name="shield" class="w-3.5 h-3.5" />
                Probar Validación de IP
            </button>
        </div>
        <div class="flex items-center gap-4">
            <div class="hidden md:block text-right">
                <div class="text-xs font-semibold text-slate-900">{{ $user->name }}</div>
                <div class="text-[11px] text-slate-500 flex items-center justify-end gap-1.5 mt-0.5">
                    <span class="bg-blue-50 text-blue-700 border border-blue-200 px-1.5 rounded text-[10px] font-medium uppercase">{{ $user->role }}</span>
                    <span>{{ $user->email }}</span>
                </div>
            </div>
            <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs uppercase">
                {{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}" class="border-l border-slate-200 pl-3">
                @csrf
                <button class="p-2 rounded-lg text-rose-600 hover:bg-rose-50" title="Cerrar sesión">
                    <x-icon name="logout" class="w-4 h-4" />
                </button>
            </form>
        </div>
    </header>

    <div class="flex-1 flex">
        <aside class="w-64 bg-slate-900 text-slate-200 min-h-[calc(100vh-4rem)] flex flex-col border-r border-slate-800">
            <div class="p-5 border-b border-slate-800 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white">
                    <x-icon name="book" class="w-5 h-5" />
                </div>
                <div>
                    <h1 class="font-semibold text-sm tracking-wide text-white leading-tight">TRD & GESTIÓN</h1>
                    <p class="text-[11px] text-slate-400 font-medium">Inventario Documental</p>
                </div>
            </div>
            <nav class="flex-1 p-3 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ $nav === 'dashboard' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-icon name="dashboard" class="w-4 h-4" /> Dashboard Limpio
                </a>
                <p class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Inventario y TRD</p>
                @if($user->hasPermission('trd.view'))
                    <a href="{{ route('trd.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $isTrd ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <x-icon name="file" class="w-4 h-4 text-slate-400" /> Estructuras TRD
                    </a>
                @endif
                @if($user->hasPermission('proceedings.view'))
                    <a href="{{ route('proceedings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $isProceedings ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <x-icon name="folder" class="w-4 h-4 text-slate-400" /> Expedientes
                    </a>
                @endif
                @if($user->hasPermission('documents.view'))
                    <a href="{{ route('documents.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $isDocuments ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <x-icon name="file" class="w-4 h-4 text-slate-400" /> Documentos
                    </a>
                @endif
                @if($user->hasPermission('trd.import'))
                    <a href="{{ route('trd.import') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $nav === 'trd.import' ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <x-icon name="upload" class="w-4 h-4 text-slate-400" /> Carga Masiva TRD
                    </a>
                @endif
                <p class="px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">Seguridad & Sesiones</p>
                @if($user->hasPermission('security.view_sessions'))
                    <a href="{{ route('security.sessions') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $nav === 'security.sessions' ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <x-icon name="activity" class="w-4 h-4 text-slate-400" /> Monitoreo de Sesiones
                    </a>
                    <a href="{{ route('security.logs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $nav === 'security.logs' ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <x-icon name="shield" class="w-4 h-4 text-slate-400" /> Logs y Auditoría IP
                    </a>
                @endif
                @if($user->hasPermission('users.view'))
                    <a href="{{ route('users.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $isUsers ? 'bg-blue-600/20 text-blue-300 border border-blue-500/30' : 'text-slate-300 hover:bg-slate-800' }}">
                        <x-icon name="users" class="w-4 h-4 text-slate-400" /> Usuarios y Roles RBAC
                    </a>
                @endif
            </nav>
            <div class="p-3 border-t border-slate-800 bg-slate-950/40 text-[11px] text-slate-400 flex items-center justify-between">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    SQLite + Eloquent
                </span>
                <span class="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded">Soft Deletes</span>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto bg-slate-50">
            @if(session('status'))
                <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="mx-6 mt-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>

<div id="ip-modal" class="hidden fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 border border-slate-200">
        <h3 class="text-base font-bold text-slate-900 mb-1">Simulador de Seguridad de IP</h3>
        <p class="text-xs text-slate-500 mb-4">Si la IP queda fuera del rango, el middleware cierra la sesión y registra BLOCKED.</p>
        <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-4">
            IP autorizada: <span class="font-mono text-emerald-700">{{ $user->allowed_ip_range }}</span>
        </p>
        <form method="POST" action="{{ route('security.simulate-ip') }}" class="space-y-3">
            @csrf
            <input name="simulated_ip" id="input-simulated-ip" value="{{ $simulated ?: $currentIp }}" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg font-mono">
            <div class="flex flex-wrap gap-2">
                <button type="button" data-ip="127.0.0.1" class="ip-preset px-2.5 py-1 text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">IP Local</button>
                <button type="button" data-ip="192.168.1.55" class="ip-preset px-2.5 py-1 text-[11px] bg-blue-50 text-blue-700 border border-blue-200 rounded-md">Intranet</button>
                <button type="button" data-ip="201.55.99.1" class="ip-preset px-2.5 py-1 text-[11px] bg-rose-50 text-rose-700 border border-rose-200 rounded-md font-semibold">IP no autorizada</button>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" id="btn-close-ip-modal" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Cancelar</button>
                <button class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-600 rounded-lg">Aplicar IP y Probar</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
