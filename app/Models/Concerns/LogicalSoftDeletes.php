<?php

namespace App\Models\Concerns;

use App\Models\Scopes\NotDeletedScope;
use RuntimeException;

trait LogicalSoftDeletes
{
    public static function bootLogicalSoftDeletes(): void
    {
        static::addGlobalScope(new NotDeletedScope);

        static::creating(function ($model) {
            if ($model->is_deleted === null) {
                $model->is_deleted = false;
            }
            if ($model->deleted_at === null) {
                $model->deleted_at = null;
            }
        });
    }

    public function initializeLogicalSoftDeletes(): void
    {
        $this->casts['is_deleted'] = 'boolean';
        $this->casts['deleted_at'] = 'datetime';
    }

    public function delete(): never
    {
        throw new RuntimeException('CRITICAL_POLICY_VIOLATION: delete() físico está prohibido. Use softDelete() con is_deleted: true.');
    }

    public function forceDelete(): never
    {
        throw new RuntimeException('CRITICAL_POLICY_VIOLATION: forceDelete() está prohibido.');
    }

    public function softDelete(): bool
    {
        $this->is_deleted = true;
        $this->deleted_at = now();

        return $this->save();
    }

    public function restoreLogical(): bool
    {
        $this->is_deleted = false;
        $this->deleted_at = null;

        return $this->save();
    }

    public function trashed(): bool
    {
        return (bool) $this->is_deleted;
    }

    public function scopeWithTrashed($query)
    {
        return $query->withoutGlobalScope(NotDeletedScope::class);
    }

    public function scopeOnlyTrashed($query)
    {
        return $query->withoutGlobalScope(NotDeletedScope::class)->where('is_deleted', true);
    }
}
