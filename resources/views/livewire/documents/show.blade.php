<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Banner Header --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="documents" class="w-6 h-6" />
            </div>
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30 mb-1">
                    {{ data_get($document->file_metadata, 'uuid', data_get($document->file_metadata, 'original_name')) }}
                </span>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $document->name }}</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    {{ $document->document_type }} · {{ $document->support }} · {{ number_format((int) data_get($document->file_metadata, 'size_bytes', 0) / 1024, 1) }} KB
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if(auth()->user()->hasPermission('documents.edit'))
                <x-icon-action href="{{ route('documents.edit', $document->getKey()) }}" tooltip="Editar" variant="neutral">
                    <x-icon name="pencil" class="w-4 h-4" />
                </x-icon-action>
            @endif
            @if(auth()->user()->hasPermission('documents.download'))
                <x-icon-action href="{{ route('documents.download', $document->getKey()) }}" tooltip="Descargar PDF" variant="info">
                    <x-icon name="download" class="w-4 h-4" />
                </x-icon-action>
            @endif
        </div>
    </div>

    {{-- Metadata del Documento --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            Metadatos del archivo
        </h3>
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs">
            <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl">
                <dt class="text-slate-500 font-semibold uppercase tracking-wider text-[10px]">Expediente</dt>
                <dd class="mt-1 font-mono font-bold text-blue-700 text-sm">{{ $document->proceeding?->file_number ?: '—' }}</dd>
            </div>
            <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl">
                <dt class="text-slate-500 font-semibold uppercase tracking-wider text-[10px]">Tipo MIME</dt>
                <dd class="mt-1 font-mono font-semibold text-slate-900 text-sm">{{ data_get($document->file_metadata, 'mime_type') ?: '—' }}</dd>
            </div>
            <div class="bg-slate-50 border border-slate-200 p-3.5 rounded-xl">
                <dt class="text-slate-500 font-semibold uppercase tracking-wider text-[10px]">Ruta de Almacenamiento</dt>
                <dd class="mt-1 font-mono text-slate-700 break-all text-[11px]">{{ $document->file_path ?: 'Sin archivo digital' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Bitácora de Auditoría --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900">Bitácora de auditoría documental</h3>
            <p class="text-xs text-slate-500 mt-0.5">Historial de visualizaciones, descargas y modificaciones</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-semibold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="p-3.5">Acción</th>
                        <th class="p-3.5">Usuario</th>
                        <th class="p-3.5 text-center">IP</th>
                        <th class="p-3.5 text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($audits as $audit)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="p-3.5 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full border text-[11px] font-semibold uppercase tracking-wide {{ $audit->actionBadgeClass() }}">
                                {{ $audit->actionLabel() }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            <div class="font-semibold text-slate-900">{{ $audit->user?->name ?? 'Usuario no disponible' }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $audit->user?->email ?? '—' }}</div>
                        </td>
                        <td class="p-3.5 font-mono text-slate-600 text-center whitespace-nowrap">{{ $audit->ip_address ?: '—' }}</td>
                        <td class="p-3.5 text-right text-slate-600 font-mono whitespace-nowrap">{{ \App\Support\FormatsDate::datetime($audit->created_at, 'd/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-slate-500">Sin eventos de auditoría registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <x-table-pagination :paginator="$audits" :perPage="$perPage" :customPerPage="$customPerPage" />
    </div>
</div>

