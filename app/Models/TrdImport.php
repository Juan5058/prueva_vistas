<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use App\Models\Concerns\LogicalSoftDeletes;
use MongoDB\Laravel\Eloquent\Model;

class TrdImport extends Model
{
    use HasPublicKey, LogicalSoftDeletes;

    protected $collection = 'trd_imports';

    protected $fillable = [
        'user_id',
        'file_name',
        'status',
        'total_rows',
        'processed_rows',
        'error_log',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'error_log' => 'array',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
