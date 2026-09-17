<div class="p-6 max-w-5xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">{{ $structureId ? 'Editar estructura TRD' : 'Creación de Estructura TRD' }}</h2>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white border rounded-xl p-5 grid sm:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-semibold">Código de sección</label>
                <input wire:model="section_code" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                @error('section_code') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="text-xs font-semibold">Nombre de sección</label>
                <input wire:model="section_name" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div>
                <label class="text-xs font-semibold">Versión</label>
                <input wire:model="version" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
        </div>
        <div class="bg-white border rounded-xl p-5 space-y-3">
            <div class="flex justify-between"><h3 class="text-sm font-semibold">Subsecciones</h3><button type="button" wire:click="addSubSection" class="text-xs font-medium text-slate-800 hover:text-slate-600">+ Agregar</button></div>
            @foreach($sub_sections as $i => $row)
                <div class="grid sm:grid-cols-2 gap-2">
                    <input wire:model="sub_sections.{{ $i }}.sub_section_code" placeholder="Código" class="px-3 py-2 text-xs border rounded-lg">
                    <input wire:model="sub_sections.{{ $i }}.sub_section_name" placeholder="Nombre" class="px-3 py-2 text-xs border rounded-lg">
                </div>
            @endforeach
        </div>
        <div class="bg-white border rounded-xl p-5 space-y-3">
            <div class="flex justify-between"><h3 class="text-sm font-semibold">Series y retención</h3><button type="button" wire:click="addSerie" class="text-xs font-medium text-slate-800 hover:text-slate-600">+ Agregar</button></div>
            @foreach($series as $i => $row)
                <div class="grid sm:grid-cols-3 gap-2 border-b pb-3">
                    <input wire:model="series.{{ $i }}.serie_code" placeholder="Código" class="px-3 py-2 text-xs border rounded-lg">
                    <input wire:model="series.{{ $i }}.serie_name" placeholder="Nombre" class="px-3 py-2 text-xs border rounded-lg">
                    <select wire:model="series.{{ $i }}.disposicion_final" class="px-3 py-2 text-xs border rounded-lg">
                        <option value="CT">CT · Conservación Total</option>
                        <option value="E">E · Eliminación</option>
                        <option value="M">M · Digitalización</option>
                        <option value="S">S · Selección</option>
                    </select>
                    <input type="number" wire:model="series.{{ $i }}.retencion_gestion" class="px-3 py-2 text-xs border rounded-lg" placeholder="Gestión">
                    <input type="number" wire:model="series.{{ $i }}.retencion_central" class="px-3 py-2 text-xs border rounded-lg" placeholder="Central">
                </div>
            @endforeach
        </div>
        <div class="bg-white border rounded-xl p-5 space-y-3">
            <div class="flex justify-between"><h3 class="text-sm font-semibold">Subseries</h3><button type="button" wire:click="addSubSerie" class="text-xs font-medium text-slate-800 hover:text-slate-600">+ Agregar</button></div>
            @foreach($sub_series as $i => $row)
                <div class="grid sm:grid-cols-2 gap-2">
                    <input wire:model="sub_series.{{ $i }}.sub_serie_code" placeholder="Código" class="px-3 py-2 text-xs border rounded-lg">
                    <input wire:model="sub_series.{{ $i }}.sub_serie_name" placeholder="Nombre" class="px-3 py-2 text-xs border rounded-lg">
                </div>
            @endforeach
        </div>
        <button class="px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Guardar estructura</button>
    </form>
</div>
