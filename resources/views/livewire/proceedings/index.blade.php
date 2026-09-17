<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <h2 class="text-xl font-bold">Expedientes</h2>
            <p class="text-xs text-slate-500">Inventario archivístico vinculado a TRD</p>
        </div>
        @if(auth()->user()->hasPermission('proceedings.create'))
            <a href="{{ route('proceedings.create') }}" wire:navigate class="px-3 py-1.5 text-xs text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Abrir expediente</a>
        @endif
    </div>
    <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar radicado o nombre" class="px-3 py-2 text-xs border rounded-lg w-72">
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-3 text-left">Radicado</th>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Serie</th>
                    <th class="p-3 text-left">Estado</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($proceedings as $proceeding)
                <tr class="border-t">
                    <td class="p-3 font-mono">{{ $proceeding->file_number }}</td>
                    <td class="p-3 font-medium">{{ $proceeding->name }}</td>
                    <td class="p-3">{{ $proceeding->serie_name }}</td>
                    <td class="p-3">{{ $proceeding->state }}</td>
                    <td class="p-3">
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
                <tr><td colspan="5" class="p-6 text-center text-slate-500">No hay expedientes.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
