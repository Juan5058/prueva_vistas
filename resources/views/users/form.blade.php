<x-layouts.app>
@php($isEdit = $managedUser->exists)
<div class="p-6 max-w-xl mx-auto space-y-6">
    <h2 class="text-xl font-bold">{{ $isEdit ? 'Editar usuario' : 'Alta de usuario' }}</h2>
    <form method="POST" action="{{ $isEdit ? route('users.update', $managedUser) : route('users.store') }}" class="bg-white border rounded-xl p-5 space-y-4">
        @csrf
        @if($isEdit) @method('PUT') @endif
        <div>
            <label class="text-xs font-semibold">Nombre</label>
            <input name="name" required value="{{ old('name', $managedUser->name) }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
        </div>
        <div>
            <label class="text-xs font-semibold">Correo</label>
            <input type="email" name="email" required value="{{ old('email', $managedUser->email) }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
        </div>
        <div>
            <label class="text-xs font-semibold">Contraseña {{ $isEdit ? '(dejar vacío para no cambiar)' : '' }}</label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }} class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
        </div>
        <div>
            <label class="text-xs font-semibold">Rol</label>
            <select name="role" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg">
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected(old('role', $managedUser->role) === $role)>{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold">Rango IP permitido</label>
            <input name="allowed_ip_range" value="{{ old('allowed_ip_range', $managedUser->allowed_ip_range ?: '*') }}" class="mt-1 w-full px-3 py-2 text-xs border rounded-lg font-mono" placeholder="* o 192.168.*">
        </div>
        <button class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 rounded-lg">Guardar</button>
    </form>
</div>
</x-layouts.app>
