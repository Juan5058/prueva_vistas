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

    public function downloadInventory(array $filters = []): Response
    {
        $metrics = $this->metrics();
        $selected = (array) ($filters['reports'] ?? ['totales']);
        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        $lines = [];
        $lines[] = 'EMPRESA: COTRANSHUILA S.A.';
        $lines[] = 'SISTEMA: Gestión Documental TRD y Archivo Oficial';
        $lines[] = 'FECHA GENERACIÓN: '.now()->format('d/m/Y H:i');

        if ($startDate || $endDate) {
            $lines[] = 'PERÍODO CONSULTADO: '.($startDate ?: 'Inicio').' al '.($endDate ?: 'Fecha actual');
        } else {
            $lines[] = 'PERÍODO CONSULTADO: Historico completo';
        }

        $lines[] = 'REPORTES INCLUIDOS: '.implode(', ', array_map('strtoupper', $selected));
        $lines[] = '---';

        if (in_array('totales', $selected) || empty($selected)) {
            $lines[] = '[SECCION] CONSOLIDADO GENERAL (KPIS)';
            $lines[] = 'Expedientes Totales: '.$metrics['kpis']['totalProceedings'];
            $lines[] = 'Documentos Totales: '.$metrics['kpis']['totalDocuments'];
            $lines[] = 'Documentos Electrónicos: '.$metrics['kpis']['totalElectronicDocuments'];
            $lines[] = 'Documentos Físicos: '.$metrics['kpis']['totalPhysicalDocuments'];
            $lines[] = 'Secciones TRD Registradas: '.$metrics['kpis']['totalTrdSections'];
            $lines[] = 'Series Documentales: '.$metrics['kpis']['totalSeries'];
            $lines[] = 'Subseries Documentales: '.$metrics['kpis']['totalSubSeries'];
            $lines[] = 'Usuarios Activos: '.$metrics['kpis']['totalActiveUsers'];
            $lines[] = 'Almacenamiento Total (bytes): '.$metrics['kpis']['totalStorageBytes'];
            $lines[] = '';
        }

        if (in_array('trd', $selected)) {
            $lines[] = '[SECCION] ESTRUCTURAS TRD';
            $lines[] = 'Total Secciones TRD Registradas: '.$metrics['kpis']['totalTrdSections'];
            $lines[] = 'Total Series Documentales: '.$metrics['kpis']['totalSeries'];
            $lines[] = 'Total Subseries Documentales: '.$metrics['kpis']['totalSubSeries'];
            $lines[] = 'Disposición Final TRD:';
            foreach ($metrics['distribution']['byDisposition'] as $row) {
                $lines[] = '  • '.$row['label'].': '.$row['count'];
            }
            $lines[] = '';
        }

        if (in_array('expedientes', $selected)) {
            $lines[] = '[SECCION] EXPEDIENTES DOCUMENTALES';
            $lines[] = 'Total Expedientes: '.$metrics['kpis']['totalProceedings'];
            $lines[] = 'Estado de Expedientes:';
            foreach ($metrics['distribution']['byState'] as $row) {
                $lines[] = '  • '.$row['label'].': '.$row['count'];
            }
            $lines[] = '';
        }

        if (in_array('documentos', $selected)) {
            $lines[] = '[SECCION] DOCUMENTOS REGISTRADOS';
            $lines[] = 'Total Documentos Registrados: '.$metrics['kpis']['totalDocuments'];
            $lines[] = 'Distribución por Soporte Documental:';
            foreach ($metrics['distribution']['bySupport'] as $row) {
                $lines[] = '  • '.$row['label'].': '.$row['count'].' ('.$row['percentage'].'%)';
            }
            $lines[] = '';
        }

        if (in_array('usuarios', $selected)) {
            $lines[] = '[SECCION] USUARIOS ACTIVOS Y SEGURIDAD';
            $lines[] = 'Usuarios Activos en Plataforma: '.$metrics['kpis']['totalActiveUsers'];
            $lines[] = 'Alertas de Bloqueo Registradas: '.$metrics['kpis']['totalBlockedAlerts'];
            $lines[] = '';
        }

        $binary = SimplePdf::fromLines('COTRANSHUILA S.A. - Reporte de Inventario TRD', $lines);

        return response($binary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="cotranshuila-reporte-trd.pdf"',
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
