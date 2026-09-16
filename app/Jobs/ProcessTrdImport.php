<?php

namespace App\Jobs;

use App\Models\TrdImport;
use App\Models\TrdStructure;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class ProcessTrdImport implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $importId, public string $content) {}

    public function handle(): void
    {
        $import = TrdImport::query()->find($this->importId);
        if (! $import) {
            return;
        }

        $import->update(['status' => 'PROCESSING']);

        $lines = preg_split('/\r\n|\r|\n/', $this->content) ?: [];
        $lines = array_values(array_filter($lines, fn ($line) => trim($line) !== ''));

        if ($lines !== [] && (str_contains(mb_strtolower($lines[0]), 'seccion') || str_contains(mb_strtolower($lines[0]), 'sección'))) {
            array_shift($lines);
        }

        $import->update(['total_rows' => count($lines)]);

        $errors = [];
        $processed = 0;

        foreach ($lines as $index => $line) {
            $delimiter = str_contains($line, ';') ? ';' : (str_contains($line, "\t") ? "\t" : ',');
            $cols = array_map(fn ($col) => trim(preg_replace('/^["\']|["\']$/', '', $col) ?? ''), explode($delimiter, $line));

            if (count($cols) < 6) {
                $errors[] = 'Fila '.($index + 1).': columnas insuficientes.';
                continue;
            }

            $sectionCode = $cols[0];
            $sectionName = $cols[1];
            $subSectionCode = $cols[2] ?? '';
            $subSectionName = $cols[3] ?? '';
            $serieCode = $cols[4];
            $serieName = $cols[5];
            $subSerieCode = $cols[6] ?? $serieCode;
            $subSerieName = $cols[7] ?? $serieName;
            $retGestion = (int) ($cols[8] ?? 3);
            $retCentral = (int) ($cols[9] ?? 10);
            $disposition = $cols[10] ?? 'Conservación Total';
            $procedure = $cols[11] ?? '';

            $structure = TrdStructure::query()->where('section_code', $sectionCode)->first();
            if (! $structure) {
                $structure = TrdStructure::query()->create([
                    'section_code' => $sectionCode,
                    'section_name' => $sectionName,
                    'version' => 'TRD-IMPORT-'.now()->year,
                    'is_active' => true,
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

            if ($subSectionCode !== '') {
                $exists = collect($nested['sub_sections'])->contains('sub_section_code', $subSectionCode);
                if (! $exists) {
                    $nested['sub_sections'][] = [
                        'sub_section_id' => (string) Str::uuid(),
                        'sub_section_code' => $subSectionCode,
                        'sub_section_name' => $subSectionName,
                    ];
                }
            }

            $serieExists = collect($nested['series'])->contains('serie_code', $serieCode);
            if (! $serieExists) {
                $nested['series'][] = [
                    'serie_id' => (string) Str::uuid(),
                    'serie_code' => $serieCode,
                    'serie_name' => $serieName,
                ];
            }

            $subExists = collect($nested['sub_series'])->contains('sub_serie_code', $subSerieCode);
            if (! $subExists) {
                $nested['sub_series'][] = [
                    'sub_serie_id' => (string) Str::uuid(),
                    'sub_serie_code' => $subSerieCode,
                    'sub_serie_name' => $subSerieName,
                    'retention_management_years' => $retGestion,
                    'retention_central_years' => $retCentral,
                    'final_disposition' => $disposition,
                    'procedure' => $procedure,
                ];
            }

            $structure->update($nested);
            $processed++;
        }

        $import->update([
            'status' => $errors !== [] && $processed === 0 ? 'FAILED' : 'COMPLETED',
            'processed_rows' => $processed,
            'error_log' => $errors,
        ]);
    }
}
