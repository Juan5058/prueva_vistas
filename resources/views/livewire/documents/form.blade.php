<div class="p-6 max-w-3xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">{{ $documentId ? 'Editar documento' : 'Carga de documento PDF' }}</h2>
    <form wire:submit="save" class="bg-white border rounded-xl p-5 space-y-4">
        <div>
            <label class="text-xs font-semibold">Expediente</label>
            <select wire:model="proceedings_id" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                @foreach($proceedings as $proceeding)
                    <option value="{{ $proceeding->getKey() }}">{{ $proceeding->file_number }} — {{ $proceeding->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Nombre</label>
            <input wire:model="name" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            @error('name') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="text-xs font-semibold">Descripción</label>
            <textarea wire:model="description" rows="3" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg"></textarea>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-semibold">Tipo documental</label>
                <input wire:model="document_type" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
            <div>
                <label class="text-xs font-semibold">Soporte</label>
                <select wire:model="support" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    <option>Electrónico</option>
                    <option>Físico</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold">Estado</label>
                <select wire:model="state" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                    <option>Abierto</option>
                    <option>Cerrado</option>
                    <option>Privado</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold">Fecha de creación</label>
                <input type="date" wire:model="document_creation_date" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            </div>
        </div>
        <div>
            <label class="text-xs font-semibold">PDF (máx. 10 MB, MIME application/pdf, Storage::disk('private'), nombre UUID)</label>
            <input type="file" wire:model="file" accept="application/pdf" class="mt-1 text-xs">
            @error('file') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
            <div wire:loading wire:target="file" class="text-xs text-slate-500">Validando archivo...</div>
        </div>
        <button class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg">{{ $documentId ? 'Guardar cambios' : 'Registrar documento' }}</button>
    </form>
</div>
