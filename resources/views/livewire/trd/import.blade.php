<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Banner Header --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="upload-cloud" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Carga masiva TRD</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Sube archivos CSV o Excel (.xlsx) para procesar estructuras TRD en segundo plano.</p>
            </div>
        </div>
    </div>

    {{-- Formulario de Archivo --}}
    <form wire:submit="save" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="p-4 bg-slate-50/60 border border-slate-200 rounded-xl space-y-2">
            <label class="block text-xs font-semibold text-slate-700">Seleccionar archivo (.csv, .xlsx)</label>
            <input type="file" wire:model="file" accept=".csv,.txt,.xlsx,.xls" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
            <p class="text-[11px] text-slate-400">Encabezados requeridos: codigo_seccion, nombre_seccion, codigo_subseccion, nombre_subseccion, codigo_serie, nombre_serie, codigo_subserie, nombre_subserie, retencion_gestion, retencion_central, disposicion_final.</p>
        </div>
        @error('file') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
        <div wire:loading wire:target="file" class="text-xs text-blue-600 font-medium">Validando y cargando archivo...</div>

        <div class="flex justify-end pt-2">
            <button class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all cursor-pointer">
                <x-icon name="upload-cloud" class="w-4 h-4" />
                <span>Enviar a cola Redis</span>
            </button>
        </div>
    </form>

    {{-- Historial de Importaciones --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900">Historial de importaciones procesadas</h3>
        </div>
        <table class="w-full text-xs text-left">
            <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="p-3.5">Archivo</th>
                    <th class="p-3.5 text-center">Estado</th>
                    <th class="p-3.5 text-center">Filas</th>
                    <th class="p-3.5 text-center">Errores</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
            @forelse($imports as $import)
                @php
                    $s = strtolower($import->status ?? '');
                    $importBadge = match(true) {
                        str_contains($s, 'done') || str_contains($s, 'complet') || str_contains($s, 'success')
                            => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        str_contains($s, 'process') || str_contains($s, 'running')
                            => 'bg-blue-100 text-blue-700 border-blue-200',
                        str_contains($s, 'error') || str_contains($s, 'fail')
                            => 'bg-rose-100 text-rose-700 border-rose-200',
                        default => 'bg-amber-100 text-amber-700 border-amber-200',
                    };
                    $errCount = count($import->error_log ?? []);
                @endphp
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="p-3.5 font-medium text-slate-900">{{ $import->file_name }}</td>
                    <td class="p-3.5 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border {{ $importBadge }}">
                            {{ $import->status }}
                        </span>
                    </td>
                    <td class="p-3.5 text-center">
                        <span class="font-mono text-slate-700 text-[11px]">
                            {{ $import->processed_rows }}<span class="text-slate-400">/</span>{{ $import->total_rows }}
                        </span>
                    </td>
                    <td class="p-3.5 text-center">
                        @if($errCount > 0)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                <x-icon name="alert-triangle" class="w-3.5 h-3.5 text-rose-600 shrink-0" />
                                <span>{{ $errCount }}</span>
                            </span>
                        @else
                            <span class="text-slate-400 text-[11px]">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="p-8 text-center text-slate-500">Sin importaciones registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
        <x-table-pagination :paginator="$imports" :perPage="$perPage" :customPerPage="$customPerPage" />
    </div>
</div>

