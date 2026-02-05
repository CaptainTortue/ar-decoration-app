<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FurnitureObject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category_id',
        'model_path',
        'thumbnail_path',
        'width',
        'height',
        'depth',
        'default_scale',
        'available_colors',
        'available_materials',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'decimal:3',
            'height' => 'decimal:3',
            'depth' => 'decimal:3',
            'default_scale' => 'decimal:3',
            'available_colors' => 'array',
            'available_materials' => 'array',
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function projectObjects(): HasMany
    {
        return $this->hasMany(ProjectObject::class);
    }
}
