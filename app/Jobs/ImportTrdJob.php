<?php

namespace App\Jobs;

use App\Models\TrdImport;
use App\Models\TrdStructure;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class ImportTrdJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 120;

    /**
     * @var list<string>
     */
    public const REQUIRED_HEADERS = [
        'codigo_seccion',
        'nombre_seccion',
        'codigo_subseccion',
        'nombre_subseccion',
        'codigo_serie',
        'nombre_serie',
        'codigo_subserie',
        'nombre_subserie',
        'retencion_gestion',
        'retencion_central',
        'disposicion_final',
    ];

    public function __construct(public string $importId, public string $content)
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $import = TrdImport::query()->find($this->importId);
        if (! $import) {
            return;
        }

        $import->update(['status' => 'PROCESSING']);

        $lines = preg_split('/\r\n|\r|\n/', $this->content) ?: [];
        $lines = array_values(array_filter($lines, fn ($line) => trim($line) !== ''));

        if ($lines === []) {
            $import->update(['status' => 'FAILED', 'error_log' => ['El archivo está vacío.']]);

            return;
        }

        $delimiter = str_contains($lines[0], ';') ? ';' : (str_contains($lines[0], "\t") ? "\t" : ',');
        $header = array_map(fn ($col) => $this->normalizeHeader($col), explode($delimiter, $lines[0]));
        $missing = array_diff(self::REQUIRED_HEADERS, $header);

        if ($missing !== []) {
            $import->update([
                'status' => 'FAILED',
                'error_log' => ['Encabezados faltantes: '.implode(', ', $missing)],
            ]);

            return;
        }

        $map = array_flip($header);
        array_shift($lines);
        $import->update(['total_rows' => count($lines)]);

        $errors = [];
        $processed = 0;

        foreach ($lines as $index => $line) {
            $cols = array_map(fn ($col) => trim(preg_replace('/^["\']|["\']$/', '', $col) ?? ''), explode($delimiter, $line));
            $get = fn (string $key) => $cols[$map[$key]] ?? '';

            $sectionCode = $get('codigo_seccion');
            $sectionName = $get('nombre_seccion');
            $serieCode = $get('codigo_serie');
            $serieName = $get('nombre_serie');

            if ($sectionCode === '' || $serieCode === '') {
                $errors[] = 'Fila '.($index + 2).': codigo_seccion y codigo_serie son obligatorios.';
                continue;
            }

            $disposition = strtoupper($get('disposicion_final') ?: 'CT');
            if (! in_array($disposition, ['CT', 'E', 'M', 'S'], true)) {
                $errors[] = 'Fila '.($index + 2).': disposicion_final debe ser CT, E, M o S.';
                continue;
            }

            $structure = TrdStructure::query()->where('section_code', $sectionCode)->first();
            if (! $structure) {
                $structure = TrdStructure::query()->create([
                    'section_code' => $sectionCode,
                    'section_name' => $sectionName,
                    'version' => 'TRD-IMPORT-'.now()->year,
                    'is_active' => true,
                    'is_deleted' => false,
                    'sub_sections' => [],
                    'series' => [],
                    'sub_series' => [],
                ]);
            }

            $nested = [
                'sub_sections' => $structure->sub_sections ?? [],
                'series' => $structure->series ?? [],
                'sub_series' => $structure->sub_series ?? [],
            ];

            $subSectionCode = $get('codigo_subseccion');
            if ($subSectionCode !== '' && ! collect($nested['sub_sections'])->contains('sub_section_code', $subSectionCode)) {
                $nested['sub_sections'][] = [
                    'sub_section_id' => (string) Str::uuid(),
                    'sub_section_code' => $subSectionCode,
                    'sub_section_name' => $get('nombre_subseccion'),
                ];
            }

            if (! collect($nested['series'])->contains('serie_code', $serieCode)) {
                $nested['series'][] = [
                    'serie_id' => (string) Str::uuid(),
                    'serie_code' => $serieCode,
                    'serie_name' => $serieName,
                    'retencion_gestion' => (int) $get('retencion_gestion'),
                    'retencion_central' => (int) $get('retencion_central'),
                    'disposicion_final' => $disposition,
                ];
            }

            $subSerieCode = $get('codigo_subserie');
            if ($subSerieCode !== '' && ! collect($nested['sub_series'])->contains('sub_serie_code', $subSerieCode)) {
                $nested['sub_series'][] = [
                    'sub_serie_id' => (string) Str::uuid(),
                    'sub_serie_code' => $subSerieCode,
                    'sub_serie_name' => $get('nombre_subserie'),
                ];
            }

            $structure->fill($nested);
            $structure->save();
            $processed++;
        }

        $import->update([
            'status' => $errors !== [] && $processed === 0 ? 'FAILED' : 'COMPLETED',
            'processed_rows' => $processed,
            'error_log' => $errors,
        ]);
    }

    private function normalizeHeader(string $value): string
    {
        $value = strtolower(trim(preg_replace('/^["\']|["\']$/', '', $value) ?? ''));
        $value = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $value);

        return $value;
    }
}
