<div class="p-6 max-w-3xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">{{ $userId ? 'Editar usuario' : 'Nuevo usuario' }}</h2>
    <form wire:submit="save" class="bg-white border rounded-xl p-5 space-y-4">
        <div>
            <label class="text-xs font-semibold">Nombre</label>
            <input wire:model="name" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            @error('name') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="text-xs font-semibold">Correo</label>
            <input type="email" wire:model="email" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            @error('email') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="text-xs font-semibold">Rol</label>
            <select wire:model="role" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                <option value="super_admin">Super Admin</option>
                <option value="lider_ambiental">Líder Ambiental</option>
                <option value="aprendiz">Aprendiz</option>
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Rango IP permitido</label>
            <input wire:model="allowed_ip_range" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg font-mono" placeholder="* o 192.168.1.*">
            @error('allowed_ip_range') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="text-xs font-semibold">Contraseña {{ $userId ? '(dejar vacío para no cambiar)' : '' }}</label>
            <input type="password" wire:model="password" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
            @error('password') <p class="text-rose-600 text-xs">{{ $message }}</p> @enderror
        </div>
        <button class="px-4 py-2 text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 rounded-lg">Guardar usuario</button>
    </form>
</div>
