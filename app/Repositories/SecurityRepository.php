<?php

namespace App\Repositories;

use App\Models\DocumentAuditLog;
use App\Models\TrdImport;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Support\Collection;

class SecurityRepository
{
    public function recordLoginLog(array $data): UserLoginLog
    {
        return UserLoginLog::query()->create($data);
    }

    public function logs(int $limit = 200): Collection
    {
        return UserLoginLog::query()->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function blockedCount(): int
    {
        return UserLoginLog::query()->where('status', 'BLOCKED')->count();
    }

    public function recentBlocked(int $limit = 6): Collection
    {
        return UserLoginLog::query()->where('status', 'BLOCKED')->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function activeUsers(): Collection
    {
        return User::query()
            ->where('is_active', true)
            ->orderBy('last_login_at', 'desc')
            ->get();
    }

    public function recordDocumentAudit(array $data): DocumentAuditLog
    {
        return DocumentAuditLog::query()->create($data);
    }

    public function createImport(array $data): TrdImport
    {
        return TrdImport::query()->create($data);
    }

    public function imports(int $limit = 20): Collection
    {
        return TrdImport::query()->orderBy('created_at', 'desc')->limit($limit)->get();
    }
}
