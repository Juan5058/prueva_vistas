@props([
    'paginator',
    'perPage' => '10',
    'customPerPage' => '',
])

@php
    $total = $paginator->total();
    $currentPage = $paginator->currentPage();
    $lastPage = max(1, $paginator->lastPage());

    // Generate smart page links window (up to 7 pages total)
    $pages = [];
    if ($lastPage <= 7) {
        for ($i = 1; $i <= $lastPage; $i++) {
            $pages[] = $i;
        }
    } else {
        if ($currentPage <= 4) {
            $pages = [1, 2, 3, 4, 5, '...', $lastPage];
        } elseif ($currentPage >= $lastPage - 3) {
            $pages = [1, '...', $lastPage - 4, $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
        } else {
            $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
        }
    }
@endphp

<div class="px-6 py-4 bg-slate-50/70 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-600 rounded-b-2xl">
    
    {{-- Left: Record Stats --}}
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 shadow-2xs font-sans">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
            @if($total > 0)
                Mostrando <strong class="font-bold text-slate-900 font-mono">{{ $paginator->firstItem() }}</strong> - <strong class="font-bold text-slate-900 font-mono">{{ $paginator->lastItem() }}</strong> de <strong class="font-bold text-slate-900 font-mono">{{ $total }}</strong> registros
            @else
                Mostrando <strong class="font-bold text-slate-900 font-mono">0</strong> registros
            @endif
        </span>
    </div>

    {{-- Center: Per Page Selector & Custom Input --}}
    <div class="flex items-center gap-2 flex-wrap justify-center">
        <span class="text-slate-500 font-medium">Mostrar:</span>
        <select wire:model.live="perPage" class="px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-semibold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-2xs">
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="custom">Personalizado</option>
        </select>

        @if($perPage === 'custom')
            <div class="flex items-center gap-1 animate-fadeIn">
                <input type="number"
                       min="1"
                       max="1000"
                       wire:model.live.debounce.400ms="customPerPage"
                       placeholder="Cant."
                       class="w-20 px-2.5 py-1.5 border border-slate-300 rounded-lg text-xs font-bold font-mono text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-2xs"
                />
            </div>
        @endif
        <span class="text-slate-500 font-medium">registros por página</span>
    </div>

    {{-- Right: Page Navigation Buttons --}}
    @if($lastPage > 1)
        <nav class="flex items-center gap-1.5">
            {{-- Previous Button --}}
            @if($paginator->onFirstPage())
                <button disabled type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed opacity-60">
                    <x-icon name="chevron-left" class="w-3.5 h-3.5" />
                    <span>Anterior</span>
                </button>
            @else
                <button type="button" wire:click="previousPage" wire:loading.attr="disabled" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300 hover:bg-slate-100 hover:text-slate-900 active:scale-95 transition-all shadow-2xs">
                    <x-icon name="chevron-left" class="w-3.5 h-3.5" />
                    <span>Anterior</span>
                </button>
            @endif

            {{-- Numbered Page Buttons --}}
            <div class="hidden sm:flex items-center gap-1">
                @foreach($pages as $p)
                    @if($p === '...')
                        <span class="px-2 py-1 text-slate-400 font-mono text-xs">...</span>
                    @elseif($p == $currentPage)
                        <button disabled type="button" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-blue-600 text-white shadow-sm shadow-blue-500/30">
                            {{ $p }}
                        </button>
                    @else
                        <button type="button" wire:click="gotoPage({{ $p }})" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 hover:text-slate-900 transition-all shadow-2xs">
                            {{ $p }}
                        </button>
                    @endif
                @endforeach
            </div>

            {{-- Next Button --}}
            @if($paginator->hasMorePages())
                <button type="button" wire:click="nextPage" wire:loading.attr="disabled" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white text-slate-700 border border-slate-300 hover:bg-slate-100 hover:text-slate-900 active:scale-95 transition-all shadow-2xs">
                    <span>Siguiente</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                </button>
            @else
                <button disabled type="button" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed opacity-60">
                    <span>Siguiente</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5" />
                </button>
            @endif
        </nav>
    @else
        <div class="hidden sm:block text-slate-400 text-xs font-mono">Página 1 de 1</div>
    @endif

</div>
