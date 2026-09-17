<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Banner Header --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="book" class="w-6 h-6" />
            </div>
            <div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-mono font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/30 mb-1">
                    {{ $structure->section_code }} · {{ $structure->version }}
                </span>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $structure->section_name }}</h2>
            </div>
        </div>
        @if(auth()->user()->hasPermission('trd.edit'))
            <x-icon-action href="{{ route('trd.edit', $structure->getKey()) }}" tooltip="Editar" variant="neutral">
                <x-icon name="pencil" class="w-4 h-4" />
            </x-icon-action>
        @endif
    </div>

    {{-- Grid de Detalle TRD --}}
    <div class="grid md:grid-cols-3 gap-5">
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Subsecciones
            </h3>
            <div class="space-y-1.5 divide-y divide-slate-100">
                @forelse($structure->sub_sections ?? [] as $row)
                    <p class="text-xs pt-1.5 flex items-center gap-2 text-slate-700">
                        <span class="font-mono font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ $row['sub_section_code'] }}</span>
                        <span>{{ $row['sub_section_name'] }}</span>
                    </p>
                @empty
                    <p class="text-xs text-slate-400 py-2">Sin subsecciones.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Series y retención
            </h3>
            <div class="space-y-1.5 divide-y divide-slate-100">
                @forelse($structure->series ?? [] as $row)
                    @php
                        $disp = strtoupper(trim($row['disposicion_final'] ?? ''));
                        $dispBadge = match($disp) {
                            'CT' => ['bg-emerald-100 text-emerald-700 border-emerald-200', 'CT'],
                            'E'  => ['bg-rose-100 text-rose-700 border-rose-200',     'E'],
                            'M'  => ['bg-blue-100 text-blue-700 border-blue-200',     'M'],
                            'S'  => ['bg-purple-100 text-purple-700 border-purple-200','S'],
                            default => ['bg-slate-100 text-slate-600 border-slate-200', $disp],
                        };
                    @endphp
                    <div class="pt-1.5 space-y-1">
                        <p class="text-xs text-slate-700 flex items-center gap-2 flex-wrap">
                            <span class="font-mono font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ $row['serie_code'] }}</span>
                            <span class="font-medium text-slate-900">{{ $row['serie_name'] }}</span>
                        </p>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center px-2 py-0 rounded-full text-[10px] font-bold border {{ $dispBadge[0] }}">
                                {{ $dispBadge[1] }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">
                                Gestión: <span class="text-slate-600 font-semibold">{{ $row['retencion_gestion'] ?? 0 }} a.</span>
                                · Central: <span class="text-slate-600 font-semibold">{{ $row['retencion_central'] ?? 0 }} a.</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2">Sin series.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Subseries
            </h3>
            <div class="space-y-1.5 divide-y divide-slate-100">
                @forelse($structure->sub_series ?? [] as $row)
                    <p class="text-xs pt-1.5 flex items-center gap-2 text-slate-700">
                        <span class="font-mono font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">{{ $row['sub_serie_code'] }}</span>
                        <span>{{ $row['sub_serie_name'] }}</span>
                    </p>
                @empty
                    <p class="text-xs text-slate-400 py-2">Sin subseries.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

