<?php

namespace App\Models;

use App\Models\Concerns\HasPublicKey;
use App\Models\Concerns\LogicalSoftDeletes;
use MongoDB\Laravel\Eloquent\Model;

class Proceeding extends Model
{
    use HasPublicKey, LogicalSoftDeletes;

    protected $collection = 'proceedings';

    protected $fillable = [
        'trd_structure_id',
        'section_code',
        'serie_id',
        'sub_serie_id',
        'serie_name',
        'sub_serie_name',
        'file_number',
        'name',
        'description',
        'opening_date',
        'deadline',
        'state',
        'physical_location',
        'sub_proceedings',
        'is_deleted',
        'deleted_at',
    ];

    protected function casts(): array
    {
        return [
            'physical_location' => 'array',
            'sub_proceedings' => 'array',
            'opening_date' => 'datetime',
            'deadline' => 'datetime',
            'is_deleted' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    public function trdStructure()
    {
        return $this->belongsTo(TrdStructure::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'proceedings_id');
    }
}
