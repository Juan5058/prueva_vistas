<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- ═══════════════════════════════════════════════════════════
         1. BANNER SUPERIOR DE DASHBOARD
    ════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="dashboard" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Dashboard Ejecutivo & Métricas TRD</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                    KPIs, estadísticas de almacenamiento y resumen del acervo documental.
                </p>
            </div>
        </div>
        <div class="hidden lg:flex items-center gap-2 bg-slate-800/80 border border-slate-700/60 px-3.5 py-1.5 rounded-full text-xs text-slate-300 font-mono">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Sistema en línea</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         2. TARJETAS KPIS CON LENGUAJE VISUAL DE REPORTES
    ════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Expedientes radicados', $kpis['totalProceedings'], 'folder', 'Activos en TRD', 'Secciones: '.$kpis['totalTrdSections']],
            ['Acervo documental', $kpis['totalDocuments'], 'file', 'Electrónicos '.$kpis['totalElectronicDocuments'], 'Físicos '.$kpis['totalPhysicalDocuments']],
            ['Estructuras TRD', $kpis['totalTrdSections'], 'book', 'Series '.$kpis['totalSeries'], 'Subseries '.$kpis['totalSubSeries']],
            ['Usuarios activos', $kpis['totalActiveUsers'], 'users', 'Alertas IP '.$kpis['totalBlockedAlerts'], number_format($kpis['totalStorageBytes'] / 1048576, 2).' MB'],
        ] as [$title, $value, $icon, $a, $b])
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-slate-300 hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ $title }}</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <x-icon :name="$icon" class="w-4 h-4" />
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-slate-900 font-mono tracking-tight">{{ $value }}</div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
                    <span class="font-medium text-slate-500">{{ $a }}</span>
                    <span class="font-semibold text-slate-700 font-mono">{{ $b }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         3. GRÁFICOS Y DISTRIBUCIÓN
    ════════════════════════════════════════════════════════════ --}}
    <x-metrics-charts :distribution="$distribution" />
</div>

