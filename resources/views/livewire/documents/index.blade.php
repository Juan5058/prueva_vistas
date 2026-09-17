<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="file" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Acervo Documental</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                    Gestión de unidades documentales en formato PDF (UUID, MIME application/pdf, máx 10 MB).
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->hasPermission('documents.upload'))
                <a href="{{ route('documents.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all">
                    <x-icon name="upload" class="w-4 h-4" />
                    <span>Cargar documento</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Buscador y Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-80">
                <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar documento..." class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
            </div>
        </div>

        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Nombre</th>
                        <th class="p-3.5">Expediente</th>
                        <th class="p-3.5 text-center">Tipo</th>
                        <th class="p-3.5 text-center">Soporte</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($documents as $document)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5 font-semibold text-slate-900">{{ $document->name }}</td>
                        <td class="p-3.5 font-mono text-blue-700 font-bold">{{ $document->proceeding?->file_number ?: '—' }}</td>
                        <td class="p-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                {{ $document->document_type }}
                            </span>
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $document->support }}
                            </span>
                        </td>
                        <td class="p-3.5">
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
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">No hay documentos registrados.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

