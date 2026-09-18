<div class="flex items-center w-full max-w-md">
    <form
        wire:submit.prevent="search"
        class="flex items-center w-full bg-slate-800 border border-slate-700 rounded-lg transition-all duration-150 focus-within:ring-2 focus-within:ring-emerald-500/40 focus-within:border-emerald-500/60"
    >
        {{-- Icono lupa izquierda --}}
        <span class="pl-3 pr-1 shrink-0 text-slate-400 pointer-events-none">
            <x-icon name="search" class="w-4 h-4" />
        </span>

        {{-- Input --}}
        <input
            id="global-search-input"
            type="text"
            wire:model="q"
            placeholder="Buscar (Código, Nombre, Expediente, Persona...)"
            maxlength="{{ \App\Support\SearchQuery::MAX_LENGTH }}"
            autocomplete="off"
            class="flex-1 py-2 pr-1 text-sm bg-transparent text-slate-200 placeholder-slate-500 focus:outline-none"
        >

        {{-- Botón buscar --}}
        <button
            type="submit"
            class="pr-2.5 pl-1 shrink-0 text-slate-400 hover:text-white transition-colors"
            title="Buscar"
        >
            <x-icon name="search" class="w-3.5 h-3.5" />
        </button>
    </form>
</div>
