<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="pb-4 border-b">
        <h2 class="text-xl font-bold">Sesiones activas</h2>
        <p class="text-xs text-slate-500">Cierre forzado invalida la sesión y registra force_logout</p>
    </div>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-3 text-left">Usuario</th>
                    <th class="p-3">Última IP</th>
                    <th class="p-3">Último acceso</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sessions as $session)
                <tr class="border-t">
                    <td class="p-3">
                        <p class="font-medium">{{ $session->name }}</p>
                        <p class="text-slate-500">{{ $session->email }}</p>
                    </td>
                    <td class="p-3 text-center font-mono">{{ $session->current_ip }}</td>
                    <td class="p-3 text-center">{{ \App\Support\FormatsDate::datetime($session->last_login_at) }}</td>
                    <td class="p-3">
                        <div class="flex justify-end">
                            @if($session->getKey() !== auth()->id())
                                <x-icon-action tooltip="Cerrar sesión forzada" variant="danger" wire:click="forceLogout('{{ $session->getKey() }}')" wire:confirm="¿Cerrar la sesión de este usuario?">
                                    <x-icon name="logout" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6 text-center text-slate-500">No hay sesiones activas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
