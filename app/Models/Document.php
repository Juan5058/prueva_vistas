<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use App\Models\Concerns\LogicalSoftDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Document extends Model
{
    use HasPublicKey, LogicalSoftDeletes;

    protected $collection = 'documents';

    protected $fillable = [
        'proceedings_id',
        'sub_proceeding_id',
        'document_type',
        'name',
        'description',
        'document_creation_date',
        'support',
        'file_path',
        'file_metadata',
        'state',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'file_metadata' => 'array',
            'document_creation_date' => 'datetime',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function proceeding()
    {
        return $this->belongsTo(Proceeding::class, 'proceedings_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(DocumentAuditLog::class, 'document_id');
    }
}
