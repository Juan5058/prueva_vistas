<div class="p-6 max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Capa 1 de 4 · Control TRD</p>
            <h2 class="text-xl font-bold">Tablas de Retención Documental</h2>
            <p class="text-xs text-slate-500">Versión, fechas y activación. Clic en la sección para bajar a la estructura orgánica.</p>
        </div>
        <div class="flex gap-2">
            @if(auth()->user()->hasPermission('trd.import'))
                <a href="{{ route('trd.import') }}" wire:navigate class="px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">Importar CSV</a>
            @endif
            @if(auth()->user()->hasPermission('trd.create'))
                <a href="{{ route('trd.create') }}" wire:navigate class="px-3 py-1.5 text-xs text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Nueva TRD</a>
            @endif
        </div>
    </div>
    <input type="search" wire:model.live.debounce.400ms="q" placeholder="Buscar sección o versión" class="px-3 py-2 text-xs border rounded-lg w-72">
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="text-left p-3">Sección</th>
                    <th class="text-left p-3">Versión</th>
                    <th class="text-left p-3">Creación</th>
                    <th class="text-left p-3">Aprobación</th>
                    <th class="text-center p-3">Activa</th>
                    <th class="p-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
            @forelse($structures as $structure)
                @php $key = (string) $structure->getKey(); @endphp
                <tr class="border-t border-slate-100 {{ $structure->is_active ? '' : 'bg-slate-50/80' }}">
                    <td class="p-3">
                        <a href="{{ route('trd.show', $key) }}" wire:navigate class="font-medium text-slate-900 hover:underline">{{ $structure->section_name }}</a>
                        <div class="font-mono text-[11px] text-slate-500 mt-0.5">{{ $structure->section_code }} · {{ count($structure->series ?? []) }} series</div>
                    </td>
                    <td class="p-3">
                        @if(auth()->user()->hasPermission('trd.edit'))
                            <input wire:model="versions.{{ $key }}" class="w-36 px-2 py-1 border rounded-md">
                        @else
                            {{ $structure->version }}
                        @endif
                    </td>
                    <td class="p-3 whitespace-nowrap">{{ \App\Support\FormatsDate::datetime($structure->created_at, 'd/m/Y') }}</td>
                    <td class="p-3">
                        @if(auth()->user()->hasPermission('trd.edit'))
                            <input type="date" wire:model="approved.{{ $key }}" class="px-2 py-1 border rounded-md">
                        @else
                            {{ \App\Support\FormatsDate::datetime($structure->approved_at, 'd/m/Y') }}
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        @if($structure->is_active)
                            @if(auth()->user()->hasPermission('trd.edit'))
                                <input type="checkbox" checked
                                    wire:click="toggleActive('{{ $key }}')"
                                    wire:confirm="¿Inhabilitar esta TRD? Solo un superusuario podrá reactivarla."
                                    class="rounded border-slate-300"
                                    title="Inhabilitar">
                            @else
                                <span class="text-emerald-700 font-semibold">Sí</span>
                            @endif
                        @elseif(auth()->user()->isSuperAdmin())
                            <input type="checkbox"
                                wire:click="toggleActive('{{ $key }}')"
                                class="rounded border-slate-300"
                                title="Reactivar (solo superusuario)">
                        @else
                            <span class="text-slate-500">No · solo superusuario</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex justify-end gap-1">
                            @if(auth()->user()->hasPermission('trd.edit'))
                                <x-icon-action tooltip="Guardar parametrización" variant="info" wire:click="saveControl('{{ $key }}')">
                                    <x-icon name="check" class="w-4 h-4" />
                                </x-icon-action>
                                <x-icon-action href="{{ route('trd.edit', $key) }}" tooltip="Editar estructura" variant="neutral">
                                    <x-icon name="pencil" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                            <x-icon-action href="{{ route('trd.show', $key) }}" tooltip="Capa 2 · Órgano" variant="info">
                                <x-icon name="eye" class="w-4 h-4" />
                            </x-icon-action>
                            @if(auth()->user()->hasPermission('trd.delete'))
                                <x-icon-action tooltip="Dar de baja (is_deleted)" variant="danger" wire:click="inhabilitar('{{ $key }}')" wire:confirm="¿Dar de baja esta TRD con is_deleted: true?">
                                    <x-icon name="trash" class="w-4 h-4" />
                                </x-icon-action>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-500">No hay estructuras TRD.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
