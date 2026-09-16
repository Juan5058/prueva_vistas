<x-layouts.app>
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">Logs y auditoría IP</h2>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left">Fecha</th>
                    <th class="p-3">Usuario</th>
                    <th class="p-3">IP</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3 text-left">Motivo</th>
                </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr class="border-t {{ $log->status === 'BLOCKED' ? 'bg-rose-50' : '' }}">
                    <td class="p-3 whitespace-nowrap">{{ $log->created_at }}</td>
                    <td class="p-3">{{ $log->user_email }}</td>
                    <td class="p-3 font-mono text-center">{{ $log->ip_address }}</td>
                    <td class="p-3 text-center font-semibold">{{ $log->status }}</td>
                    <td class="p-3">{{ $log->reason }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin registros.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
