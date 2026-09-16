<x-layouts.app>
<div class="p-6 max-w-3xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">{{ $document->name }}</h2>
    <div class="bg-white border rounded-xl p-5 text-xs space-y-2">
        <div class="flex justify-between"><span class="text-slate-500">Tipo</span><span>{{ $document->document_type }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Expediente</span><span>{{ $document->proceeding?->file_number }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Soporte</span><span>{{ $document->support }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">Estado</span><span>{{ $document->state }}</span></div>
        <div class="flex justify-between"><span class="text-slate-500">SHA-256</span><span class="font-mono">{{ data_get($document->file_metadata, 'hash_sha256', '—') }}</span></div>
        <p class="pt-2 text-slate-600">{{ $document->description }}</p>
        @if($document->file_path)
            <a href="{{ route('documents.download', $document) }}" class="inline-block mt-3 px-3 py-1.5 bg-blue-600 text-white rounded-lg">Descargar archivo</a>
        @endif
    </div>
</div>
</x-layouts.app>
