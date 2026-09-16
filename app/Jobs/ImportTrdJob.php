<?php

namespace App\Jobs;

use App\Models\TrdImport;
use App\Models\TrdStructure;
use App\Support\TabularFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

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

    public function __construct(
        public string $importId,
        public string $content,
        public string $originalName = 'import.csv',
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $import = TrdImport::query()->find($this->importId);
        if (! $import) {
            return;
        }

        $import->update(['status' => 'PROCESSING']);

        try {
            $rows = TabularFile::rows($this->content, $this->originalName);
        } catch (RuntimeException $exception) {
            $import->update(['status' => 'FAILED', 'error_log' => [$exception->getMessage()]]);

            return;
        } catch (Throwable $exception) {
            $import->update(['status' => 'FAILED', 'error_log' => ['No fue posible leer el archivo plano.']]);

            return;
        }

        if ($rows === []) {
            $import->update(['status' => 'FAILED', 'error_log' => ['El archivo está vacío.']]);

            return;
        }

        $header = array_map(fn ($col) => $this->normalizeHeader((string) $col), $rows[0]);
        $missing = array_diff(self::REQUIRED_HEADERS, $header);

        if ($missing !== []) {
            $import->update([
                'status' => 'FAILED',
                'error_log' => ['Encabezados faltantes: '.implode(', ', $missing)],
            ]);

            return;
        }

        $map = array_flip($header);
        $dataRows = array_slice($rows, 1);
        $import->update(['total_rows' => count($dataRows)]);

        $errors = [];
        $processed = 0;

        foreach ($dataRows as $index => $cols) {
            $get = fn (string $key) => trim((string) ($cols[$map[$key]] ?? ''));

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
