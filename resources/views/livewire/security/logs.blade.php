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
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                {{ $log->status }}
                            </span>
                        </td>
                        <td class="p-3.5 font-medium text-slate-900">{{ $log->user_email }}</td>
                        <td class="p-3.5 font-mono text-center text-slate-700 font-semibold">{{ $log->ip_address }}</td>
                        <td class="p-3.5 text-right font-mono text-slate-600">{{ \App\Support\FormatsDate::datetime($log->created_at) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-8 text-center text-slate-500">Sin registros de eventos de seguridad.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

