<x-layouts.app>
<div class="p-6 max-w-3xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">Carga de documento</h2>
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="bg-white border rounded-xl p-5 grid sm:grid-cols-2 gap-4">
        @csrf
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold">Expediente</label>
            <select name="proceeding_id" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                @foreach($proceedings as $proceeding)
                    <option value="{{ $proceeding->id }}" @selected($preselected == $proceeding->id)>{{ $proceeding->file_number }} — {{ $proceeding->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Tipo</label>
            <input name="document_type" value="Acta" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
        </div>
        <div>
            <label class="text-xs font-semibold">Soporte</label>
            <select name="support" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                <option>Electrónico</option>
                <option>Físico</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold">Nombre</label>
            <input name="name" required class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
        </div>
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold">Descripción</label>
            <textarea name="description" rows="3" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg"></textarea>
        </div>
        <div>
            <label class="text-xs font-semibold">Fecha del documento</label>
            <input type="date" name="document_creation_date" value="{{ now()->toDateString() }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
        </div>
        <div>
            <label class="text-xs font-semibold">Estado</label>
            <select name="state" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                <option>Abierto</option>
                <option selected>Cerrado</option>
                <option>Privado</option>
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="text-xs font-semibold">Archivo (máx. 25 MB)</label>
            <input type="file" name="file" class="mt-1 text-xs">
        </div>
        <div class="sm:col-span-2">
            <button class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg">Indexar documento</button>
        </div>
    </form>
</div>
</x-layouts.app>
