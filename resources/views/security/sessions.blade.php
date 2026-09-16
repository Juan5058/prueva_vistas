<x-layouts.app>
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">Monitoreo de sesiones</h2>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50">
                <tr>
                    <th class="p-3 text-left">Usuario</th>
                    <th class="p-3">IP</th>
                    <th class="p-3">Última actividad</th>
                    <th class="p-3">Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($sessions as $session)
                <tr class="border-t">
                    <td class="p-3">{{ $session->email ?? 'Anónimo' }} <span class="text-slate-500">{{ $session->role }}</span></td>
                    <td class="p-3 text-center font-mono">{{ $session->ip_address }}</td>
                    <td class="p-3 text-center">{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</td>
                    <td class="p-3 text-center">{{ $session->force_logout ? 'force_logout' : ($session->is_active ? 'Activa' : 'Inactiva') }}</td>
                    <td class="p-3 text-right">
                        @if($session->user_id && auth()->user()->hasPermission('security.force_logout'))
                            <form method="POST" action="{{ route('security.force-logout', $session->user_id) }}">
                                @csrf
                                <button class="text-rose-600">Cerrar remoto</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">No hay sesiones registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
