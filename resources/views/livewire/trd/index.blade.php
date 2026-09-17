<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-bold">Estructuras TRD</h2>
            <p class="text-xs text-slate-500">Series, retención y disposición final (CT / E / M / S)</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('trd.import'))
                <a href="{{ route('trd.import') }}" wire:navigate class="px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">Importar CSV</a>
            @endif
            @if(auth()->user()->hasPermission('trd.create'))
                <a href="{{ route('trd.create') }}" wire:navigate class="px-3 py-1.5 text-xs text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Nueva TRD</a>
            @endif
        </div>
    </div>
    <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar sección o versión" class="px-3 py-2 text-xs border rounded-lg w-72">
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left p-3">Código</th>
                    <th class="text-left p-3">Sección</th>
                    <th class="text-left p-3">Versión</th>
                    <th class="text-left p-3">Series</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($structures as $structure)
                <tr class="border-t border-slate-100">
                    <td class="p-3 font-mono">{{ $structure->section_code }}</td>
                    <td class="p-3 font-medium">{{ $structure->section_name }}</td>
                    <td class="p-3">{{ $structure->version }}</td>
                    <td class="p-3">{{ count($structure->series ?? []) }}</td>
                    <td class="p-3">
                        <div class="flex justify-end gap-1">
                            <x-icon-action href="{{ route('trd.show', $structure->getKey()) }}" tooltip="Ver detalle" variant="info">
                                <x-icon name="eye" class="w-4 h-4" />
                            </x-icon-action>
                            @if(auth()->user()->hasPermission('trd.edit'))
                                <x-icon-action href="{{ route('trd.edit', $structure->getKey()) }}" tooltip="Editar" variant="neutral">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                            @if(auth()->user()->hasPermission('trd.delete'))
                                <x-icon-action tooltip="Inhabilitar" variant="danger" wire:click="inhabilitar('{{ $structure->getKey() }}')" wire:confirm="¿Inhabilitar esta TRD con is_deleted: true?">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">No hay estructuras TRD.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
