<x-layouts.app>
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-bold">Estructuras TRD</h2>
            <p class="text-xs text-slate-500">Series, subseries y disposición final</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('trd.import'))
                <a href="{{ route('trd.import') }}" class="px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">Importar CSV</a>
            @endif
            @if(auth()->user()->hasPermission('trd.create'))
                <a href="{{ route('trd.create') }}" class="px-3 py-1.5 text-xs text-white bg-blue-600 rounded-lg">Nueva TRD</a>
            @endif
        </div>
    </div>
    <form class="flex gap-2">
        <input name="q" value="{{ $search }}" placeholder="Buscar sección o versión" class="px-3 py-2 text-xs border rounded-lg w-72">
        <button class="px-3 py-2 text-xs bg-white border rounded-lg">Buscar</button>
    </form>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left p-3">Código</th>
                    <th class="text-left p-3">Sección</th>
                    <th class="text-left p-3">Versión</th>
                    <th class="text-left p-3">Series</th>
                    <th class="text-left p-3">Subseries</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
            @forelse($structures as $structure)
                <tr class="border-t border-slate-100">
                    <td class="p-3 font-mono">{{ $structure->section_code }}</td>
                    <td class="p-3 font-medium">{{ $structure->section_name }}</td>
                    <td class="p-3">{{ $structure->version }}</td>
                    <td class="p-3">{{ count($structure->series ?? []) }}</td>
                    <td class="p-3">{{ count($structure->sub_series ?? []) }}</td>
                    <td class="p-3 text-right space-x-2">
                        <a class="text-blue-600" href="{{ route('trd.show', $structure) }}">Ver</a>
                        @if(auth()->user()->hasPermission('trd.edit'))
                            <a class="text-slate-700" href="{{ route('trd.edit', $structure) }}">Editar</a>
                        @endif
                        @if(auth()->user()->hasPermission('trd.delete'))
                            <form class="inline" method="POST" action="{{ route('trd.destroy', $structure) }}" onsubmit="return confirm('¿Inhabilitar esta TRD con soft delete?')">
                                @csrf @method('DELETE')
                                <button class="text-rose-600">Baja</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-500">No hay estructuras TRD.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
