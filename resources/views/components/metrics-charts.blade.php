@props(['distribution'])

@php
    $pie = function (array $items): string {
        if ($items === []) {
            return '#e2e8f0 0 100%';
        }
        $stops = [];
        $cursor = 0.0;
        foreach ($items as $item) {
            $next = $cursor + (float) $item['percentage'];
            $stops[] = $item['color'].' '.$cursor.'% '.$next.'%';
            $cursor = $next;
        }

        return implode(', ', $stops);
    };
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            Soporte documental
        </h3>
        @if(count($distribution['bySupport']) > 0)
            <div class="flex items-center gap-5">
                <div class="w-28 h-28 rounded-full shrink-0 border border-slate-100 shadow-inner" style="background: conic-gradient({{ $pie($distribution['bySupport']) }});"></div>
                <div class="space-y-2 text-xs flex-1">
                    @foreach($distribution['bySupport'] as $row)
                        <div class="flex justify-between gap-3">
                            <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $row['color'] }}"></span>{{ $row['label'] }}</span>
                            <span class="font-semibold text-slate-900 font-mono">{{ $row['count'] }} ({{ $row['percentage'] }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-xs text-slate-500">Sin documentos.</p>
        @endif
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            Estado de expedientes
        </h3>
        @if(count($distribution['byState']) > 0)
            <div class="h-36 flex items-end gap-3 pt-2">
                @foreach($distribution['byState'] as $row)
                    <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                        <span class="text-[10px] font-bold text-slate-800 font-mono">{{ $row['count'] }}</span>
                        <div class="w-full rounded-t-lg min-h-1 transition-all duration-300 shadow-sm" style="height: {{ max($row['bar'], 8) }}%; background: {{ $row['color'] }}"></div>
                        <span class="text-[10px] font-medium text-slate-600 text-center leading-tight">{{ $row['label'] }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-500">Sin expedientes.</p>
        @endif
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            Disposición final TRD
        </h3>
        @if(count($distribution['byDisposition']) > 0)
            <div class="flex items-center gap-5">
                <div class="w-28 h-28 rounded-full shrink-0 border border-slate-100 shadow-inner" style="background: conic-gradient({{ $pie($distribution['byDisposition']) }});"></div>
                <div class="space-y-2 text-xs flex-1">
                    @foreach($distribution['byDisposition'] as $row)
                        <div class="flex justify-between gap-3">
                            <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $row['color'] }}"></span>{{ $row['label'] }}</span>
                            <span class="font-semibold text-slate-900 font-mono">{{ $row['count'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-xs text-slate-500">Sin series.</p>
        @endif
    </div>
</div>
