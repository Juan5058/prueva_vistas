<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="users-rbac" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Usuarios y Roles RBAC</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                    Gestión de accesos, asignación de roles (Super Admin, Líder Ambiental, Aprendiz) y políticas de seguridad IP.
                </p>
            </div>
        </div>
        <div>
            @if(auth()->user()->hasPermission('users.create'))
                <a href="{{ route('users.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Nuevo usuario</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Contenedor de Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Nombre</th>
                        <th class="p-3.5">Correo Electrónico</th>
                        <th class="p-3.5 text-center">Rol Asignado</th>
                        <th class="p-3.5 text-center">Estado</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5 font-semibold text-slate-900">{{ $user->name }}</td>
                        <td class="p-3.5 text-slate-600 font-mono">{{ $user->email }}</td>
                        <td class="p-3.5 text-center">
                            @php
                                $roleLabel = \App\Support\RoleCatalog::label($user->role);
                                $roleBadge = match($user->role) {
                                    'super_admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'lider_ambiental' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    default => 'bg-amber-50 text-amber-700 border-amber-200'
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-semibold uppercase tracking-wide {{ $roleBadge }}">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td class="p-3.5 text-center">
                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5">
                            <div class="flex justify-end gap-1">
                                @if(auth()->user()->hasPermission('users.edit'))
                                    <x-icon-action href="{{ route('users.edit', $user->getKey()) }}" tooltip="Editar usuario" variant="neutral">
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
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">No hay usuarios registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
            <x-table-pagination :paginator="$users" :perPage="$perPage" :customPerPage="$customPerPage" />
        </div>
    </div>
</div>

