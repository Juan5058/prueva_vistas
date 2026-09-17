<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="folder" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Expedientes Documentales</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                    Inventario archivístico y control de acervos documentales vinculados a TRD.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->hasPermission('proceedings.create'))
                <a href="{{ route('proceedings.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Abrir expediente</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Buscador y Panel de Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-80">
                <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar radicado o nombre..." class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
            </div>
        </div>

        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Radicado</th>
                        <th class="p-3.5">Nombre</th>
                        <th class="p-3.5">Serie</th>
                        <th class="p-3.5">Estado</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($proceedings as $proceeding)
                    @php
                        $state = strtolower($proceeding->state ?? '');
                        [$stateBadge] = match(true) {
                            str_contains($state, 'abierto') || str_contains($state, 'activo') || str_contains($state, 'open')
                                => ['bg-emerald-100 text-emerald-700 border-emerald-200'],
                            str_contains($state, 'cerrado') || str_contains($state, 'closed')
                                => ['bg-slate-100 text-slate-600 border-slate-200'],
                            str_contains($state, 'archivado') || str_contains($state, 'archive')
                                => ['bg-purple-100 text-purple-700 border-purple-200'],
                            str_contains($state, 'suspendido') || str_contains($state, 'pend')
                                => ['bg-amber-100 text-amber-700 border-amber-200'],
                            default => ['bg-slate-100 text-slate-600 border-slate-200'],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5">
                            <span class="inline-flex items-center gap-1 font-mono font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md text-[11px]">
                                📁 {{ $proceeding->file_number }}
                            </span>
                        </td>
                        <td class="p-3.5 font-semibold text-slate-900">{{ $proceeding->name }}</td>
                        <td class="p-3.5 text-slate-500 text-[11px]">
                            <span class="flex items-center gap-1">
                                <span class="text-slate-400">·</span> {{ $proceeding->serie_name }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $stateBadge }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ str_contains(strtolower($proceeding->state ?? ''), 'abierto') || str_contains(strtolower($proceeding->state ?? ''), 'activo') ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $proceeding->state }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            <div class="flex justify-end gap-1">
                                <x-icon-action href="{{ route('proceedings.show', $proceeding->getKey()) }}" tooltip="Ver detalle" variant="info">
                                    <x-icon name="eye" class="w-4 h-4" />
                                </x-icon-action>
                                @if(auth()->user()->hasPermission('proceedings.edit'))
                                    <x-icon-action href="{{ route('proceedings.edit', $proceeding->getKey()) }}" tooltip="Editar">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </x-icon-action>
                                @endif
                                @if(auth()->user()->hasPermission('proceedings.delete'))
                                    <x-icon-action tooltip="Inhabilitar" variant="danger" wire:click="inhabilitar('{{ $proceeding->getKey() }}')" wire:confirm="¿Inhabilitar este expediente?">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </x-icon-action>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">No hay expedientes registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

