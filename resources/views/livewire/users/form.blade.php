<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Banner de Encabezado --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex items-center justify-between gap-6">
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="users" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $userId ? 'Editar Usuario' : 'Nuevo Usuario' }}</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">Configuración de credenciales, roles RBAC y políticas de seguridad IP.</p>
            </div>
        </div>
    </div>

    {{-- Formulario Principal --}}
    <form wire:submit="save" class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-6">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            Información del perfil de usuario
        </h3>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nombre completo</label>
                <input wire:model="name" placeholder="Ej. Carlos Mendoza" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                @error('name') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Correo electrónico</label>
                <input type="email" wire:model="email" placeholder="usuario@trd.gob" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                @error('email') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rol del sistema (RBAC)</label>
                <select wire:model="role" class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                    <option value="super_admin">Super Admin (Acceso Total)</option>
                    <option value="lider_ambiental">Líder Ambiental (Gestión TRD y Documentos)</option>
                    <option value="aprendiz">Aprendiz (Consulta y Bitácoras)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rango IP permitido</label>
                <input wire:model="allowed_ip_range" class="w-full px-3.5 py-2.5 text-xs font-mono font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all" placeholder="* o 192.168.1.*">
                @error('allowed_ip_range') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-2 border-t border-slate-100">
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Contraseña {{ $userId ? '(dejar vacío para mantener la actual)' : '' }}</label>
            <input type="password" wire:model="password" class="w-full sm:w-1/2 px-3.5 py-2.5 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
            @error('password') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2 flex justify-end">
            <button class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-xs bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 active:scale-[0.99] transition-all cursor-pointer">
                <x-icon name="check" class="w-4 h-4" />
                <span>Guardar usuario</span>
            </button>
        </div>
    </form>
</div>

