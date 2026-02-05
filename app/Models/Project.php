<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'thumbnail_path',
        'status',
        'scene_settings',
    ];

    protected function casts(): array
    {
        return [
            'scene_settings' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): HasOne
    {
        return $this->hasOne(Room::class);
    }

    public function projectObjects(): HasMany
    {
        return $this->hasMany(ProjectObject::class);
    }
}
