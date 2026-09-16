<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Proceeding;
use App\Models\TrdStructure;
use App\Models\User;
use App\Repositories\SecurityRepository;
use App\Support\RoleCatalog;
use App\Support\SimplePdf;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class ReportService
{
    public function __construct(private SecurityRepository $security) {}

    /**
     * @return array{kpis: array<string, int|float>, distribution: array<string, mixed>}
     */
    public function metrics(): array
    {
        $documents = Document::query()->get();
        $structures = TrdStructure::query()->get();
        $proceedings = Proceeding::query()->get();
        $totalSeries = $structures->sum(fn (TrdStructure $trd) => count($trd->series ?? []));
        $totalSubSeries = $structures->sum(fn (TrdStructure $trd) => count($trd->sub_series ?? []));
        $storageBytes = $documents->sum(fn (Document $doc) => (int) data_get($doc->file_metadata, 'size_bytes', 0));
        $bySupport = $documents->groupBy('support')->map->count();
        $byState = $proceedings->groupBy('state')->map->count();
        $byDisposition = $structures
            ->flatMap(fn (TrdStructure $trd) => collect($trd->series ?? [])->pluck('disposicion_final'))
            ->filter()
            ->countBy();

        return [
            'kpis' => [
                'totalProceedings' => $proceedings->count(),
                'totalDocuments' => $documents->count(),
                'totalPhysicalDocuments' => $documents->where('support', 'Físico')->count(),
                'totalElectronicDocuments' => $documents->where('support', 'Electrónico')->count(),
                'totalTrdSections' => $structures->count(),
                'totalSeries' => $totalSeries,
                'totalSubSeries' => $totalSubSeries,
                'totalActiveUsers' => User::query()->where('is_active', true)->count(),
                'totalBlockedAlerts' => $this->security->blockedCount(),
                'totalStorageBytes' => $storageBytes,
            ],
            'distribution' => [
                'bySupport' => $this->chartItems($bySupport, fn ($name) => (string) $name),
                'byState' => $this->chartItems($byState, fn ($state) => (string) $state),
                'byDisposition' => $this->chartItems($byDisposition, fn ($type) => RoleCatalog::dispositionLabels()[$type] ?? (string) $type),
            ],
        ];
    }

    public function downloadInventory(): Response
    {
        $metrics = $this->metrics();
        $lines = [
            'Generado: '.now()->format('d/m/Y H:i'),
            '',
            'KPIs',
            'Expedientes: '.$metrics['kpis']['totalProceedings'],
            'Documentos: '.$metrics['kpis']['totalDocuments'],
            'Electronicos: '.$metrics['kpis']['totalElectronicDocuments'],
            'Fisicos: '.$metrics['kpis']['totalPhysicalDocuments'],
            'Secciones TRD: '.$metrics['kpis']['totalTrdSections'],
            'Series: '.$metrics['kpis']['totalSeries'],
            'Subseries: '.$metrics['kpis']['totalSubSeries'],
            'Usuarios activos: '.$metrics['kpis']['totalActiveUsers'],
            'Almacenamiento (bytes): '.$metrics['kpis']['totalStorageBytes'],
            '',
            'Soporte documental',
        ];

        foreach ($metrics['distribution']['bySupport'] as $row) {
            $lines[] = $row['label'].': '.$row['count'].' ('.$row['percentage'].'%)';
        }

        $lines[] = '';
        $lines[] = 'Estado de expedientes';

        foreach ($metrics['distribution']['byState'] as $row) {
            $lines[] = $row['label'].': '.$row['count'];
        }

        $lines[] = '';
        $lines[] = 'Disposicion final TRD';

        foreach ($metrics['distribution']['byDisposition'] as $row) {
            $lines[] = $row['label'].': '.$row['count'];
        }

        $binary = SimplePdf::fromLines('Inventario documental TRD', $lines);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="inventario-trd.pdf"',
        ]);
    }

    /**
     * @param  Collection<string, int>  $counts
     * @return list<array{label: string, count: int, percentage: float, bar: float, color: string}>
     */
    private function chartItems($counts, callable $label): array
    {
        $palette = ['#2563eb', '#059669', '#d97706', '#e11d48', '#7c3aed', '#0f766e'];
        $total = max($counts->sum(), 1);
        $max = max($counts->max() ?: 1, 1);
        $index = 0;

        return $counts->map(function ($count, $key) use (&$index, $palette, $total, $max, $label) {
            $item = [
                'label' => $label($key),
                'count' => (int) $count,
                'percentage' => round(((int) $count / $total) * 100, 1),
                'bar' => round(((int) $count / $max) * 100, 1),
                'color' => $palette[$index % count($palette)],
            ];
            $index++;

            return $item;
        })->values()->all();
    }
}
