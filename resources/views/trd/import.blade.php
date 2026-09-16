<x-layouts.app>
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-bold">Carga masiva TRD</h2>
        <p class="text-xs text-slate-500">CSV o TXT: seccion_codigo; seccion_nombre; subseccion_codigo; subseccion_nombre; serie_codigo; serie_nombre; subserie_codigo; subserie_nombre; ret_gestion; ret_central; disposicion; procedimiento</p>
    </div>
    <form method="POST" action="{{ route('trd.import.store') }}" enctype="multipart/form-data" class="bg-white border rounded-xl p-5 space-y-3">
        @csrf
        <input type="file" name="file" required accept=".csv,.txt" class="text-xs">
        <button class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg">Enviar a cola de importación</button>
    </form>
    <div class="bg-white border rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left">Archivo</th><th class="p-3">Estado</th><th class="p-3">Filas</th><th class="p-3">Errores</th></tr></thead>
            <tbody>
            @forelse($imports as $import)
                <tr class="border-t">
                    <td class="p-3">{{ $import->file_name }}</td>
                    <td class="p-3 text-center">{{ $import->status }}</td>
                    <td class="p-3 text-center">{{ $import->processed_rows }}/{{ $import->total_rows }}</td>
                    <td class="p-3">{{ count($import->error_log ?? []) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin importaciones.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
