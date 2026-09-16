<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-xl font-bold">Carga masiva TRD</h2>
        <p class="text-xs text-slate-500">Encabezados: codigo_seccion, nombre_seccion, codigo_subseccion, nombre_subseccion, codigo_serie, nombre_serie, codigo_subserie, nombre_subserie, retencion_gestion, retencion_central, disposicion_final. Formatos: CSV o Excel (.xlsx).</p>
    </div>
    <form wire:submit="save" class="bg-white border rounded-xl p-5 space-y-3">
        <input type="file" wire:model="file" accept=".csv,.txt,.xlsx,.xls" class="text-xs">
        @error('file') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
        <div wire:loading wire:target="file" class="text-xs text-slate-500">Cargando archivo...</div>
        <button class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg">Enviar a cola Redis</button>
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
