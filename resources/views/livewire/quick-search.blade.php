<div class="flex items-center w-full max-w-md" x-data="{ focused: false }">
    <form wire:submit.prevent="search" class="relative w-full flex items-center">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
            <x-icon name="search" class="w-4 h-4" />
        </span>
        <input
            id="global-search-input"
            type="search"
            wire:model="q"
            placeholder="Buscar (Código, Nombre, Expediente, Persona...)"
            maxlength="{{ \App\Support\SearchQuery::MAX_LENGTH }}"
            autocomplete="off"
            @focus="focused = true"
            @blur="focused = false"
            class="w-full pl-9 pr-10 py-2 text-sm bg-slate-800 border border-slate-700 rounded-lg text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/60 transition-all duration-150"
        >
        <button
            type="submit"
            class="absolute right-2 top-1/2 -translate-y-1/2 p-1 rounded text-slate-400 hover:text-white hover:bg-slate-700 transition-colors"
            title="Buscar"
        >
            <x-icon name="search" class="w-3.5 h-3.5" />
        </button>
    </form>
</div>
