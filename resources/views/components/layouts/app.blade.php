<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TRD & Gestión' }} · Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased" x-data="{
    inventory: true,
    security: true,
    analytics: true,
    ipModal: false,
    collapsed: localStorage.getItem('trd.sidebar.collapsed') === '1',
    toggleSidebar() {
        this.collapsed = !this.collapsed;
        localStorage.setItem('trd.sidebar.collapsed', this.collapsed ? '1' : '0');
    }
}">
@php
    $user = auth()->user();
    $nav = request()->route()?->getName() ?? '';
    $isTrd = str_starts_with($nav, 'trd.') && $nav !== 'trd.import';
    $isProceedings = str_starts_with($nav, 'proceedings.');
    $isDocuments = str_starts_with($nav, 'documents.');
    $isUsers = str_starts_with($nav, 'users.');
    $isReports = str_starts_with($nav, 'reports.');
    $simulated = session('simulated_ip');
    $currentIp = $currentIp ?? \App\Http\Middleware\VerifyUserSessionAndIp::clientIp(request());
    $navClass = function (bool $active) {
        return $active
            ? 'bg-slate-800 text-white'
            : 'text-slate-300 hover:bg-slate-800 hover:text-white';
    };
@endphp
{{-- Layout: sidebar full-height | header + content in right column --}}
<div class="flex h-screen overflow-hidden">

    {{-- ═══════════════════════════════════════════════════════════
         SIDEBAR — full viewport height, toggle via logo icon
    ════════════════════════════════════════════════════════════ --}}
    <aside class="bg-slate-900 text-slate-200 h-screen flex flex-col border-r border-slate-800 transition-all duration-200 shrink-0 z-20"
           :class="collapsed ? 'w-[4.5rem]' : 'w-64'">

        {{-- Logo / Brand — clicking it toggles the sidebar --}}
        <button type="button"
                @click="toggleSidebar()"
                class="p-4 border-b border-slate-800 flex items-center gap-3 w-full text-left hover:bg-slate-800/50 transition-colors"
                :class="collapsed && 'justify-center'"
                :title="collapsed ? 'Expandir menú' : 'Plegar menú'">
            <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white shrink-0">
                <x-icon name="book" class="w-5 h-5" />
            </div>
            <div x-show="!collapsed" x-cloak>
                <h1 class="font-semibold text-sm tracking-wide text-white leading-tight">TRD & GESTIÓN</h1>
                <p class="text-[11px] text-slate-400 font-medium">Inventario Documental</p>
            </div>
        </button>

        {{-- Navigation --}}
        <nav class="flex-1 p-3 space-y-1.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}" wire:navigate title="Dashboard Limpio"
               class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ $nav === 'dashboard' ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}"
               :class="collapsed && 'justify-center px-2'">
                <x-icon name="dashboard" class="w-4 h-4 shrink-0" />
                <span x-show="!collapsed" x-cloak>Dashboard Limpio</span>
            </a>

            <button type="button" x-show="!collapsed" x-cloak @click="inventory = !inventory"
                    class="w-full flex items-center justify-between px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span>Inventario y TRD</span>
                <span x-text="inventory ? '−' : '+'"></span>
            </button>
            <div x-show="collapsed || inventory" x-cloak class="space-y-1">
                @if($user->hasPermission('trd.view'))
                    <a href="{{ route('trd.index') }}" wire:navigate title="Estructuras TRD"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($isTrd) }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="file" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Estructuras TRD</span>
                    </a>
                @endif
                @if($user->hasPermission('proceedings.view'))
                    <a href="{{ route('proceedings.index') }}" wire:navigate title="Expedientes"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($isProceedings) }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="folder" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Expedientes</span>
                    </a>
                @endif
                @if($user->hasPermission('documents.view'))
                    <a href="{{ route('documents.index') }}" wire:navigate title="Documentos"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($isDocuments) }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="file" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Documentos</span>
                    </a>
                @endif
                @if($user->hasPermission('trd.import'))
                    <a href="{{ route('trd.import') }}" wire:navigate title="Carga Masiva TRD"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($nav === 'trd.import') }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="upload" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Carga Masiva TRD</span>
                    </a>
                @endif
            </div>

            @if($user->hasPermission('reports.view'))
                <button type="button" x-show="!collapsed" x-cloak @click="analytics = !analytics"
                        class="w-full flex items-center justify-between px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    <span>Analítica</span>
                    <span x-text="analytics ? '−' : '+'"></span>
                </button>
                <div x-show="collapsed || analytics" x-cloak class="space-y-1">
                    <a href="{{ route('reports.index') }}" wire:navigate title="Reportes PDF"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($isReports) }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="chart" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Reportes PDF</span>
                    </a>
                </div>
            @endif

            <button type="button" x-show="!collapsed" x-cloak @click="security = !security"
                    class="w-full flex items-center justify-between px-3 pt-3 pb-1 text-xs font-semibold uppercase tracking-wider text-slate-400">
                <span>Seguridad & Sesiones</span>
                <span x-text="security ? '−' : '+'"></span>
            </button>
            <div x-show="collapsed || security" x-cloak class="space-y-1">
                @if($user->hasPermission('security.view_sessions'))
                    <a href="{{ route('security.sessions') }}" wire:navigate title="Monitoreo de Sesiones"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($nav === 'security.sessions') }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="activity" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Monitoreo de Sesiones</span>
                    </a>
                    <a href="{{ route('security.logs') }}" wire:navigate title="Logs y Auditoría IP"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($nav === 'security.logs') }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="shield" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Logs y Auditoría IP</span>
                    </a>
                @endif
                @if($user->hasPermission('users.view'))
                    <a href="{{ route('users.index') }}" wire:navigate title="Usuarios y Roles RBAC"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-md text-xs font-medium {{ $navClass($isUsers) }}"
                       :class="collapsed && 'justify-center px-2'">
                        <x-icon name="users" class="w-4 h-4 text-slate-400 shrink-0" />
                        <span x-show="!collapsed" x-cloak>Usuarios y Roles RBAC</span>
                    </a>
                @endif
            </div>
        </nav>

        {{-- Footer status bar --}}
        <div class="p-3 border-t border-slate-800 bg-slate-950/40 text-[11px] text-slate-400 flex items-center"
             :class="collapsed ? 'justify-center' : 'justify-between'">
            <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span x-show="!collapsed" x-cloak>MongoDB 7 + Redis</span>
            </span>
            <span x-show="!collapsed" x-cloak class="text-[10px] font-mono bg-slate-800 px-1.5 py-0.5 rounded">is_deleted</span>
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════════════
         RIGHT COLUMN — header (starts after sidebar) + main content
    ════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col flex-1 min-w-0 h-screen">

        {{-- Header — only spans the right column --}}
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 z-10">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-semibold text-slate-700 tracking-tight">SISTEMA TRD EN LÍNEA</span>
                </div>
                <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 border border-slate-200 text-xs text-slate-600 font-mono">
                    <x-icon name="wifi" class="w-3.5 h-3.5 text-blue-600" />
                    <span>IP Origen:</span>
                    <span class="font-semibold text-slate-800">{{ $currentIp }}</span>
                    @if($simulated)
                        <span class="bg-amber-100 text-amber-800 text-[10px] px-1.5 rounded font-sans font-medium">Simulada</span>
                    @endif
                </div>
                <button type="button" @click="ipModal = true"
                        class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-medium">
                    <x-icon name="shield" class="w-3.5 h-3.5" />
                    Probar Validación de IP
                </button>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:block text-right">
                    <div class="text-xs font-semibold text-slate-900">{{ $user->name }}</div>
                    <div class="text-[11px] text-slate-500 flex items-center justify-end gap-1.5 mt-0.5">
                        <span class="bg-blue-50 text-blue-700 border border-blue-200 px-1.5 rounded text-[10px] font-medium uppercase">{{ $user->roleLabel() }}</span>
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

        {{-- Main scrollable content --}}
        <main class="flex-1 overflow-y-auto bg-slate-50">
            @if(session('status'))
                <div class="mx-6 mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs">{{ session('status') }}</div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>

<div x-show="ipModal" x-cloak class="fixed inset-0 bg-slate-900/50 flex items-center justify-center z-50 p-4" @keydown.escape.window="ipModal = false">
    <div class="bg-white rounded-xl max-w-md w-full p-6 border border-slate-200" @click.outside="ipModal = false">
        <h3 class="text-base font-bold text-slate-900 mb-1">Simulador de Seguridad de IP</h3>
        <p class="text-xs text-slate-500 mb-4">Si la IP queda fuera del rango, el middleware cierra la sesión y registra BLOCKED.</p>
        <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200 mb-4">
            IP autorizada: <span class="font-mono text-emerald-700">{{ $user->allowed_ip_range }}</span>
        </p>
        <form method="POST" action="{{ route('security.simulate-ip') }}" class="space-y-3">
            @csrf
            <input name="simulated_ip" x-ref="ip" value="{{ $simulated ?: $currentIp }}" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg font-mono">
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="$refs.ip.value = '127.0.0.1'" class="px-2.5 py-1 text-[11px] bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md">IP Local</button>
                <button type="button" @click="$refs.ip.value = '192.168.1.55'" class="px-2.5 py-1 text-[11px] bg-slate-100 text-slate-700 border border-slate-200 rounded-md">Intranet</button>
                <button type="button" @click="$refs.ip.value = '201.55.99.1'" class="px-2.5 py-1 text-[11px] bg-rose-50 text-rose-700 border border-rose-200 rounded-md font-semibold">IP no autorizada</button>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" @click="ipModal = false" class="px-3 py-1.5 text-xs text-slate-600 hover:bg-slate-100 rounded-lg">Cancelar</button>
                <button class="px-4 py-1.5 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Aplicar IP y Probar</button>
            </div>
        </form>
    </div>
</div>
<style>[x-cloak]{display:none !important;}</style>
@livewireScripts
</body>
</html>
