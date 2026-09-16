<x-layouts.app>
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex justify-between items-start pb-4 border-b">
        <div>
            <p class="text-xs font-mono text-blue-700">{{ $proceeding->file_number }}</p>
            <h2 class="text-xl font-bold">{{ $proceeding->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $proceeding->serie_name }} / {{ $proceeding->sub_serie_name }} · {{ $proceeding->state }}</p>
        </div>
        @if(auth()->user()->hasPermission('documents.upload'))
            <a href="{{ route('documents.create', ['proceeding_id' => $proceeding->id]) }}" class="px-3 py-1.5 text-xs bg-blue-600 text-white rounded-lg">Cargar documento</a>
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
                <form method="POST" action="{{ route('proceedings.sub', $proceeding) }}" class="mt-3 flex gap-2">
                    @csrf
                    <input name="code" placeholder="Código" class="px-2 py-1 border rounded text-xs">
                    <input name="name" placeholder="Nombre" class="px-2 py-1 border rounded text-xs flex-1">
                    <button class="px-2 py-1 bg-slate-800 text-white rounded text-xs">Agregar</button>
                </form>
            @endif
        </div>
    </div>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left">Documento</th><th class="p-3">Tipo</th><th class="p-3">Soporte</th><th class="p-3">Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($proceeding->documents as $document)
                <tr class="border-t">
                    <td class="p-3">{{ $document->name }}</td>
                    <td class="p-3 text-center">{{ $document->document_type }}</td>
                    <td class="p-3 text-center">{{ $document->support }}</td>
                    <td class="p-3 text-center">{{ $document->state }}</td>
                    <td class="p-3 text-right"><a class="text-blue-600" href="{{ route('documents.show', $document) }}">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-6 text-center text-slate-500">Sin documentos asociados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
