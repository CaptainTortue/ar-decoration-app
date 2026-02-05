<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectObject extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'furniture_object_id',
        'position_x',
        'position_y',
        'position_z',
        'rotation_x',
        'rotation_y',
        'rotation_z',
        'scale_x',
        'scale_y',
        'scale_z',
        'color',
        'material',
        'is_locked',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'position_x' => 'decimal:4',
            'position_y' => 'decimal:4',
            'position_z' => 'decimal:4',
            'rotation_x' => 'decimal:4',
            'rotation_y' => 'decimal:4',
            'rotation_z' => 'decimal:4',
            'scale_x' => 'decimal:3',
            'scale_y' => 'decimal:3',
            'scale_z' => 'decimal:3',
            'is_locked' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function furnitureObject(): BelongsTo
    {
        return $this->belongsTo(FurnitureObject::class);
    }
}
