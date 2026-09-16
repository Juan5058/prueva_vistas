<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use App\Models\Concerns\LogicalSoftDeletes;
use MongoDB\Laravel\Eloquent\Model;

class DocumentAuditLog extends Model
{
    use HasPublicKey, LogicalSoftDeletes;

    protected $collection = 'document_audit_logs';

    protected $fillable = [
        'user_id',
        'document_id',
        'action',
        'ip_address',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'VIEW' => 'Consulta',
            'DOWNLOAD' => 'Descarga',
            'UPDATE' => 'Actualización',
            default => (string) $this->action,
        };
    }

    public function actionBadgeClass(): string
    {
        return match ($this->action) {
            'VIEW' => 'bg-sky-50 text-sky-800 border-sky-200',
            'DOWNLOAD' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'UPDATE' => 'bg-amber-50 text-amber-800 border-amber-200',
            default => 'bg-slate-50 text-slate-700 border-slate-200',
        };
    }
}
