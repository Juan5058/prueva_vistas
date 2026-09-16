<?php

namespace App\Http\Controllers;

use App\Models\ArchiveDocument;
use App\Models\Proceeding;
use App\Models\TrdStructure;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $documents = ArchiveDocument::query()->get();
        $structures = TrdStructure::query()->get();
        $totalSeries = $structures->sum(fn (TrdStructure $trd) => count($trd->series ?? []));
        $totalSubSeries = $structures->sum(fn (TrdStructure $trd) => count($trd->sub_series ?? []));
        $storageBytes = $documents->sum(fn (ArchiveDocument $doc) => (int) data_get($doc->file_metadata, 'size_bytes', 0));

        $bySupport = $documents->groupBy('support')->map->count();
        $byState = Proceeding::query()->get()->groupBy('state')->map->count();
        $byDisposition = $structures
            ->flatMap(fn (TrdStructure $trd) => collect($trd->sub_series ?? [])->pluck('final_disposition'))
            ->filter()
            ->countBy();

        $totalDocs = max($documents->count(), 1);

        return view('dashboard', [
            'kpis' => [
                'totalProceedings' => Proceeding::count(),
                'totalDocuments' => $documents->count(),
                'totalPhysicalDocuments' => $documents->where('support', 'Físico')->count(),
                'totalElectronicDocuments' => $documents->where('support', 'Electrónico')->count(),
                'totalTrdSections' => $structures->count(),
                'totalSeries' => $totalSeries,
                'totalSubSeries' => $totalSubSeries,
                'totalActiveUsers' => User::query()->where('is_active', true)->count(),
                'totalBlockedAlerts' => UserLoginLog::query()->where('status', 'BLOCKED')->count(),
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
            'recentAlerts' => UserLoginLog::query()->where('status', 'BLOCKED')->latest()->limit(6)->get(),
        ]);
    }
}
