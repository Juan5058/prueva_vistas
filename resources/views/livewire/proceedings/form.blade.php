<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Banner Header --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="folder" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $proceedingId ? 'Editar expediente' : 'Apertura de expediente' }}</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Ingresa la información general y ubicación física del expediente.</p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">
        {{-- Datos del Expediente --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Información de expediente
            </h3>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Estructura TRD</label>
                    <select wire:model.live="trd_structure_id" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                        @foreach($structures as $structure)
                            <option value="{{ $structure->getKey() }}">{{ $structure->section_code }} — {{ $structure->section_name }}</option>
                        @endforeach
                    </select>
                </div>
                @php($current = $structures->first(fn ($structure) => (string) $structure->getKey() === (string) $trd_structure_id) ?? $structures->first())
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Serie</label>
                    <select wire:model="serie_id" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                        @foreach($current->series ?? [] as $serie)
                            <option value="{{ $serie['serie_id'] }}">{{ $serie['serie_code'] }} — {{ $serie['serie_name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Subserie</label>
                    <select wire:model="sub_serie_id" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                        @foreach($current->sub_series ?? [] as $sub)
                            <option value="{{ $sub['sub_serie_id'] }}">{{ $sub['sub_serie_code'] }} — {{ $sub['sub_serie_name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Número de radicado</label>
                    <input wire:model="file_number" class="w-full px-3.5 py-2.5 text-xs font-mono font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Estado</label>
                    <select wire:model="state" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                        <option>Público</option><option>Privado</option><option>Reservado</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nombre del expediente</label>
                    <input wire:model="name" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Descripción</label>
                    <textarea wire:model="description" rows="3" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Fecha de apertura</label>
                    <input type="date" wire:model="opening_date" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Fecha límite</label>
                    <input type="date" wire:model="deadline" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                </div>
            </div>
        </div>

        {{-- Ubicación física --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                Ubicación física en archivo
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                @foreach(['deposit'=>'Depósito','shelf'=>'Estante','module'=>'Módulo','box'=>'Caja','folder'=>'Carpeta'] as $key => $label)
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 mb-1">{{ $label }}</label>
                        <input wire:model="physical_location.{{ $key }}" class="w-full px-3 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Acciones --}}
        <div class="pt-2 flex justify-end">
            <button class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all cursor-pointer">
                <x-icon name="check" class="w-4 h-4" />
                <span>Guardar expediente</span>
            </button>
        </div>
    </form>
</div>

