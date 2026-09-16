<x-layouts.app>
@php($isEdit = $structure->exists)
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <div class="flex items-center gap-3 pb-4 border-b">
        <a href="{{ route('trd.index') }}" class="p-2 rounded-lg border border-slate-200 text-slate-600">←</a>
        <div>
            <h2 class="text-xl font-bold">{{ $isEdit ? 'Editar estructura TRD' : 'Creación de Estructura TRD' }}</h2>
            <p class="text-xs text-slate-500">Sección, subsecciones, series y tiempos de retención</p>
        </div>
    </div>
    <form method="POST" action="{{ $isEdit ? route('trd.update', $structure) : route('trd.store') }}" class="space-y-6">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div class="bg-white border rounded-xl p-5 grid sm:grid-cols-3 gap-4">
            <div>
                <label class="text-xs font-semibold">Código de sección</label>
                <input name="section_code" value="{{ old('section_code', $structure->section_code) }}" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div class="sm:col-span-1">
                <label class="text-xs font-semibold">Nombre de sección</label>
                <input name="section_name" value="{{ old('section_name', $structure->section_name) }}" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div>
                <label class="text-xs font-semibold">Versión</label>
                <input name="version" value="{{ old('version', $structure->version) }}" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 space-y-3" data-repeat-root>
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold">Subsecciones</h3>
                <button type="button" class="repeat-add text-xs text-blue-600">+ Agregar</button>
            </div>
            <div data-repeat-list>
                @foreach(old('sub_sections', $structure->sub_sections ?: [['sub_section_code'=>'','sub_section_name'=>'']]) as $i => $row)
                    <div class="grid sm:grid-cols-2 gap-2 repeat-row">
                        <input name="sub_sections[{{ $i }}][sub_section_id]" type="hidden" value="{{ $row['sub_section_id'] ?? '' }}">
                        <input name="sub_sections[{{ $i }}][sub_section_code]" value="{{ $row['sub_section_code'] ?? '' }}" placeholder="Código" class="px-3 py-2 text-xs border rounded-lg">
                        <input name="sub_sections[{{ $i }}][sub_section_name]" value="{{ $row['sub_section_name'] ?? '' }}" placeholder="Nombre" class="px-3 py-2 text-xs border rounded-lg">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 space-y-3" data-repeat-root>
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold">Series</h3>
                <button type="button" class="repeat-add text-xs text-blue-600">+ Agregar</button>
            </div>
            <div data-repeat-list>
                @foreach(old('series', $structure->series ?: [['serie_code'=>'','serie_name'=>'']]) as $i => $row)
                    <div class="grid sm:grid-cols-2 gap-2 repeat-row">
                        <input name="series[{{ $i }}][serie_id]" type="hidden" value="{{ $row['serie_id'] ?? '' }}">
                        <input name="series[{{ $i }}][serie_code]" value="{{ $row['serie_code'] ?? '' }}" placeholder="Código" class="px-3 py-2 text-xs border rounded-lg">
                        <input name="series[{{ $i }}][serie_name]" value="{{ $row['serie_name'] ?? '' }}" placeholder="Nombre" class="px-3 py-2 text-xs border rounded-lg">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white border rounded-xl p-5 space-y-3" data-repeat-root>
            <div class="flex justify-between items-center">
                <h3 class="text-sm font-semibold">Subseries y retención</h3>
                <button type="button" class="repeat-add text-xs text-blue-600">+ Agregar</button>
            </div>
            <div data-repeat-list>
                @foreach(old('sub_series', $structure->sub_series ?: [[]]) as $i => $row)
                    <div class="grid sm:grid-cols-2 gap-2 repeat-row border-b border-slate-100 pb-3 mb-3">
                        <input name="sub_series[{{ $i }}][sub_serie_id]" type="hidden" value="{{ $row['sub_serie_id'] ?? '' }}">
                        <input name="sub_series[{{ $i }}][sub_serie_code]" value="{{ $row['sub_serie_code'] ?? '' }}" placeholder="Código" class="px-3 py-2 text-xs border rounded-lg">
                        <input name="sub_series[{{ $i }}][sub_serie_name]" value="{{ $row['sub_serie_name'] ?? '' }}" placeholder="Nombre" class="px-3 py-2 text-xs border rounded-lg">
                        <input type="number" name="sub_series[{{ $i }}][retention_management_years]" value="{{ $row['retention_management_years'] ?? 3 }}" class="px-3 py-2 text-xs border rounded-lg" placeholder="Años gestión">
                        <input type="number" name="sub_series[{{ $i }}][retention_central_years]" value="{{ $row['retention_central_years'] ?? 15 }}" class="px-3 py-2 text-xs border rounded-lg" placeholder="Años central">
                        <select name="sub_series[{{ $i }}][final_disposition]" class="px-3 py-2 text-xs border rounded-lg">
                            @foreach(['Conservación Total','Eliminación','Digitalización','Selección'] as $opt)
                                <option @selected(($row['final_disposition'] ?? '') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <input name="sub_series[{{ $i }}][procedure]" value="{{ $row['procedure'] ?? '' }}" placeholder="Procedimiento" class="px-3 py-2 text-xs border rounded-lg sm:col-span-2">
                    </div>
                @endforeach
            </div>
        </div>

        <button class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg">Guardar estructura</button>
    </form>
</div>
</x-layouts.app>
