<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b">
        <div>
            <p class="text-xs font-mono text-slate-700">{{ $structure->section_code }} · {{ $structure->version }}</p>
            <h2 class="text-xl font-bold">{{ $structure->section_name }}</h2>
        </div>
        @if(auth()->user()->hasPermission('trd.edit'))
            <x-icon-action href="{{ route('trd.edit', $structure->getKey()) }}" tooltip="Editar">
                <x-icon name="pencil" class="w-4 h-4" />
            </x-icon-action>
        @endif
    </div>
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white border rounded-xl p-4">
            <h3 class="text-sm font-semibold mb-2">Subsecciones</h3>
            @foreach($structure->sub_sections ?? [] as $row)
                <p class="text-xs py-1"><span class="font-mono">{{ $row['sub_section_code'] }}</span> {{ $row['sub_section_name'] }}</p>
            @endforeach
        </div>
        <div class="bg-white border rounded-xl p-4">
            <h3 class="text-sm font-semibold mb-2">Series</h3>
            @foreach($structure->series ?? [] as $row)
                <p class="text-xs py-1"><span class="font-mono">{{ $row['serie_code'] }}</span> {{ $row['serie_name'] }} · {{ $row['disposicion_final'] ?? '' }} ({{ $row['retencion_gestion'] ?? 0 }}/{{ $row['retencion_central'] ?? 0 }})</p>
            @endforeach
        </div>
        <div class="bg-white border rounded-xl p-4">
            <h3 class="text-sm font-semibold mb-2">Subseries</h3>
            @foreach($structure->sub_series ?? [] as $row)
                <p class="text-xs py-1"><span class="font-mono">{{ $row['sub_serie_code'] }}</span> {{ $row['sub_serie_name'] }}</p>
            @endforeach
        </div>
    </div>
</div>
