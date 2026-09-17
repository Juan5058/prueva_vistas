<div class="p-6 max-w-7xl mx-auto space-y-6" x-data="{
    selectedReports: ['totales'],
    startDate: '',
    endDate: '',
    errorMessage: '',

    toggleReport(id) {
        if (this.selectedReports.includes(id)) {
            this.selectedReports = this.selectedReports.filter(r => r !== id);
        } else {
            this.selectedReports.push(id);
        }
        this.validate();
    },

    isSelected(id) {
        return this.selectedReports.includes(id);
    },

    setThisMonth() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');

        this.startDate = `${year}-${month}-01`;
        this.endDate = `${year}-${month}-${day}`;
        this.validate();
    },

    validate() {
        this.errorMessage = '';
        if (this.selectedReports.length === 0) {
            return false;
        }
        if (this.startDate && this.endDate && this.startDate > this.endDate) {
            this.errorMessage = 'La fecha de inicio no puede ser posterior a la fecha de fin.';
            return false;
        }
        return true;
    },

    submitForm(e) {
        if (!this.validate() || this.selectedReports.length === 0) {
            e.preventDefault();
            return false;
        }
    }
}">

    {{-- ═══════════════════════════════════════════════════════════
         1. BANNER SUPERIOR
    ════════════════════════════════════════════════════════════ --}}
    <div class="relative overflow-hidden bg-slate-900 border border-slate-800 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start md:items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-xl bg-blue-600/20 text-blue-400 border border-blue-500/30 flex items-center justify-center shrink-0 shadow-inner">
                <x-icon name="file" class="w-6 h-6" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white tracking-tight">Generador de Reportes</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 leading-relaxed">
                    Selecciona los reportes que deseas generar y configura el período de consulta.
                </p>
            </div>
        </div>
        <div class="hidden lg:flex items-center gap-2 bg-slate-800/80 border border-slate-700/60 px-3.5 py-1.5 rounded-full text-xs text-slate-300 font-mono">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Módulo de Exportación PDF</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         2. FORMULARIO PRINCIPAL DE GENERACIÓN DE REPORTES
    ════════════════════════════════════════════════════════════ --}}
    <form action="{{ route('reports.download-pdf') }}" method="GET" @submit="submitForm($event)" class="space-y-6">

        {{-- Hidden inputs to send selected reports and date range to backend --}}
        <template x-for="reportId in selectedReports" :key="reportId">
            <input type="hidden" name="reports[]" :value="reportId">
        </template>
        <input type="hidden" name="start_date" :value="startDate">
        <input type="hidden" name="end_date" :value="endDate">

        {{-- ═══════════════════════════════════════════════════════════
             3. SELECCIÓN DE REPORTES & CONTADOR DINÁMICO
        ════════════════════════════════════════════════════════════ --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-2 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Selecciona los reportes</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Elige uno o varios reportes para incluir en el archivo PDF.</p>
                </div>
                {{-- Dynamic counter badge --}}
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all duration-200"
                          :class="selectedReports.length > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200 shadow-sm' : 'bg-slate-100 text-slate-500 border border-slate-200'">
                        <span class="w-2 h-2 rounded-full" :class="selectedReports.length > 0 ? 'bg-blue-600' : 'bg-slate-400'"></span>
                        <span x-text="selectedReports.length === 0 ? '0 reportes seleccionados' : (selectedReports.length === 1 ? '1 reporte seleccionado' : selectedReports.length + ' reportes seleccionados')"></span>
                    </span>
                </div>
            </div>

            {{-- Responsive Grid: 1 col (mobile), 2 cols (tablet), 3 cols (desktop) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                {{-- Tarjeta 1: Reportes Totales --}}
                <div @click="toggleReport('totales')"
                     class="p-5 rounded-xl border relative text-left transition-all duration-200 cursor-pointer select-none group"
                     :class="isSelected('totales') ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600/30 shadow-md' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-sm'">
                    {{-- Circular indicator --}}
                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full border flex items-center justify-center transition-all duration-200"
                         :class="isSelected('totales') ? 'bg-blue-600 border-blue-600 text-white scale-110 shadow-sm' : 'border-slate-300 bg-white text-transparent group-hover:border-slate-400'">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200"
                         :class="isSelected('totales') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200'">
                        <x-icon name="chart" class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1">Reportes Totales</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Todos los registros principales consolidados.</p>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Consolidado general</span>
                        <span class="font-mono font-semibold text-slate-700">Resumen global</span>
                    </div>
                </div>

                {{-- Tarjeta 2: Estructuras TRD --}}
                <div @click="toggleReport('trd')"
                     class="p-5 rounded-xl border relative text-left transition-all duration-200 cursor-pointer select-none group"
                     :class="isSelected('trd') ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600/30 shadow-md' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-sm'">
                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full border flex items-center justify-center transition-all duration-200"
                         :class="isSelected('trd') ? 'bg-blue-600 border-blue-600 text-white scale-110 shadow-sm' : 'border-slate-300 bg-white text-transparent group-hover:border-slate-400'">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200"
                         :class="isSelected('trd') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200'">
                        <x-icon name="book" class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1">Estructuras TRD</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Información relacionada con las estructuras TRD.</p>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Secciones y Series</span>
                        <span class="font-mono font-semibold text-slate-700">{{ $kpis['totalTrdSections'] ?? 0 }} secciones</span>
                    </div>
                </div>

                {{-- Tarjeta 3: Expedientes --}}
                <div @click="toggleReport('expedientes')"
                     class="p-5 rounded-xl border relative text-left transition-all duration-200 cursor-pointer select-none group"
                     :class="isSelected('expedientes') ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600/30 shadow-md' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-sm'">
                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full border flex items-center justify-center transition-all duration-200"
                         :class="isSelected('expedientes') ? 'bg-blue-600 border-blue-600 text-white scale-110 shadow-sm' : 'border-slate-300 bg-white text-transparent group-hover:border-slate-400'">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200"
                         :class="isSelected('expedientes') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200'">
                        <x-icon name="folder" class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1">Expedientes</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Listado y resumen de los expedientes registrados.</p>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Estado de acervo</span>
                        <span class="font-mono font-semibold text-slate-700">{{ $kpis['totalProceedings'] ?? 0 }} expedientes</span>
                    </div>
                </div>

                {{-- Tarjeta 4: Documentos --}}
                <div @click="toggleReport('documentos')"
                     class="p-5 rounded-xl border relative text-left transition-all duration-200 cursor-pointer select-none group"
                     :class="isSelected('documentos') ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600/30 shadow-md' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-sm'">
                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full border flex items-center justify-center transition-all duration-200"
                         :class="isSelected('documentos') ? 'bg-blue-600 border-blue-600 text-white scale-110 shadow-sm' : 'border-slate-300 bg-white text-transparent group-hover:border-slate-400'">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200"
                         :class="isSelected('documentos') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200'">
                        <x-icon name="file" class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1">Documentos</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Información de los documentos registrados.</p>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Físicos y Electrónicos</span>
                        <span class="font-mono font-semibold text-slate-700">{{ $kpis['totalDocuments'] ?? 0 }} documentos</span>
                    </div>
                </div>

                {{-- Tarjeta 5: Usuarios Activos --}}
                <div @click="toggleReport('usuarios')"
                     class="p-5 rounded-xl border relative text-left transition-all duration-200 cursor-pointer select-none group"
                     :class="isSelected('usuarios') ? 'bg-blue-50/50 border-blue-600 ring-1 ring-blue-600/30 shadow-md' : 'bg-white border-slate-200 hover:border-slate-300 hover:shadow-sm'">
                    <div class="absolute top-4 right-4 w-6 h-6 rounded-full border flex items-center justify-center transition-all duration-200"
                         :class="isSelected('usuarios') ? 'bg-blue-600 border-blue-600 text-white scale-110 shadow-sm' : 'border-slate-300 bg-white text-transparent group-hover:border-slate-400'">
                        <x-icon name="check" class="w-3.5 h-3.5" />
                    </div>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 transition-colors duration-200"
                         :class="isSelected('usuarios') ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-slate-200'">
                        <x-icon name="users" class="w-5 h-5" />
                    </div>
                    <h4 class="font-bold text-sm text-slate-900 mb-1">Usuarios Activos</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">Listado de usuarios activos registrados.</p>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>Cuentas activas RBAC</span>
                        <span class="font-mono font-semibold text-slate-700">{{ $kpis['totalActiveUsers'] ?? 0 }} usuarios</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             4. CONFIGURACIÓN DE FECHAS Y PERÍODO
        ════════════════════════════════════════════════════════════ --}}
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <x-icon name="shield" class="w-4 h-4 text-blue-600" />
                    Configuración de período
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Define el rango de fechas para el filtrado de la información en el reporte.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end pt-2">
                {{-- Fecha inicio --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Fecha inicio</label>
                    <input type="date"
                           x-model="startDate"
                           @change="validate()"
                           class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                </div>

                {{-- Fecha fin --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700">Fecha fin</label>
                    <input type="date"
                           x-model="endDate"
                           @change="validate()"
                           class="w-full px-3.5 py-2 text-xs font-medium border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-slate-800 transition-all">
                </div>

                {{-- Botón Este mes --}}
                <div>
                    <button type="button"
                            @click="setThisMonth()"
                            class="w-full py-2 px-4 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.99]">
                        <x-icon name="activity" class="w-3.5 h-3.5 text-blue-600" />
                        <span>Este mes</span>
                    </button>
                </div>
            </div>

            {{-- Error validation message --}}
            <div x-show="errorMessage" x-cloak class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2 animate-fadeIn">
                <x-icon name="alert" class="w-4 h-4 shrink-0" />
                <span x-text="errorMessage"></span>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             5. BOTÓN PRINCIPAL DE GENERACIÓN DEL PDF & ESTADO
        ════════════════════════════════════════════════════════════ --}}
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-200">
            <div class="text-xs text-slate-500">
                <span x-show="selectedReports.length === 0" class="text-amber-600 font-medium">⚠️ Selecciona al menos un reporte para habilitar la descarga.</span>
                <span x-show="selectedReports.length > 0 && !errorMessage" class="text-slate-600">
                    Se exportará el PDF consolidado con los tipos de reporte seleccionados.
                </span>
            </div>

            @if(auth()->user()->hasPermission('reports.download-pdf'))
                <button type="submit"
                        :disabled="selectedReports.length === 0 || errorMessage !== ''"
                        :class="selectedReports.length === 0 || errorMessage !== ''
                            ? 'opacity-50 cursor-not-allowed bg-slate-400 text-white'
                            : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/20 cursor-pointer active:scale-[0.99]'"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-xs transition-all duration-150">
                    <x-icon name="download" class="w-4 h-4" />
                    <span>Generar PDF</span>
                </button>
            @else
                <button type="button" disabled class="opacity-50 cursor-not-allowed bg-slate-400 text-white w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl font-bold text-xs">
                    <x-icon name="lock" class="w-4 h-4" />
                    <span>Sin permiso para descargar PDF</span>
                </button>
            @endif
        </div>
    </form>
</div>

