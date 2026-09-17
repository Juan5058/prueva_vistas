<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex items-start justify-between pb-4 border-b">
        <div>
            <p class="text-xs font-mono text-slate-700">{{ data_get($document->file_metadata, 'uuid', data_get($document->file_metadata, 'original_name')) }}</p>
            <h2 class="text-xl font-bold">{{ $document->name }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $document->document_type }} · {{ $document->support }} · {{ number_format((int) data_get($document->file_metadata, 'size_bytes', 0) / 1024, 1) }} KB</p>
        </div>
        <div class="flex gap-1">
            @if(auth()->user()->hasPermission('documents.edit'))
                <x-icon-action href="{{ route('documents.edit', $document->getKey()) }}" tooltip="Editar">
                    <x-icon name="pencil" class="w-4 h-4" />
                </x-icon-action>
            @endif
            @if(auth()->user()->hasPermission('documents.download'))
                <x-icon-action href="{{ route('documents.download', $document->getKey()) }}" tooltip="Descargar PDF">
                    <x-icon name="download" class="w-4 h-4" />
                </x-icon-action>
            @endif
        </div>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-4">
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div>
                <dt class="text-slate-500">Expediente</dt>
                <dd class="mt-1 font-mono font-medium text-slate-900">{{ $document->proceeding?->file_number ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Tipo MIME</dt>
                <dd class="mt-1 font-medium text-slate-900">{{ data_get($document->file_metadata, 'mime_type') ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-slate-500">Archivo</dt>
                <dd class="mt-1 font-mono text-slate-700 break-all">{{ $document->file_path ?: 'Sin archivo digital' }}</dd>
            </div>
        </dl>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50/80">
            <h3 class="text-sm font-semibold text-slate-900">Bitácora de auditoría</h3>
            <p class="text-[11px] text-slate-500 mt-0.5">Consultas, descargas y actualizaciones del documento</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="p-3 text-left font-semibold">Acción</th>
                        <th class="p-3 text-left font-semibold">Usuario</th>
                        <th class="p-3 text-left font-semibold">IP</th>
                        <th class="p-3 text-right font-semibold">Fecha</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($audits as $audit)
                    <tr class="border-t border-slate-100 hover:bg-slate-50/70">
                        <td class="p-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md border text-[11px] font-semibold uppercase tracking-wide {{ $audit->actionBadgeClass() }}">
                                {{ $audit->actionLabel() }}
                            </span>
                        </td>
                        <td class="p-3">
                            <div class="font-medium text-slate-900">{{ $audit->user?->name ?? 'Usuario no disponible' }}</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">{{ $audit->user?->email ?? '—' }}</div>
                        </td>
                        <td class="p-3 font-mono text-slate-600 whitespace-nowrap">{{ $audit->ip_address ?: '—' }}</td>
                        <td class="p-3 text-right text-slate-600 whitespace-nowrap">{{ \App\Support\FormatsDate::datetime($audit->created_at, 'd/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-slate-500">Sin eventos de auditoría.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
