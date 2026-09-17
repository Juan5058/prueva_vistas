<div class="p-6 max-w-4xl mx-auto space-y-6">

    {{-- Banner Header --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="file" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $documentId ? 'Editar documento' : 'Carga de documento PDF' }}</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Gestión de adjuntos privados y metadatos del expediente.</p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-5">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Expediente asociado</label>
            <select wire:model="proceedings_id" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                @foreach($proceedings as $proceeding)
                    <option value="{{ $proceeding->getKey() }}">{{ $proceeding->file_number }} — {{ $proceeding->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nombre del documento</label>
            <input wire:model="name" placeholder="Ej. Acta de Inicio de Auditoría" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
            @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Descripción</label>
            <textarea wire:model="description" rows="3" placeholder="Detalles u observaciones del documento..." class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all"></textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tipo documental</label>
                <input wire:model="document_type" placeholder="Ej. Informe, Oficio, Resolución" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Soporte</label>
                <select wire:model="support" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                    <option>Electrónico</option>
                    <option>Físico</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Estado</label>
                <select wire:model="state" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
                    <option>Abierto</option>
                    <option>Cerrado</option>
                    <option>Privado</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Fecha de creación</label>
                <input type="date" wire:model="document_creation_date" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-slate-800 transition-all">
            </div>
        </div>

        <div class="p-4 bg-slate-50/60 border border-slate-200 rounded-xl space-y-2">
            <label class="block text-xs font-semibold text-slate-700">Archivo PDF (máx. 10 MB)</label>
            <input type="file" wire:model="file" accept="application/pdf" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
            @error('file') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
            <div wire:loading wire:target="file" class="text-xs text-blue-600 font-medium">Validando y procesando archivo...</div>
        </div>

        <div class="pt-2 flex justify-end">
            <button class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all cursor-pointer">
                <x-icon name="check" class="w-4 h-4" />
                <span>{{ $documentId ? 'Guardar cambios' : 'Registrar documento' }}</span>
            </button>
        </div>
<<<<<<< HEAD
=======
        <button class="px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg">{{ $documentId ? 'Guardar cambios' : 'Registrar documento' }}</button>
>>>>>>> b167af4246a9b21d0efbbd3ab6a5ea5bb6bf4537
    </form>
</div>

