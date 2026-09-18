<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- Page Header --}}
    <div class="pb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight">Resultados Globales</h2>
            @if(filled($term))
                <p class="text-xs text-slate-500 mt-0.5">
                    Resultados para: <span class="font-semibold text-slate-700">«{{ $term }}»</span>
                </p>
            @else
                <p class="text-xs text-slate-500 mt-0.5">Ingresa un término de búsqueda o aplica filtros.</p>
            @endif
        </div>
        @if($ready && !$error && $total > 0)
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="bg-slate-100 border border-slate-200 px-2.5 py-1 rounded-full font-semibold text-slate-700">
                    {{ $total }} resultado{{ $total !== 1 ? 's' : '' }} encontrado{{ $total !== 1 ? 's' : '' }}
                </span>
            </div>
        @endif
    </div>

    {{-- Filters Panel --}}
    <div class="bg-white p-5 rounded-xl border border-slate-200 space-y-4">
        <h3 class="text-sm font-bold text-slate-900">Filtros</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Razón Social / Nombre --}}
            <div>
                <label for="gs-nombre" class="block text-xs font-medium text-slate-600 mb-1">
                    Razón Social / Nombre
                </label>
                <input
                    id="gs-nombre"
                    type="text"
                    wire:model.defer="nombre"
                    placeholder="Razón Social / Nombre"
                    maxlength="{{ \App\Support\SearchQuery::MAX_LENGTH }}"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-800/20 focus:border-slate-500"
                    autocomplete="off"
                >
            </div>
            {{-- Identificación --}}
            <div>
                <label for="gs-id" class="block text-xs font-medium text-slate-600 mb-1">
                    Identificación
                </label>
                <input
                    id="gs-id"
                    type="text"
                    wire:model.defer="identificacion"
                    placeholder="Identificación"
                    maxlength="{{ \App\Support\SearchQuery::MAX_LENGTH }}"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-800/20 focus:border-slate-500"
                    autocomplete="off"
                >
            </div>
            {{-- Módulo --}}
            <div>
                <label for="gs-modulo" class="block text-xs font-medium text-slate-600 mb-1">
                    Módulo
                </label>
                <select
                    id="gs-modulo"
                    wire:model.defer="modulo"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-800/20 focus:border-slate-500 bg-white"
                >
                    @foreach($modulosDisponibles as $val => $label)
                        <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex items-center gap-2 pt-1">
            <button
                type="button"
                wire:click="buscar"
                class="px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors"
            >
                Buscar
            </button>
            <button
                type="button"
                wire:click="limpiar"
                class="px-4 py-2 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition-colors"
            >
                Limpiar filtros
            </button>
        </div>
    </div>

    {{-- Results Area --}}
    <div wire:loading.class="opacity-60 pointer-events-none" class="transition-opacity duration-200">

        {{-- Loading State --}}
        <div wire:loading wire:target="buscar,limpiar,modulo,nombre,identificacion,q" class="flex items-center justify-center py-12">
            <div class="flex items-center gap-3 text-sm text-slate-500">
                <svg class="w-4 h-4 animate-spin text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                Buscando resultados…
            </div>
        </div>

        {{-- Error State --}}
        @if($error)
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-5 text-center">
                <p class="text-sm font-medium text-rose-700">No fue posible realizar la búsqueda. Intenta nuevamente.</p>
            </div>

        {{-- Empty State (no query) --}}
        @elseif(!$ready)
            <div class="bg-white border border-slate-200 rounded-xl p-10 text-center">
                <x-icon name="search" class="w-8 h-8 text-slate-300 mx-auto mb-3" />
                <p class="text-sm font-medium text-slate-500">Usa el buscador del encabezado o los filtros de arriba para encontrar registros.</p>
            </div>

        {{-- No Results --}}
        @elseif($ready && count($items) === 0)
            <div class="bg-white border border-slate-200 rounded-xl p-10 text-center">
                <x-icon name="search" class="w-8 h-8 text-slate-300 mx-auto mb-3" />
                <p class="text-sm font-medium text-slate-500">No encontramos resultados para tu búsqueda.</p>
                <p class="text-xs text-slate-400 mt-1">Prueba con otro término o ajusta los filtros.</p>
            </div>

        {{-- Results Table --}}
        @elseif($ready && count($items) > 0)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Módulo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Identificación</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Razón Social</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Expediente</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($items as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <span class="text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                            {{ $item['module'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-xs text-slate-500 max-w-[10rem] truncate" title="{{ $item['meta'] }}">
                                        {{ $item['meta'] }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-slate-900 max-w-[14rem] truncate" title="{{ $item['title'] }}">
                                        {{ $item['title'] }}
                                    </td>
                                    <td class="px-4 py-3 text-xs text-slate-500">
                                        @if($item['module'] === 'Expediente')
                                            {{ $item['meta'] }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <a
                                            href="{{ $item['url'] }}"
                                            wire:navigate
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-md transition-colors"
                                        >
                                            <x-icon name="eye" class="w-3 h-3" />
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($lastPage > 1)
                    <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>
                            Mostrando {{ (($currentPage - 1) * $perPage) + 1 }}–{{ min($currentPage * $perPage, $total) }} de {{ $total }} resultado{{ $total !== 1 ? 's' : '' }}
                        </span>
                        <div class="flex items-center gap-1">
                            {{-- Anterior --}}
                            @if($currentPage > 1)
                                <button
                                    type="button"
                                    wire:click="previousPage"
                                    class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 border border-slate-200 font-medium transition-colors"
                                >
                                    Anterior
                                </button>
                            @endif

                            {{-- Page numbers --}}
                            @php
                                $start = max(1, $currentPage - 2);
                                $end   = min($lastPage, $currentPage + 2);
                            @endphp

                            @if($start > 1)
                                <button type="button" wire:click="gotoPage(1)" class="px-2.5 py-1 rounded-md hover:bg-slate-100 border border-transparent transition-colors">1</button>
                                @if($start > 2)
                                    <span class="px-1">…</span>
                                @endif
                            @endif

                            @for($p = $start; $p <= $end; $p++)
                                <button
                                    type="button"
                                    wire:click="gotoPage({{ $p }})"
                                    @class([
                                        'px-2.5 py-1 rounded-md border font-medium transition-colors',
                                        'bg-slate-800 text-white border-slate-800' => $p === $currentPage,
                                        'hover:bg-slate-100 border-transparent' => $p !== $currentPage,
                                    ])
                                >{{ $p }}</button>
                            @endfor

                            @if($end < $lastPage)
                                @if($end < $lastPage - 1)
                                    <span class="px-1">…</span>
                                @endif
                                <button type="button" wire:click="gotoPage({{ $lastPage }})" class="px-2.5 py-1 rounded-md hover:bg-slate-100 border border-transparent transition-colors">{{ $lastPage }}</button>
                            @endif

                            {{-- Siguiente --}}
                            @if($currentPage < $lastPage)
                                <button
                                    type="button"
                                    wire:click="nextPage"
                                    class="px-2.5 py-1 rounded-md bg-slate-100 hover:bg-slate-200 border border-slate-200 font-medium transition-colors"
                                >
                                    Siguiente
                                </button>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="px-4 py-3 border-t border-slate-100 text-xs text-slate-500">
                        Mostrando {{ $total }} resultado{{ $total !== 1 ? 's' : '' }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
