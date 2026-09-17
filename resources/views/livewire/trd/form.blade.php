<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="trd-structure" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $structureId ? 'Editar estructura TRD' : 'Creación de Estructura TRD' }}</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Configura las secciones, subsecciones, series y retenciones documentales.</p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Datos Principales --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Información general de la sección
            </h3>
            <div class="grid sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Código de sección</label>
                    <input wire:model="section_code" class="w-full px-3.5 py-2 text-xs font-mono font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                    @error('section_code') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nombre de sección</label>
                    <input wire:model="section_name" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Versión</label>
                    <input wire:model="version" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                </div>
            </div>
        </div>

        {{-- Subsecciones --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Subsecciones
                </h3>
                <button type="button" wire:click="addSubSection" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors shadow-sm">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Agregar</span>
                </button>
            </div>
            <div class="space-y-3">
                @foreach($sub_sections as $i => $row)
                    <div class="grid sm:grid-cols-2 gap-3 p-3 bg-slate-50/60 border border-slate-200 rounded-xl">
                        <input wire:model="sub_sections.{{ $i }}.sub_section_code" placeholder="Código" class="px-3.5 py-2 text-xs font-mono font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                        <input wire:model="sub_sections.{{ $i }}.sub_section_name" placeholder="Nombre" class="px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Series y retención --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Series y retención documental
                </h3>
                <button type="button" wire:click="addSerie" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors shadow-sm">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Agregar</span>
                </button>
            </div>
            <div class="space-y-3">
                @foreach($series as $i => $row)
                    <div class="p-4 bg-slate-50/60 border border-slate-200 rounded-xl space-y-3">
                        <div class="grid sm:grid-cols-3 gap-3">
                            <input wire:model="series.{{ $i }}.serie_code" placeholder="Código de serie" class="px-3.5 py-2 text-xs font-mono font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                            <input wire:model="series.{{ $i }}.serie_name" placeholder="Nombre de serie" class="px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                            <select wire:model="series.{{ $i }}.disposicion_final" class="px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                                <option value="CT">CT · Conservación Total</option>
                                <option value="E">E · Eliminación</option>
                                <option value="M">M · Digitalización</option>
                                <option value="S">S · Selección</option>
                            </select>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Retención Gestión (Años)</label>
                                <input type="number" wire:model="series.{{ $i }}.retencion_gestion" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800" placeholder="Años en gestión">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">Retención Central (Años)</label>
                                <input type="number" wire:model="series.{{ $i }}.retencion_central" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800" placeholder="Años en central">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Subseries --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    Subseries documentales
                </h3>
                <button type="button" wire:click="addSubSerie" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors shadow-sm">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Agregar</span>
                </button>
            </div>
            <div class="space-y-3">
                @foreach($sub_series as $i => $row)
                    <div class="grid sm:grid-cols-2 gap-3 p-3 bg-slate-50/60 border border-slate-200 rounded-xl">
                        <input wire:model="sub_series.{{ $i }}.sub_serie_code" placeholder="Código" class="px-3.5 py-2 text-xs font-mono font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                        <input wire:model="sub_series.{{ $i }}.sub_serie_name" placeholder="Nombre" class="px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Acciones del formulario --}}
        <div class="pt-2 flex justify-end">
            <button class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all cursor-pointer">
                <x-icon name="check" class="w-4 h-4" />
                <span>Guardar estructura</span>
            </button>
        </div>
    </form>
</div>

