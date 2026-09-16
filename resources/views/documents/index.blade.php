<x-layouts.app>
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex justify-between items-center pb-4 border-b">
        <div>
            <h2 class="text-xl font-bold">Documentos</h2>
            <p class="text-xs text-slate-500">Inventario indexado con almacenamiento privado</p>
        </div>
        @if(auth()->user()->hasPermission('documents.upload'))
            <a href="{{ route('documents.create') }}" class="px-3 py-1.5 text-xs text-white bg-blue-600 rounded-lg">Cargar documento</a>
        @endif
    </div>
    <form class="flex gap-2">
        <input name="q" value="{{ $search }}" placeholder="Buscar documento" class="px-3 py-2 text-xs border rounded-lg w-72">
        <button class="px-3 py-2 text-xs bg-white border rounded-lg">Buscar</button>
    </form>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left">Nombre</th><th class="p-3">Tipo</th><th class="p-3">Expediente</th><th class="p-3">Soporte</th><th></th></tr></thead>
            <tbody>
            @forelse($documents as $document)
                <tr class="border-t">
                    <td class="p-3 font-medium">{{ $document->name }}</td>
                    <td class="p-3 text-center">{{ $document->document_type }}</td>
                    <td class="p-3">{{ $document->proceeding?->file_number }}</td>
                    <td class="p-3 text-center">{{ $document->support }}</td>
                    <td class="p-3 text-right space-x-2">
                        <a class="text-blue-600" href="{{ route('documents.show', $document) }}">Ver</a>
                        @if(auth()->user()->hasPermission('documents.delete'))
                            <form class="inline" method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('¿Inhabilitar documento?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600">Baja</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">No hay documentos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
