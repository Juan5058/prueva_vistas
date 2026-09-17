<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="shield" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Sesiones Activas</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Monitoreo de conexiones de usuarios y cierre forzado de sesión (force_logout).</p>
            </div>
        </div>
    </div>

    {{-- Contenedor de Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden p-6">
        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Usuario</th>
                        <th class="p-3.5 text-center">Última Dirección IP</th>
                        <th class="p-3.5 text-center">Último Acceso</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($sessions as $session)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5">
                            <p class="font-semibold text-slate-900">{{ $session->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ $session->email }}</p>
                        </td>
                        <td class="p-3.5 text-center font-mono text-slate-700 font-semibold">{{ $session->current_ip ?: '—' }}</td>
                        <td class="p-3.5 text-center font-mono text-slate-600">{{ \App\Support\FormatsDate::datetime($session->last_login_at) }}</td>
                        <td class="p-3.5">
                            <div class="flex justify-end">
                                @if($session->getKey() !== auth()->id())
                                    <x-icon-action tooltip="Cerrar sesión forzada" variant="danger" wire:click="forceLogout('{{ $session->getKey() }}')" wire:confirm="¿Cerrar la sesión de este usuario?">
                                        <x-icon name="logout" class="w-4 h-4" />
                                    </x-icon-action>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Tu sesión actual
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-8 text-center text-slate-500">No hay sesiones activas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

