<div class="p-6 max-w-5xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">{{ $proceedingId ? 'Editar expediente' : 'Apertura de expediente' }}</h2>
    <form wire:submit="save" class="space-y-5">
        <div class="bg-white border rounded-xl p-5 grid sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold">Estructura TRD</label>
                <select wire:model.live="trd_structure_id" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    @foreach($structures as $structure)
                        <option value="{{ $structure->getKey() }}">{{ $structure->section_code }} — {{ $structure->section_name }}</option>
                    @endforeach
                </select>
            </div>
            @php($current = $structures->first(fn ($structure) => (string) $structure->getKey() === (string) $trd_structure_id) ?? $structures->first())
            <div>
                <label class="text-xs font-semibold">Serie</label>
                <select wire:model="serie_id" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    @foreach($current->series ?? [] as $serie)
                        <option value="{{ $serie['serie_id'] }}">{{ $serie['serie_code'] }} — {{ $serie['serie_name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold">Subserie</label>
                <select wire:model="sub_serie_id" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    @foreach($current->sub_series ?? [] as $sub)
                        <option value="{{ $sub['sub_serie_id'] }}">{{ $sub['sub_serie_code'] }} — {{ $sub['sub_serie_name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold">Número</label>
                <input wire:model="file_number" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg font-mono">
            </div>
            <div>
                <label class="text-xs font-semibold">Estado</label>
                <select wire:model="state" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    <option>Público</option><option>Privado</option><option>Reservado</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold">Nombre</label>
                <input wire:model="name" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold">Descripción</label>
                <textarea wire:model="description" rows="3" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg"></textarea>
            </div>
            <div>
                <label class="text-xs font-semibold">Apertura</label>
                <input type="date" wire:model="opening_date" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div>
                <label class="text-xs font-semibold">Límite</label>
                <input type="date" wire:model="deadline" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
        </div>
        <div class="bg-white border rounded-xl p-5 grid sm:grid-cols-5 gap-3">
            <h3 class="sm:col-span-5 text-sm font-semibold">Ubicación física</h3>
            @foreach(['deposit'=>'Depósito','shelf'=>'Estante','module'=>'Módulo','box'=>'Caja','folder'=>'Carpeta'] as $key => $label)
                <div>
                    <label class="text-xs">{{ $label }}</label>
                    <input wire:model="physical_location.{{ $key }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                </div>
            @endforeach
        </div>
        <button class="px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Guardar expediente</button>
    </form>
</div>
