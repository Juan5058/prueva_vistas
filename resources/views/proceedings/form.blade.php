<x-layouts.app>
@php
    $isEdit = $proceeding->exists;
    $structuresJson = $structures->map(fn ($s) => [
        'id' => $s->id,
        'section_code' => $s->section_code,
        'series' => $s->series ?? [],
        'sub_series' => $s->sub_series ?? [],
    ]);
@endphp
<div class="p-6 max-w-5xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">{{ $isEdit ? 'Editar expediente' : 'Apertura de expediente' }}</h2>
    <form method="POST" action="{{ $isEdit ? route('proceedings.update', $proceeding) : route('proceedings.store') }}" class="space-y-5" id="proceeding-form">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <script type="application/json" id="trd-structures-data">{!! $structuresJson->toJson() !!}</script>
        <div class="bg-white border rounded-xl p-5 grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold">Estructura TRD</label>
                <select name="trd_structure_id" id="trd_structure_id" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    @foreach($structures as $structure)
                        <option value="{{ $structure->id }}" @selected(old('trd_structure_id', $proceeding->trd_structure_id) == $structure->id)>
                            {{ $structure->section_code }} — {{ $structure->section_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold">Serie</label>
                <select name="serie_id" id="serie_id" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg" data-selected="{{ old('serie_id', $proceeding->serie_id) }}"></select>
            </div>
            <div>
                <label class="text-xs font-semibold">Subserie</label>
                <select name="sub_serie_id" id="sub_serie_id" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg" data-selected="{{ old('sub_serie_id', $proceeding->sub_serie_id) }}"></select>
            </div>
            <div>
                <label class="text-xs font-semibold">Número de expediente</label>
                <input name="file_number" required value="{{ old('file_number', $proceeding->file_number) }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg font-mono">
            </div>
            <div>
                <label class="text-xs font-semibold">Estado</label>
                <select name="state" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    @foreach(['Público','Privado','Reservado'] as $state)
                        <option @selected(old('state', $proceeding->state) === $state)>{{ $state }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold">Nombre</label>
                <input name="name" required value="{{ old('name', $proceeding->name) }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold">Descripción</label>
                <textarea name="description" rows="3" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">{{ old('description', $proceeding->description) }}</textarea>
            </div>
            <div>
                <label class="text-xs font-semibold">Fecha de apertura</label>
                <input type="date" name="opening_date" value="{{ old('opening_date', optional($proceeding->opening_date)->toDateString()) }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div>
                <label class="text-xs font-semibold">Fecha límite</label>
                <input type="date" name="deadline" value="{{ old('deadline', optional($proceeding->deadline)->toDateString()) }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
        </div>
        <div class="bg-white border rounded-xl p-5 grid sm:grid-cols-5 gap-3">
            <h3 class="sm:col-span-5 text-sm font-semibold">Ubicación física</h3>
            @foreach(['deposit'=>'Depósito','shelf'=>'Estante','module'=>'Módulo','box'=>'Caja','folder'=>'Carpeta'] as $key => $label)
                <div>
                    <label class="text-xs">{{ $label }}</label>
                    <input name="physical_location[{{ $key }}]" value="{{ old("physical_location.$key", data_get($proceeding->physical_location, $key)) }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                </div>
            @endforeach
        </div>
        <button class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg">Guardar expediente</button>
    </form>
</div>
</x-layouts.app>
