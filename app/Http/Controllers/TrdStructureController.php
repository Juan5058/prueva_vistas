<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTrdImport;
use App\Models\TrdImport;
use App\Models\TrdStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrdStructureController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('q', ''));
        $structures = TrdStructure::query()
            ->when($search !== '', function ($query) use ($search) {
                \App\Support\SearchQuery::apply($query, $search, ['section_code', 'section_name', 'version']);
            })
            ->latest()
            ->get();

        return view('trd.index', compact('structures', 'search'));
    }

    public function create(): View
    {
        return view('trd.form', ['structure' => new TrdStructure([
            'version' => 'TRD-V1-'.now()->year,
            'sub_sections' => [['sub_section_code' => '', 'sub_section_name' => '']],
            'series' => [['serie_code' => '', 'serie_name' => '']],
            'sub_series' => [[
                'sub_serie_code' => '',
                'sub_serie_name' => '',
                'retention_management_years' => 3,
                'retention_central_years' => 15,
                'final_disposition' => 'Conservación Total',
                'procedure' => '',
            ]],
        ])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $nested = TrdStructure::normalizeNested($request->all());

        TrdStructure::query()->create([
            ...$data,
            ...$nested,
            'is_active' => true,
        ]);

        return redirect()->route('trd.index')->with('status', 'Estructura TRD creada exitosamente.');
    }

    public function show(TrdStructure $trd): View
    {
        return view('trd.show', ['structure' => $trd]);
    }

    public function edit(TrdStructure $trd): View
    {
        return view('trd.form', ['structure' => $trd]);
    }

    public function update(Request $request, TrdStructure $trd): RedirectResponse
    {
        $data = $this->validated($request, $trd->id);
        $nested = TrdStructure::normalizeNested($request->all());
        $trd->update([...$data, ...$nested]);

        return redirect()->route('trd.show', $trd)->with('status', 'Estructura TRD actualizada correctamente.');
    }

    public function destroy(TrdStructure $trd): RedirectResponse
    {
        $trd->delete();

        return redirect()->route('trd.index')->with('status', 'Estructura TRD inhabilitada mediante soft delete.');
    }

    public function importForm(): View
    {
        $imports = TrdImport::query()->latest()->limit(20)->get();

        return view('trd.import', compact('imports'));
    }

    public function importStore(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:25600', 'mimes:csv,txt,text'],
        ]);

        $file = $request->file('file');
        $content = $file->get();

        $import = TrdImport::query()->create([
            'user_id' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'PENDING',
            'total_rows' => 0,
            'processed_rows' => 0,
            'error_log' => [],
        ]);

        ProcessTrdImport::dispatch($import->id, $content);

        return redirect()->route('trd.import')->with('status', 'Archivo recibido y enviado a la cola de procesamiento TRD.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'section_code' => ['required', 'string', 'max:50'],
            'section_name' => ['required', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:80'],
        ]);
    }
}
