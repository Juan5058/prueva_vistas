@php
    $crumbs = [
        ['label' => 'Capa 1 · Control TRD', 'href' => route('trd.index')],
        ['label' => $structure->section_name, 'href' => $layer === 'organic' ? null : route('trd.show', $structure->getKey())],
    ];
    if (in_array($layer, ['series', 'holdings', 'expediente'], true)) {
        $crumbs[] = [
            'label' => $dependencyName,
            'href' => $layer === 'series' ? null : route('trd.series', [$structure->getKey(), $dependency]),
        ];
    }
    if (in_array($layer, ['holdings', 'expediente'], true) && $serie) {
        $crumbs[] = [
            'label' => $serie['serie_name'] ?? 'Serie',
            'href' => $layer === 'holdings' ? null : route('trd.holdings', [$structure->getKey(), $dependency, $serieId]),
        ];
    }
    if ($layer === 'expediente' && $proceeding) {
        $crumbs[] = ['label' => $proceeding->file_number, 'href' => null];
    }
    $layerMeta = [
        'organic' => ['2', 'Estructura orgánica'],
        'series' => ['3', 'Series y subseries'],
        'holdings' => ['4', 'Expedientes'],
        'expediente' => ['4', 'Tipos documentales'],
    ][$layer];
@endphp

<div class="p-6 max-w-7xl mx-auto space-y-6">
    <x-trd-crumbs :items="$crumbs" />

    @unless($structure->is_active)
        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium">
            Esta TRD está inactiva. La consulta está permitida; solo un superusuario puede reactivarla en la capa 1.
        </div>
    @endunless

    <div class="pb-4 border-b border-slate-200">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Capa {{ $layerMeta[0] }} de 4 · {{ $layerMeta[1] }}</p>
        <h2 class="text-xl font-bold">{{ $structure->section_name }}</h2>
        <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $structure->section_code }} · {{ $structure->version }}</p>
    </div>

    @if($layer === 'organic')
        <p class="text-xs text-slate-500">Seleccione la sección o una subsección para ver series y subseries.</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('trd.series', [$structure->getKey(), 'seccion']) }}" wire:navigate class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-400">
                <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Sección</p>
                <h3 class="text-sm font-semibold mt-1">{{ $structure->section_name }}</h3>
                <p class="font-mono text-[11px] text-slate-500 mt-1">{{ $structure->section_code }}</p>
                <p class="text-[11px] text-slate-500 mt-3">{{ count($structure->sub_sections ?? []) }} subsecciones · {{ count($structure->series ?? []) }} series</p>
            </a>
            @forelse($structure->sub_sections ?? [] as $row)
                <a href="{{ route('trd.series', [$structure->getKey(), $row['sub_section_id']]) }}" wire:navigate class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-400">
                    <p class="text-[11px] uppercase tracking-wide text-slate-400 font-semibold">Subsección</p>
                    <h3 class="text-sm font-semibold mt-1">{{ $row['sub_section_name'] }}</h3>
                    <p class="font-mono text-[11px] text-slate-500 mt-1">{{ $row['sub_section_code'] }}</p>
                </a>
            @empty
            @endforelse
        </div>
        @if(($structure->sub_sections ?? []) === [])
            <p class="text-xs text-slate-500">No hay subsecciones. Use la tarjeta de sección para continuar.</p>
        @endif
    @endif

    @if($layer === 'series')
        <p class="text-xs text-slate-500">Dependencia: <span class="font-medium text-slate-700">{{ $dependencyName }}</span>. Elija una serie para ver expedientes.</p>
        <div class="grid lg:grid-cols-2 gap-4">
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b bg-slate-50/80 text-sm font-semibold">Series</div>
                <div class="divide-y">
                    @forelse($structure->series ?? [] as $row)
                        <a href="{{ route('trd.holdings', [$structure->getKey(), $dependency, $row['serie_id']]) }}" wire:navigate class="block px-4 py-3 hover:bg-slate-50">
                            <p class="text-sm font-medium">{{ $row['serie_name'] }}</p>
                            <p class="font-mono text-[11px] text-slate-500 mt-0.5">{{ $row['serie_code'] }} · {{ $row['disposicion_final'] ?? 'CT' }} · AG {{ $row['retencion_gestion'] ?? 0 }} / AC {{ $row['retencion_central'] ?? 0 }}</p>
                        </a>
                    @empty
                        <p class="p-4 text-xs text-slate-500">No hay series en esta TRD.</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 border-b bg-slate-50/80 text-sm font-semibold">Subseries</div>
                <div class="divide-y">
                    @forelse($structure->sub_series ?? [] as $row)
                        <div class="px-4 py-3">
                            <p class="text-sm font-medium">{{ $row['sub_serie_name'] }}</p>
                            <p class="font-mono text-[11px] text-slate-500 mt-0.5">{{ $row['sub_serie_code'] }}</p>
                        </div>
                    @empty
                        <p class="p-4 text-xs text-slate-500">No hay subseries.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    @if($layer === 'holdings')
        <div class="flex items-center justify-between">
            <p class="text-xs text-slate-500">Serie <span class="font-medium text-slate-700">{{ $serie['serie_name'] ?? '—' }}</span> ({{ $serie['serie_code'] ?? '' }})</p>
            @if(auth()->user()->hasPermission('proceedings.create') && $structure->is_active)
                <a href="{{ route('proceedings.create') }}" wire:navigate class="px-3 py-1.5 text-xs text-white bg-slate-800 rounded-lg">Nuevo expediente</a>
            @endif
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($proceedings as $item)
                <a href="{{ route('trd.expediente', [$structure->getKey(), $dependency, $serieId, $item->getKey()]) }}" wire:navigate class="bg-white border border-slate-200 rounded-xl p-5 hover:border-slate-400">
                    <p class="font-mono text-[11px] text-slate-500">{{ $item->file_number }}</p>
                    <h3 class="text-sm font-semibold mt-1">{{ $item->name }}</h3>
                    <p class="text-[11px] text-slate-500 mt-2">{{ $item->state }} · {{ $item->documents_count }} documento(s)</p>
                </a>
            @empty
                <p class="text-xs text-slate-500 col-span-full">No hay expedientes en esta serie.</p>
            @endforelse
        </div>
    @endif

    @if($layer === 'expediente' && $proceeding)
        <p class="text-xs text-slate-500">{{ $proceeding->name }} · {{ $proceeding->serie_name }} / {{ $proceeding->sub_serie_name }}</p>
        <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b bg-slate-50/80">
                <h3 class="text-sm font-semibold">Tipos documentales</h3>
                <p class="text-[11px] text-slate-500">Abra el PDF si el usuario tiene permiso de descarga.</p>
            </div>
            <table class="w-full text-xs">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="p-3 text-left">Documento</th>
                        <th class="p-3 text-left">Tipo documental</th>
                        <th class="p-3">Soporte</th>
                        <th class="p-3"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($proceeding->documents as $document)
                    <tr class="border-t border-slate-100">
                        <td class="p-3 font-medium">{{ $document->name }}</td>
                        <td class="p-3">{{ $document->document_type }}</td>
                        <td class="p-3 text-center">{{ $document->support }}</td>
                        <td class="p-3">
                            <div class="flex justify-end gap-1">
                                <x-icon-action href="{{ route('documents.show', $document->getKey()) }}" tooltip="Ficha" variant="info">
                                    <x-icon name="eye" class="w-4 h-4" />
                                </x-icon-action>
                                @if(auth()->user()->hasPermission('documents.download') && $document->file_path)
                                    <x-icon-action href="{{ route('documents.download', $document->getKey()) }}" :navigate="false" tooltip="Abrir PDF">
                                        <x-icon name="download" class="w-4 h-4" />
                                    </x-icon-action>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-6 text-center text-slate-500">Sin tipos documentales en este expediente.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
