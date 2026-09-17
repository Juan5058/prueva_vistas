<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="book" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Estructuras TRD</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                    Tablas de Retención Documental: Series, retención y disposición final (CT / E / M / S).
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->hasPermission('trd.import'))
                <a href="{{ route('trd.import') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-semibold text-xs text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 shadow-sm transition-all">
                    <x-icon name="download" class="w-4 h-4 text-slate-300" />
                    <span>Importar CSV</span>
                </a>
            @endif
            @if(auth()->user()->hasPermission('trd.create'))
<<<<<<< HEAD
                <a href="{{ route('trd.create') }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all">
                    <x-icon name="plus" class="w-4 h-4" />
                    <span>Nueva TRD</span>
                </a>
=======
                <a href="{{ route('trd.create') }}" wire:navigate class="px-3 py-1.5 text-xs text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Nueva TRD</a>
>>>>>>> b167af4246a9b21d0efbbd3ab6a5ea5bb6bf4537
            @endif
        </div>
    </div>

    {{-- Buscador y Panel de Tabla --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-80">
                <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar sección o versión..." class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
            </div>
        </div>

        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Código</th>
                        <th class="p-3.5">Sección</th>
                        <th class="p-3.5">Versión</th>
                        <th class="p-3.5">Series</th>
                        <th class="p-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($structures as $structure)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5 font-mono font-bold text-blue-700">{{ $structure->section_code }}</td>
                        <td class="p-3.5 font-semibold text-slate-900">{{ $structure->section_name }}</td>
                        <td class="p-3.5 text-slate-600">{{ $structure->version }}</td>
                        <td class="p-3.5 font-mono text-slate-600">{{ count($structure->series ?? []) }}</td>
                        <td class="p-3.5">
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
                    <tr><td colspan="5" class="p-8 text-center text-slate-500">No hay estructuras TRD registradas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

