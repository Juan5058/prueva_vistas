<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use MongoDB\Laravel\Eloquent\Model;

class UserLoginLog extends Model
{
    use HasPublicKey;

    protected $collection = 'user_login_logs';

    protected $fillable = [
        'user_id',
        'user_email',
        'ip_address',
        'user_agent',
        'status',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
