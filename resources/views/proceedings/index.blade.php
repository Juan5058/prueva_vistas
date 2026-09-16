<x-layouts.app>
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <h2 class="text-xl font-bold">Expedientes</h2>
            <p class="text-xs text-slate-500">Inventario archivístico vinculado a TRD</p>
        </div>
        @if(auth()->user()->hasPermission('proceedings.create'))
            <a href="{{ route('proceedings.create') }}" class="px-3 py-1.5 text-xs text-white bg-blue-600 rounded-lg">Abrir expediente</a>
        @endif
    </div>
    <form class="flex gap-2">
        <input name="q" value="{{ $search }}" placeholder="Buscar radicado o nombre" class="px-3 py-2 text-xs border rounded-lg w-72">
        <button class="px-3 py-2 text-xs bg-white border rounded-lg">Buscar</button>
    </form>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-3 text-left">Radicado</th>
                    <th class="p-3 text-left">Nombre</th>
                    <th class="p-3 text-left">Serie</th>
                    <th class="p-3 text-left">Estado</th>
                    <th class="p-3 text-left">Docs</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($proceedings as $proceeding)
                <tr class="border-t">
                    <td class="p-3 font-mono">{{ $proceeding->file_number }}</td>
                    <td class="p-3 font-medium">{{ $proceeding->name }}</td>
                    <td class="p-3">{{ $proceeding->serie_name }}</td>
                    <td class="p-3">{{ $proceeding->state }}</td>
                    <td class="p-3">{{ $proceeding->documents_count }}</td>
                    <td class="p-3 text-right space-x-2">
                        <a class="text-blue-600" href="{{ route('proceedings.show', $proceeding) }}">Ver</a>
                        @if(auth()->user()->hasPermission('proceedings.edit'))
                            <a href="{{ route('proceedings.edit', $proceeding) }}">Editar</a>
                        @endif
                        @if(auth()->user()->hasPermission('proceedings.delete'))
                            <form class="inline" method="POST" action="{{ route('proceedings.destroy', $proceeding) }}" onsubmit="return confirm('¿Dar de baja este expediente?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600">Baja</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-500">No hay expedientes.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
