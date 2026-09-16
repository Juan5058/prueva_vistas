<x-layouts.app>
@section('title', 'Dashboard TRD')
<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Dashboard Ejecutivo & Métricas TRD</h2>
            <p class="text-xs text-slate-500 mt-0.5">Monitoreo del inventario documental, retención y seguridad</p>
        </div>
        <a href="{{ route('proceedings.index') }}" class="px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg">Ver Expedientes</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['Expedientes radicados', $kpis['totalProceedings'], 'Activos en TRD', 'Secciones: '.$kpis['totalTrdSections']],
            ['Acervo documental', $kpis['totalDocuments'], 'Electrónicos '.$kpis['totalElectronicDocuments'], 'Físicos '.$kpis['totalPhysicalDocuments']],
            ['Estructuras TRD', $kpis['totalTrdSections'], 'Series '.$kpis['totalSeries'], 'Subseries '.$kpis['totalSubSeries']],
            ['Alertas bloqueadas', $kpis['totalBlockedAlerts'], 'Usuarios activos '.$kpis['totalActiveUsers'], number_format($kpis['totalStorageBytes'] / 1048576, 2).' MB'],
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <h3 class="text-sm font-semibold mb-3">Soporte documental</h3>
            @forelse($distribution['bySupport'] as $row)
                <div class="flex justify-between text-xs py-1"><span>{{ $row['name'] }}</span><span>{{ $row['count'] }} ({{ $row['percentage'] }}%)</span></div>
            @empty
                <p class="text-xs text-slate-500">Sin documentos.</p>
            @endforelse
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <h3 class="text-sm font-semibold mb-3">Estado de expedientes</h3>
            @forelse($distribution['byState'] as $row)
                <div class="flex justify-between text-xs py-1"><span>{{ $row['state'] }}</span><span>{{ $row['count'] }}</span></div>
            @empty
                <p class="text-xs text-slate-500">Sin expedientes.</p>
            @endforelse
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <h3 class="text-sm font-semibold mb-3">Disposición final TRD</h3>
            @forelse($distribution['byDisposition'] as $row)
                <div class="flex justify-between text-xs py-1"><span>{{ $row['type'] }}</span><span>{{ $row['count'] }}</span></div>
            @empty
                <p class="text-xs text-slate-500">Sin subseries.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white p-5 rounded-xl border border-slate-200">
        <h3 class="text-sm font-semibold mb-3">Alertas recientes (BLOCKED)</h3>
        <div class="space-y-2">
            @forelse($recentAlerts as $alert)
                <div class="text-xs border border-rose-100 bg-rose-50 rounded-lg p-3">
                    <div class="font-semibold text-rose-800">{{ $alert->user_email }} · {{ $alert->ip_address }}</div>
                    <div class="text-rose-700 mt-1">{{ $alert->reason }}</div>
                </div>
            @empty
                <p class="text-xs text-slate-500">No hay alertas bloqueadas recientes.</p>
            @endforelse
        </div>
    </div>
</div>
</x-layouts.app>
