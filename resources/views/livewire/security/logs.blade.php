<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner Header --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="shield" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Bitácora de Seguridad</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Registro de eventos de autenticación (Login, logout, IP mismatch y force_logout).</p>
            </div>
        </div>
    </div>

    {{-- Contenedor de Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden p-6">
        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Evento / Estado</th>
                        <th class="p-3.5">Usuario</th>
                        <th class="p-3.5 text-center">Dirección IP</th>
                        <th class="p-3.5 text-right">Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                    @php
                        $status = strtolower($log->status ?? '');
                        $isBlocked  = str_contains($status, 'block') || str_contains($status, 'deni');
                        $isSuccess  = str_contains($status, 'success') || str_contains($status, 'login') || $status === 'success';
                        $isMismatch = str_contains($status, 'mismatch') || str_contains($status, 'ip');
                        $isLogout   = str_contains($status, 'logout') || str_contains($status, 'force');
                        [$badge, $rowBg, $dot, $icon] = match(true) {
                            $isBlocked  => ['bg-rose-100 text-rose-700 border-rose-200',   'bg-rose-50/30',    'bg-rose-500',    '🚫'],
                            $isSuccess  => ['bg-emerald-100 text-emerald-700 border-emerald-200', 'bg-emerald-50/20', 'bg-emerald-500', '✔'],
                            $isMismatch => ['bg-amber-100 text-amber-700 border-amber-200', 'bg-amber-50/20',   'bg-amber-500',   '⚠'],
                            $isLogout   => ['bg-slate-100 text-slate-600 border-slate-200', '',                 'bg-slate-400',   '↩'],
                            default     => ['bg-slate-100 text-slate-700 border-slate-200', '',                 'bg-slate-400',   '•'],
                        };
                    @endphp
                    <tr class="transition-colors {{ $rowBg }} hover:brightness-95">
                        <td class="p-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $badge }}">
                                <span class="text-[10px]">{{ $icon }}</span>
                                {{ $log->status }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            <span class="font-medium text-slate-900">{{ $log->user_email }}</span>
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="font-mono text-slate-700 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md text-[11px] font-semibold">{{ $log->ip_address }}</span>
                        </td>
                        <td class="p-3.5 text-right font-mono text-slate-500 text-[11px]">{{ \App\Support\FormatsDate::datetime($log->created_at) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-8 text-center text-slate-500">Sin registros de eventos de seguridad.</td></tr>
                @endforelse
                </tbody>
            </table>
            <x-table-pagination :paginator="$logs" :perPage="$perPage" :customPerPage="$customPerPage" />
        </div>
    </div>
</div>

