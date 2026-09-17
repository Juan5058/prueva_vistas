<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="sessions" class="w-6 h-6" />
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
                    @php
                        $isMe = $session->getKey() === auth()->id();
                        $initials = collect(explode(' ', $session->name))->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors {{ $isMe ? 'bg-emerald-50/20' : '' }}">
                        <td class="p-3.5">
                            <div class="flex items-center gap-3">
                                {{-- Avatar de iniciales --}}
                                <div class="w-8 h-8 rounded-full bg-slate-800 text-white flex items-center justify-center text-[11px] font-bold shrink-0 shadow-sm">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900 flex items-center gap-1.5">
                                        {{ $session->name }}
                                        @if($isMe)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Tú
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-[11px] text-slate-400 font-mono">{{ $session->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-3.5 text-center">
                            @if($session->current_ip)
                                <span class="inline-flex items-center gap-1.5 font-mono text-slate-700 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-md text-[11px] font-semibold">
                                    <x-icon name="globe" class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                    <span>{{ $session->current_ip }}</span>
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px]">—</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center font-mono text-slate-500 text-[11px]">{{ \App\Support\FormatsDate::datetime($session->last_login_at) }}</td>
                        <td class="p-3.5">
                            <div class="flex justify-end">
                                @if(!$isMe)
                                    <x-icon-action tooltip="Cerrar sesión forzada" variant="danger" wire:click="forceLogout('{{ $session->getKey() }}')" wire:confirm="¿Cerrar la sesión de este usuario?">
                                        <x-icon name="logout" class="w-4 h-4" />
                                    </x-icon-action>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Sesión activa
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
            <x-table-pagination :paginator="$sessions" :perPage="$perPage" :customPerPage="$customPerPage" />
        </div>
    </div>
</div>

