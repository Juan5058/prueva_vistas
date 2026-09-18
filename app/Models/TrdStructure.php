<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use App\Models\Concerns\LogicalSoftDeletes;
use Illuminate\Support\Str;
use MongoDB\Laravel\Eloquent\Model;

class TrdStructure extends Model
{
    use HasPublicKey, LogicalSoftDeletes;

    protected $collection = 'trd_structures';

    protected $fillable = [
        'section_code',
        'section_name',
        'sub_sections',
        'series',
        'sub_series',
        'version',
        'approved_at',
        'is_active',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'sub_sections' => 'array',
            'series' => 'array',
            'sub_series' => 'array',
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function proceedings()
    {
        return $this->hasMany(Proceeding::class);
    }

    public function findSerie(string $serieId): ?array
    {
        foreach ($this->series ?? [] as $serie) {
            if (($serie['serie_id'] ?? '') === $serieId) {
                return $serie;
            }
        }

        return null;
    }

    public function findSubSerie(string $subSerieId): ?array
    {
        foreach ($this->sub_series ?? [] as $subSerie) {
            if (($subSerie['sub_serie_id'] ?? '') === $subSerieId) {
                return $subSerie;
            }
        }

        return null;
    }

    public static function normalizeNested(array $payload): array
    {
        $subSections = collect($payload['sub_sections'] ?? [])
            ->filter(fn ($row) => filled($row['sub_section_code'] ?? null) && filled($row['sub_section_name'] ?? null))
            ->map(fn ($row) => [
                'sub_section_id' => $row['sub_section_id'] ?? (string) Str::uuid(),
                'sub_section_code' => trim($row['sub_section_code']),
                'sub_section_name' => trim($row['sub_section_name']),
            ])
            ->values()
            ->all();

        $series = collect($payload['series'] ?? [])
            ->filter(fn ($row) => filled($row['serie_code'] ?? null) && filled($row['serie_name'] ?? null))
            ->map(fn ($row) => [
                'serie_id' => $row['serie_id'] ?? (string) Str::uuid(),
                'serie_code' => trim($row['serie_code']),
                'serie_name' => trim($row['serie_name']),
                'retencion_gestion' => (int) ($row['retencion_gestion'] ?? $row['retention_management_years'] ?? 0),
                'retencion_central' => (int) ($row['retencion_central'] ?? $row['retention_central_years'] ?? 0),
                'disposicion_final' => $row['disposicion_final'] ?? $row['final_disposition'] ?? 'CT',
            ])
            ->values()
            ->all();

        $subSeries = collect($payload['sub_series'] ?? [])
            ->filter(fn ($row) => filled($row['sub_serie_code'] ?? null) && filled($row['sub_serie_name'] ?? null))
            ->map(fn ($row) => [
                'sub_serie_id' => $row['sub_serie_id'] ?? (string) Str::uuid(),
                'sub_serie_code' => trim($row['sub_serie_code']),
                'sub_serie_name' => trim($row['sub_serie_name']),
            ])
            ->values()
            ->all();

        return [
            'sub_sections' => $subSections,
            'series' => $series,
            'sub_series' => $subSeries,
        ];
    }

    public function dependencyName(string $dependency): string
    {
        if ($dependency === '' || $dependency === 'seccion') {
            return $this->section_name;
        }

        foreach ($this->sub_sections ?? [] as $row) {
            if (($row['sub_section_id'] ?? '') === $dependency) {
                return (string) ($row['sub_section_name'] ?? $this->section_name);
            }
        }

        return $this->section_name;
    }
}
