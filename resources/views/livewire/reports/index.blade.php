<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Reportes de inventario</h2>
            <p class="text-xs text-slate-500 mt-0.5">Estadísticas TRD y descarga PDF del acervo</p>
        </div>
        @if(auth()->user()->hasPermission('reports.download-pdf'))
            <a href="{{ route('reports.download-pdf') }}" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-white bg-slate-800 hover:bg-slate-700 rounded-lg">
                <x-icon name="download" class="w-4 h-4" /> Descargar inventario PDF
            </a>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Expedientes', $kpis['totalProceedings']],
            ['Documentos', $kpis['totalDocuments']],
            ['Series TRD', $kpis['totalSeries']],
            ['Usuarios activos', $kpis['totalActiveUsers']],
        ] as [$title, $value])
            <div class="bg-white p-5 rounded-xl border border-slate-200">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ $title }}</div>
                <div class="text-2xl font-bold font-mono">{{ $value }}</div>
            </div>
        @endforeach
    </div>
    <x-metrics-charts :distribution="$distribution" />
</div>
