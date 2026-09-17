<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <h2 class="text-xl font-bold">Usuarios</h2>
            <p class="text-xs text-slate-500">Roles: Super Admin, Líder Ambiental, Aprendiz</p>
        </div>
        @if(auth()->user()->hasPermission('users.create'))
            <a href="{{ route('users.create') }}" wire:navigate class="px-3 py-1.5 text-xs text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Nuevo usuario</a>
        @endif
    </div>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Correo</th>
                    <th class="p-3">Rol</th>
                    <th class="p-3">Estado</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr class="border-t">
                    <td class="p-3 font-medium">{{ $user->name }}</td>
                    <td class="p-3">{{ $user->email }}</td>
                    <td class="p-3 text-center">{{ \App\Support\RoleCatalog::label($user->role) }}</td>
                    <td class="p-3 text-center">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</td>
                    <td class="p-3">
                        <div class="flex justify-end gap-1">
                            @if(auth()->user()->hasPermission('users.edit'))
                                <x-icon-action href="{{ route('users.edit', $user->getKey()) }}" tooltip="Editar usuario">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                            @if(auth()->user()->hasPermission('users.force_logout') && $user->getKey() !== auth()->id())
                                <x-icon-action tooltip="Cerrar sesión forzada" variant="warning" wire:click="forceLogout('{{ $user->getKey() }}')" wire:confirm="¿Activar force_logout en este usuario?">
                                    <x-icon name="logout" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                            @if(auth()->user()->hasPermission('users.delete') && $user->getKey() !== auth()->id())
                                <x-icon-action tooltip="Inhabilitar" variant="danger" wire:click="inhabilitar('{{ $user->getKey() }}')" wire:confirm="¿Inhabilitar este usuario?">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">No hay usuarios.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
