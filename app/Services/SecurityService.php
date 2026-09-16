<?php

namespace App\Services;

use App\Jobs\ImportTrdJob;
use App\Models\TrdImport;
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
            'is_deleted' => false,
        ]);

        ImportTrdJob::dispatch(
            (string) $import->getKey(),
            $file->get(),
            $file->getClientOriginalName(),
        );

        return $import;
    }

    public function imports(): Collection
    {
        return $this->repository->imports();
    }
}
