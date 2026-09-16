<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-200">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Ejecutivo & Métricas TRD</h2>
        <p class="text-xs text-slate-500 mt-0.5">KPIs, gráficos y estadísticas rápidas</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Expedientes radicados', $kpis['totalProceedings'], 'Activos en TRD', 'Secciones: '.$kpis['totalTrdSections']],
            ['Acervo documental', $kpis['totalDocuments'], 'Electrónicos '.$kpis['totalElectronicDocuments'], 'Físicos '.$kpis['totalPhysicalDocuments']],
            ['Estructuras TRD', $kpis['totalTrdSections'], 'Series '.$kpis['totalSeries'], 'Subseries '.$kpis['totalSubSeries']],
            ['Usuarios activos', $kpis['totalActiveUsers'], 'Alertas IP '.$kpis['totalBlockedAlerts'], number_format($kpis['totalStorageBytes'] / 1048576, 2).' MB'],
        ] as [$title, $value, $a, $b])
            <div class="bg-white p-5 rounded-xl border border-slate-200">
                <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ $title }}</div>
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ $value }}</div>
                <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-500 flex justify-between">
                    <span>{{ $a }}</span>
                    <span class="font-semibold text-slate-700">{{ $b }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <x-metrics-charts :distribution="$distribution" />
</div>
