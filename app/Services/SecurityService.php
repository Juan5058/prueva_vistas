<?php

namespace App\Services;

use App\Jobs\ImportTrdJob;
use App\Models\ArchiveDocument;
use App\Models\Proceeding;
use App\Models\TrdImport;
use App\Models\TrdStructure;
use App\Models\User;
use App\Repositories\SecurityRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class SecurityService
{
    public function __construct(private SecurityRepository $repository) {}

    public function logs(): Collection
    {
        return $this->repository->logs();
    }

    public function sessions(): Collection
    {
        return $this->repository->activeUsers();
    }

    public function recordLogin(array $data): void
    {
        $this->repository->recordLoginLog($data);
    }

    public function queueImport(string $userId, UploadedFile $file): TrdImport
    {
        $import = $this->repository->createImport([
            'user_id' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'status' => 'PENDING',
            'total_rows' => 0,
            'processed_rows' => 0,
            'error_log' => [],
        ]);

        ImportTrdJob::dispatch((string) $import->getKey(), $file->get());

        return $import;
    }

    public function imports(): Collection
    {
        return $this->repository->imports();
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $documents = ArchiveDocument::query()->get();
        $structures = TrdStructure::query()->get();
        $totalSeries = $structures->sum(fn (TrdStructure $trd) => count($trd->series ?? []));
        $totalSubSeries = $structures->sum(fn (TrdStructure $trd) => count($trd->sub_series ?? []));
        $storageBytes = $documents->sum(fn (ArchiveDocument $doc) => (int) data_get($doc->file_metadata, 'size_bytes', 0));
        $bySupport = $documents->groupBy('support')->map->count();
        $byState = Proceeding::query()->get()->groupBy('state')->map->count();
        $byDisposition = $structures
            ->flatMap(fn (TrdStructure $trd) => collect($trd->series ?? [])->pluck('disposicion_final'))
            ->filter()
            ->countBy();
        $totalDocs = max($documents->count(), 1);

        return [
            'kpis' => [
                'totalProceedings' => Proceeding::count(),
                'totalDocuments' => $documents->count(),
                'totalPhysicalDocuments' => $documents->where('support', 'Físico')->count(),
                'totalElectronicDocuments' => $documents->where('support', 'Electrónico')->count(),
                'totalTrdSections' => $structures->count(),
                'totalSeries' => $totalSeries,
                'totalSubSeries' => $totalSubSeries,
                'totalActiveUsers' => User::query()->where('is_active', true)->count(),
                'totalBlockedAlerts' => $this->repository->blockedCount(),
                'totalStorageBytes' => $storageBytes,
            ],
            'distribution' => [
                'bySupport' => $bySupport->map(fn ($count, $name) => [
                    'name' => $name,
                    'count' => $count,
                    'percentage' => round(($count / $totalDocs) * 100, 1),
                ])->values(),
                'byState' => $byState->map(fn ($count, $state) => [
                    'state' => $state,
                    'count' => $count,
                ])->values(),
                'byDisposition' => $byDisposition->map(fn ($count, $type) => [
                    'type' => $type,
                    'count' => $count,
                ])->values(),
            ],
            'recentAlerts' => $this->repository->recentBlocked(),
        ];
    }
}
