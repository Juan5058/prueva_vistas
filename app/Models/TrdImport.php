<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use MongoDB\Laravel\Eloquent\Model;

class TrdImport extends Model
{
    use HasPublicKey;

    protected $collection = 'trd_imports';

    protected $fillable = [
        'user_id',
        'file_name',
        'status',
        'total_rows',
        'processed_rows',
        'error_log',
    ];

    protected function casts(): array
    {
        return [
            'error_log' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
