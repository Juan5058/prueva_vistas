<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use App\Models\Concerns\LogicalSoftDeletes;
use MongoDB\Laravel\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasPublicKey, LogicalSoftDeletes;

    protected $collection = 'user_login_logs';

    protected $fillable = [
        'user_id',
        'user_email',
        'ip_address',
        'user_agent',
        'status',
        'reason',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
