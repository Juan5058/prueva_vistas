<x-layouts.app>
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b">
        <div>
            <h2 class="text-xl font-bold">Usuarios y roles RBAC</h2>
            <p class="text-xs text-slate-500">Permisos atómicos, rango IP y cierre forzado</p>
        </div>
        @if(auth()->user()->hasPermission('users.create'))
            <a href="{{ route('users.create') }}" class="px-3 py-1.5 text-xs text-white bg-blue-600 rounded-lg">Nuevo usuario</a>
        @endif
    </div>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left">Nombre</th><th class="p-3">Rol</th><th class="p-3">IP</th><th class="p-3">Estado</th><th></th></tr></thead>
            <tbody>
            @foreach($users as $managed)
                <tr class="border-t">
                    <td class="p-3">
                        <div class="font-medium">{{ $managed->name }}</div>
                        <div class="text-slate-500">{{ $managed->email }}</div>
                    </td>
                    <td class="p-3 text-center uppercase">{{ $managed->role }}</td>
                    <td class="p-3 text-center font-mono">{{ $managed->allowed_ip_range }}</td>
                    <td class="p-3 text-center">
                        {{ $managed->is_active ? 'Activo' : 'Bloqueado' }}
                        @if($managed->force_logout) · force_logout @endif
                    </td>
                    <td class="p-3 text-right space-x-2">
                        @if(auth()->user()->hasPermission('users.edit'))
                            <a href="{{ route('users.edit', $managed) }}">Editar</a>
                            <form class="inline" method="POST" action="{{ route('users.unblock', $managed) }}">@csrf<button>Desbloquear</button></form>
                        @endif
                        @if(auth()->user()->hasPermission('users.force_logout'))
                            <form class="inline" method="POST" action="{{ route('users.force-logout', $managed) }}">@csrf<button class="text-amber-700">Cerrar sesión</button></form>
                        @endif
                        @if(auth()->user()->hasPermission('users.delete') && $managed->id !== auth()->id())
                            <form class="inline" method="POST" action="{{ route('users.destroy', $managed) }}" onsubmit="return confirm('¿Dar de baja al usuario?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600">Baja</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
