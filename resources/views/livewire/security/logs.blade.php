<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="pb-4 border-b">
        <h2 class="text-xl font-bold">Bitácora de seguridad</h2>
        <p class="text-xs text-slate-500">Login, logout, IP mismatch y force_logout</p>
    </div>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-3 text-left">Evento</th>
                    <th class="p-3">Usuario</th>
                    <th class="p-3">IP</th>
                    <th class="p-3">Fecha</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr class="border-t">
                    <td class="p-3 font-semibold">{{ $log->status }}</td>
                    <td class="p-3">{{ $log->user_email }}</td>
                    <td class="p-3 font-mono text-center">{{ $log->ip_address }}</td>
                    <td class="p-3 text-center">{{ \App\Support\FormatsDate::datetime($log->created_at) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin registros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
