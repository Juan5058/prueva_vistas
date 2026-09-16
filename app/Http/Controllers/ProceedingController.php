<?php

namespace App\Http\Controllers;

use App\Models\Proceeding;
use App\Models\TrdStructure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProceedingController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('q', ''));
        $proceedings = Proceeding::query()
            ->with('trdStructure')
            ->withCount('documents')
            ->when($search !== '', function ($query) use ($search) {
                \App\Support\SearchQuery::apply($query, $search, ['file_number', 'name', 'serie_name']);
            })
            ->latest()
            ->get();

        return view('proceedings.index', compact('proceedings', 'search'));
    }

    public function create(): View
    {
        return view('proceedings.form', [
            'proceeding' => new Proceeding([
                'file_number' => 'EXP-'.now()->year.'-'.random_int(1000, 9999),
                'opening_date' => now()->toDateString(),
                'state' => 'Público',
                'physical_location' => [
                    'deposit' => 'Depósito Central 01',
                    'shelf' => 'E-04',
                    'module' => 'M-2',
                    'box' => 'C-12',
                    'folder' => '01',
                ],
                'sub_proceedings' => [],
            ]),
            'structures' => TrdStructure::query()->orderBy('section_code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $proceeding = Proceeding::query()->create($this->payload($request));

        return redirect()->route('proceedings.show', $proceeding)->with('status', 'Expediente radicado correctamente.');
    }

    public function show(Proceeding $proceeding): View
    {
        $proceeding->load('documents', 'trdStructure');

        return view('proceedings.show', compact('proceeding'));
    }

    public function edit(Proceeding $proceeding): View
    {
        return view('proceedings.form', [
            'proceeding' => $proceeding,
            'structures' => TrdStructure::query()->orderBy('section_code')->get(),
        ]);
    }

    public function update(Request $request, Proceeding $proceeding): RedirectResponse
    {
        $proceeding->update($this->payload($request, $proceeding->id));

        return redirect()->route('proceedings.show', $proceeding)->with('status', 'Expediente actualizado.');
    }

    public function destroy(Proceeding $proceeding): RedirectResponse
    {
        $proceeding->documents()->each(fn ($document) => $document->delete());
        $proceeding->delete();

        return redirect()->route('proceedings.index')->with('status', 'Expediente inhabilitado mediante soft delete.');
    }

    public function addSubProceeding(Request $request, Proceeding $proceeding): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $subs = $proceeding->sub_proceedings ?? [];
        $subs[] = [
            'sub_proceeding_id' => (string) Str::uuid(),
            'code' => $data['code'],
            'name' => $data['name'],
            'creation_date' => now()->toIso8601String(),
        ];
        $proceeding->update(['sub_proceedings' => $subs]);

        return back()->with('status', 'Subexpediente agregado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'trd_structure_id' => ['required', 'exists:trd_structures,id'],
            'serie_id' => ['required', 'string'],
            'sub_serie_id' => ['required', 'string'],
            'file_number' => ['required', 'string', 'max:80', 'unique:proceedings,file_number'.($ignoreId ? ','.$ignoreId : '')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'opening_date' => ['required', 'date'],
            'deadline' => ['nullable', 'date'],
            'state' => ['required', 'in:Público,Privado,Reservado'],
            'physical_location.deposit' => ['nullable', 'string'],
            'physical_location.shelf' => ['nullable', 'string'],
            'physical_location.module' => ['nullable', 'string'],
            'physical_location.box' => ['nullable', 'string'],
            'physical_location.folder' => ['nullable', 'string'],
        ]);

        $trd = TrdStructure::query()->findOrFail($data['trd_structure_id']);
        $serie = $trd->findSerie($data['serie_id']);
        $subSerie = $trd->findSubSerie($data['sub_serie_id']);

        $subs = collect($request->input('sub_proceedings', []))
            ->filter(fn ($row) => filled($row['code'] ?? null) && filled($row['name'] ?? null))
            ->map(fn ($row) => [
                'sub_proceeding_id' => $row['sub_proceeding_id'] ?? (string) Str::uuid(),
                'code' => $row['code'],
                'name' => $row['name'],
                'creation_date' => $row['creation_date'] ?? now()->toIso8601String(),
            ])
            ->values()
            ->all();

        return [
            ...$data,
            'section_code' => $trd->section_code,
            'serie_name' => $serie['serie_name'] ?? null,
            'sub_serie_name' => $subSerie['sub_serie_name'] ?? null,
            'physical_location' => $request->input('physical_location', []),
            'sub_proceedings' => $subs,
        ];
    }
}
