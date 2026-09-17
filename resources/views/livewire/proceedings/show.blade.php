<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner Header --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="folder" class="w-6 h-6" />
            </div>
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30 mb-1">
                    {{ $proceeding->file_number }} · {{ $proceeding->state }}
                </span>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $proceeding->name }}</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    {{ $proceeding->serie_name }} / {{ $proceeding->sub_serie_name }}
                </p>
            </div>
        </div>
        <div>
<<<<<<< HEAD
            @if(auth()->user()->hasPermission('documents.upload'))
                <a href="{{ route('documents.create', ['proceeding_id' => $proceeding->getKey()]) }}" wire:navigate class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all">
                    <x-icon name="upload" class="w-4 h-4" />
                    <span>Cargar PDF</span>
                </a>
            @endif
        </div>
=======
            <p class="text-xs font-mono text-slate-700">{{ $proceeding->file_number }}</p>
            <h2 class="text-xl font-bold">{{ $proceeding->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $proceeding->serie_name }} / {{ $proceeding->sub_serie_name }} · {{ $proceeding->state }}</p>
        </div>
        @if(auth()->user()->hasPermission('documents.upload'))
            <a href="{{ route('documents.create', ['proceeding_id' => $proceeding->getKey()]) }}" wire:navigate class="px-3 py-1.5 text-xs bg-slate-800 hover:bg-slate-700 text-white rounded-lg">Cargar PDF</a>
        @endif
>>>>>>> b167af4246a9b21d0efbbd3ab6a5ea5bb6bf4537
    </div>

    @if($proceeding->description)
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <p class="text-xs text-slate-600 leading-relaxed"><strong class="text-slate-800 font-semibold">Descripción:</strong> {{ $proceeding->description }}</p>
        </div>
    @endif

    <div class="grid sm:grid-cols-2 gap-5">
        {{-- Ubicación Física --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Ubicación física en archivo
            </h3>
            <div class="space-y-2 text-xs divide-y divide-slate-100">
                @forelse($proceeding->physical_location ?? [] as $k => $v)
                    <div class="flex justify-between pt-1.5">
                        <span class="text-slate-500 font-medium capitalize">{{ $k }}</span>
                        <span class="font-mono font-bold text-slate-900">{{ $v ?: '—' }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2">Sin información de ubicación.</p>
                @endforelse
            </div>
        </div>

        {{-- Subexpedientes --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Subexpedientes vinculados
            </h3>
            <div class="space-y-1.5 divide-y divide-slate-100">
                @forelse($proceeding->sub_proceedings ?? [] as $sub)
                    <p class="text-xs pt-1.5 flex items-center gap-2">
                        <span class="font-mono font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ $sub['code'] }}</span>
                        <span class="text-slate-800 font-medium">{{ $sub['name'] }}</span>
                    </p>
                @empty
                    <p class="text-xs text-slate-400 py-1">Sin subexpedientes.</p>
                @endforelse
            </div>

            @if(auth()->user()->hasPermission('proceedings.create'))
                <form wire:submit="addSub" class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-2">
                    <input wire:model="sub_code" placeholder="Código" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-mono font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 w-24">
                    <input wire:model="sub_name" placeholder="Nombre..." class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1">
                    <button class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition-all">Agregar</button>
                </form>
            @endif
        </div>
    </div>

    {{-- Tabla de Documentos --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-900">Documentos archivados en este expediente</h3>
            <span class="text-xs font-mono font-semibold text-slate-500 bg-slate-200/60 px-2.5 py-0.5 rounded-full">{{ count($proceeding->documents) }} docs</span>
        </div>
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="p-3.5">Documento</th>
                    <th class="p-3.5 text-center">Tipo</th>
                    <th class="p-3.5 text-center">Soporte</th>
                    <th class="p-3.5 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($proceeding->documents as $document)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-3.5 font-semibold text-slate-900">{{ $document->name }}</td>
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
                    <td class="p-3.5 text-right">
                        <x-icon-action href="{{ route('documents.show', $document->getKey()) }}" tooltip="Ver detalle" variant="info">
                            <x-icon name="eye" class="w-4 h-4" />
                        </x-icon-action>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-8 text-center text-slate-500">Sin documentos asociados a este expediente.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

