<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'width',
        'length',
        'height',
        'floor_material',
        'floor_color',
        'wall_material',
        'wall_color',
        'lighting_settings',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'decimal:2',
            'length' => 'decimal:2',
            'height' => 'decimal:2',
            'lighting_settings' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
