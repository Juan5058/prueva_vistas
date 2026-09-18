<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-start pb-4 border-b">
        <div>
            <p class="text-xs font-mono text-slate-700">{{ $proceeding->file_number }}</p>
            <h2 class="text-xl font-bold">{{ $proceeding->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $proceeding->serie_name }} / {{ $proceeding->sub_serie_name }} · {{ $proceeding->state }}</p>
        </div>
        @if(auth()->user()->hasPermission('documents.upload'))
            <a href="{{ route('documents.create', ['proceeding_id' => $proceeding->getKey()]) }}" wire:navigate class="px-3 py-1.5 text-xs bg-slate-800 hover:bg-slate-700 text-white rounded-lg">Cargar PDF</a>
        @endif
    </div>
    <p class="text-sm text-slate-600">{{ $proceeding->description }}</p>
    <div class="grid sm:grid-cols-2 gap-4">
        <div class="bg-white border rounded-xl p-4 text-xs space-y-1">
            <h3 class="font-semibold mb-2">Ubicación física</h3>
            @foreach($proceeding->physical_location ?? [] as $k => $v)
                <div class="flex justify-between"><span class="text-slate-500">{{ $k }}</span><span>{{ $v }}</span></div>
            @endforeach
        </div>
        <div class="bg-white border rounded-xl p-4 text-xs">
            <h3 class="font-semibold mb-2">Subexpedientes</h3>
            @foreach($proceeding->sub_proceedings ?? [] as $sub)
                <p class="py-1"><span class="font-mono">{{ $sub['code'] }}</span> {{ $sub['name'] }}</p>
            @endforeach
            @if(auth()->user()->hasPermission('proceedings.create'))
                <form wire:submit="addSub" class="mt-3 flex gap-2">
                    <input wire:model="sub_code" placeholder="Código" class="px-2 py-1 border rounded text-xs">
                    <input wire:model="sub_name" placeholder="Nombre" class="px-2 py-1 border rounded text-xs flex-1">
                    <button class="px-2 py-1 bg-slate-800 text-white rounded text-xs">Agregar</button>
                </form>
            @endif
        </div>
    </div>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left">Documento</th><th class="p-3">Tipo</th><th class="p-3">Soporte</th><th></th></tr></thead>
            <tbody>
            @forelse($proceeding->documents as $document)
                <tr class="border-t">
                    <td class="p-3">{{ $document->name }}</td>
                    <td class="p-3 text-center">{{ $document->document_type }}</td>
                    <td class="p-3 text-center">{{ $document->support }}</td>
                    <td class="p-3 text-right">
                        <div class="flex justify-end gap-1">
                            <x-icon-action href="{{ route('documents.show', $document->getKey()) }}" tooltip="Ver detalle" variant="info">
                                <x-icon name="eye" class="w-4 h-4" />
                            </x-icon-action>
                            @if(auth()->user()->hasPermission('documents.download') && $document->file_path)
                                <x-icon-action href="{{ route('documents.download', $document->getKey()) }}" :navigate="false" tooltip="Abrir PDF">
                                    <x-icon name="download" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin documentos asociados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
