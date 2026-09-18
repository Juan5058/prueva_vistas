<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use App\Models\Concerns\LogicalSoftDeletes;
use App\Support\RoleCatalog;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPublicKey, LogicalSoftDeletes, Notifiable;

    protected $collection = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'roles',
        'permissions',
        'current_ip',
        'allowed_ip_range',
        'last_login_at',
        'is_active',
        'force_logout',
        'is_deleted',
        'deleted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
            'permissions' => 'array',
            'is_active' => 'boolean',
            'force_logout' => 'boolean',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function loginLogs()
    {
        return $this->hasMany(UserLoginLog::class);
    }

    public function isAdmin(): bool
    {
        $roles = $this->roles ?? [];

        return in_array('super_admin', $roles, true)
            || in_array('admin', $roles, true)
            || in_array($this->role, ['super_admin', 'admin'], true);
    }

    public function isSuperAdmin(): bool
    {
        $roles = $this->roles ?? [];

        return in_array(RoleCatalog::SUPER_ADMIN, $roles, true)
            || $this->role === RoleCatalog::SUPER_ADMIN;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($permission, $this->permissions ?? [], true);
    }

    public function roleLabel(): string
    {
        return RoleCatalog::label($this->role);
    }
}
