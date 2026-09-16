<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <h2 class="text-xl font-bold">Documentos</h2>
            <p class="text-xs text-slate-500">PDF UUID, MIME application/pdf, tope 10 MB</p>
        </div>
        @if(auth()->user()->hasPermission('documents.upload'))
            <a href="{{ route('documents.create') }}" wire:navigate class="px-3 py-1.5 text-xs text-white bg-blue-600 rounded-lg">Cargar documento</a>
        @endif
    </div>
    <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar documento" class="px-3 py-2 text-xs border rounded-lg w-72">
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Expediente</th>
                    <th class="p-3">Tipo</th>
                    <th class="p-3">Soporte</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($documents as $document)
                <tr class="border-t">
                    <td class="p-3 font-medium">{{ $document->name }}</td>
                    <td class="p-3 font-mono">{{ $document->proceeding?->file_number }}</td>
                    <td class="p-3 text-center">{{ $document->document_type }}</td>
                    <td class="p-3 text-center">{{ $document->support }}</td>
                    <td class="p-3">
                        <div class="flex justify-end gap-1">
                            <x-icon-action href="{{ route('documents.show', $document->getKey()) }}" tooltip="Ver detalle" variant="info">
                                <x-icon name="eye" class="w-4 h-4" />
                            </x-icon-action>
                            @if(auth()->user()->hasPermission('documents.edit'))
                                <x-icon-action href="{{ route('documents.edit', $document->getKey()) }}" tooltip="Editar">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                            @if(auth()->user()->hasPermission('documents.download'))
                                <x-icon-action href="{{ route('documents.download', $document->getKey()) }}" tooltip="Descargar PDF">
                                    <x-icon name="download" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                            @if(auth()->user()->hasPermission('documents.delete'))
                                <x-icon-action tooltip="Inhabilitar" variant="danger" wire:click="inhabilitar('{{ $document->getKey() }}')" wire:confirm="¿Inhabilitar este documento?">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">No hay documentos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
